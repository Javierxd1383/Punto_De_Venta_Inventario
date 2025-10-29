<?php
session_start();
include 'conexion.php'; // Archivo de conexión a la base de datos

// Verificar si el usuario tiene el rol de empleado
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php"); // Redirige al login si no es empleado
    exit();
}

// Obtener los datos de configuración desde la base de datos
$query_config = "SELECT clave, valor FROM configuraciones";
$result_config = mysqli_query($conn, $query_config);

$configuraciones = [];
while ($row = mysqli_fetch_assoc($result_config)) {
    $configuraciones[$row['clave']] = $row['valor'];
}

$nombre_dulceria = $configuraciones['nombre_dulceria'] ?? 'No definido';
$direccion = $configuraciones['direccion'] ?? 'No definida';
$telefono = $configuraciones['telefono'] ?? 'No definido';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Guardar los cambios en las configuraciones
    foreach ($_POST['config'] as $clave => $valor) {
        $valor_limpio = mysqli_real_escape_string($conn, $valor);
        $clave_limpia = mysqli_real_escape_string($conn, $clave);

        $update_query = "UPDATE configuraciones SET valor = '$valor_limpio' WHERE clave = '$clave_limpia'";
        mysqli_query($conn, $update_query);
    }
    // Redirigir para evitar reenvío de formularios
    header("Location: configuracion.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #8e44ad, #3498db);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            color: #fff;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            margin: auto;
            color: #333;
        }
        .form-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-primary mb-4">Configuración de la Dulcería</h1>

        <form method="POST" action="configuracion.php">
            <div class="mb-3">
                <label for="nombre_dulceria" class="form-label">Nombre de la Dulcería:</label>
                <input type="text" id="nombre_dulceria" name="config[nombre_dulceria]" value="<?php echo htmlspecialchars($nombre_dulceria); ?>" class="form-control">
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección:</label>
                <input type="text" id="direccion" name="config[direccion]" value="<?php echo htmlspecialchars($direccion); ?>" class="form-control">
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="text" id="telefono" name="config[telefono]" value="<?php echo htmlspecialchars($telefono); ?>" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary w-100">Guardar Cambios</button>
        </form>

        <div class="mt-3 text-center">
            <a href="herramientas.php" class="btn btn-secondary">Regresar a Herramientas</a>
        </div>
    </div>
</body>
</html>
