<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo_respaldo'])) {
    $archivo = $_FILES['archivo_respaldo']['tmp_name'];

    if (is_uploaded_file($archivo)) {
        $comando = "mysql -u root -p [nombre_base_datos] < $archivo";
        system($comando);
        $mensaje = "Base de datos restaurada con éxito.";
    } else {
        $mensaje = "Error al subir el archivo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurar Base de Datos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center text-primary">Restaurar Base de Datos</h1>
        <form method="POST" enctype="multipart/form-data" class="mt-4">
            <div class="mb-3">
                <label for="archivo_respaldo" class="form-label">Subir archivo de respaldo (.sql):</label>
                <input type="file" name="archivo_respaldo" id="archivo_respaldo" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Restaurar</button>
        </form>
        <?php if (isset($mensaje)): ?>
            <div class="alert alert-success mt-3"><?php echo $mensaje; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
