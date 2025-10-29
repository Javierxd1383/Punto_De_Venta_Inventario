<?php
session_start();
include 'conexion.php'; // Archivo de conexión a la base de datos

// Verificar si el usuario tiene el rol de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php"); // Redirige al login si no es administrador
    exit();
}

// Inicializar variables para mensajes y datos
$success = ""; // Inicializamos como cadena vacía
$error = "";   // Inicializamos como cadena vacía
$configuraciones = []; // Inicializamos como un array vacío

// Manejo de operaciones (Agregar, Editar, Eliminar)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'] ?? ''; // Validar que la acción esté definida
    if ($accion === 'agregar') {
        $clave = $_POST['clave'] ?? '';
        $valor = $_POST['valor'] ?? '';

        if ($clave && $valor) {
            $query = "INSERT INTO configuraciones (clave, valor) VALUES ('$clave', '$valor')";
            if (mysqli_query($conn, $query)) {
                $success = "Configuración agregada correctamente.";
            } else {
                $error = "Error al agregar configuración: " . mysqli_error($conn);
            }
        } else {
            $error = "Por favor, complete todos los campos.";
        }
    } elseif ($accion === 'editar') {
        $id_configuracion = $_POST['id_configuracion'] ?? null;
        $clave = $_POST['clave'] ?? '';
        $valor = $_POST['valor'] ?? '';

        if ($id_configuracion && $clave && $valor) {
            $query = "UPDATE configuraciones SET clave = '$clave', valor = '$valor' WHERE id_configuracion = $id_configuracion";
            if (mysqli_query($conn, $query)) {
                $success = "Configuración actualizada correctamente.";
            } else {
                $error = "Error al actualizar configuración: " . mysqli_error($conn);
            }
        } else {
            $error = "Todos los campos son obligatorios para editar.";
        }
    } elseif ($accion === 'eliminar') {
        $id_configuracion = $_POST['id_configuracion'] ?? null;

        if ($id_configuracion) {
            $query = "DELETE FROM configuraciones WHERE id_configuracion = $id_configuracion";
            if (mysqli_query($conn, $query)) {
                $success = "Configuración eliminada correctamente.";
            } else {
                $error = "Error al eliminar configuración: " . mysqli_error($conn);
            }
        } else {
            $error = "ID de configuración inválido.";
        }
    }
}

// Obtener todas las configuraciones
$query = "SELECT * FROM configuraciones";
$result = mysqli_query($conn, $query);
if ($result) {
    $configuraciones = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $error = "Error al obtener configuraciones: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Configuraciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #a29bfe, #6c5ce7);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #333;
        }
        .container {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            padding: 20px 30px;
            max-width: 1200px;
            width: 100%;
        }
        h1 {
            color: #6c5ce7;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #6c5ce7;
            border: none;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #5a4dcf;
        }
        .btn-secondary {
            border-radius: 25px;
            background-color: #dfe6e9;
            color: #333;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #b2bec3;
        }
        .btn-warning, .btn-danger {
            border-radius: 25px;
        }
        .table {
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
        }
        .table th {
            background-color: #6c5ce7;
            color: white;
        }
        .table tbody tr:hover {
            background-color: #f4f4f4;
        }
        .modal-header {
            background: #6c5ce7;
            color: white;
        }
        .modal-footer .btn {
            border-radius: 25px;
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Configuraciones</h1>

        <!-- Mensajes de éxito o error -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Botón para regresar al menú anterior -->
        <div class="d-flex justify-content-end mb-4">
            <a href="administrador.php" class="btn btn-secondary">Volver al Menú Anterior</a>
        </div>

        <!-- Tabla de configuraciones -->
        <table class="table table-hover table-bordered text-center">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Clave</th>
                    <th>Valor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($configuraciones)): ?>
                    <?php foreach ($configuraciones as $config): ?>
                        <tr>
                            <td><?php echo $config['id_configuracion']; ?></td>
                            <td><?php echo $config['clave']; ?></td>
                            <td><?php echo $config['valor']; ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarConfiguracionModal" 
                                    onclick="cargarDatosEditar(<?php echo htmlspecialchars(json_encode($config)); ?>)">Editar</button>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id_configuracion" value="<?php echo $config['id_configuracion']; ?>">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta configuración?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No se encontraron configuraciones.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Botón para agregar configuración -->
        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#agregarConfiguracionModal">Agregar Configuración</button>
    </div>

    <!-- Modal para agregar configuración -->
    <div class="modal fade" id="agregarConfiguracionModal" tabindex="-1" aria-labelledby="agregarConfiguracionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarConfiguracionModalLabel">Agregar Configuración</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="agregar">
                        <div class="mb-3">
                            <label for="clave" class="form-label">Clave</label>
                            <input type="text" class="form-control" name="clave" required>
                        </div>
                        <div class="mb-3">
                            <label for="valor" class="form-label">Valor</label>
                            <input type="text" class="form-control" name="valor" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar configuración -->
    <div class="modal fade" id="editarConfiguracionModal" tabindex="-1" aria-labelledby="editarConfiguracionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarConfiguracionModalLabel">Editar Configuración</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id_configuracion" id="editarIdConfiguracion">
                        <div class="mb-3">
                            <label for="clave" class="form-label">Clave</label>
                            <input type="text" class="form-control" name="clave" id="editarClave" required>
                        </div>
                        <div class="mb-3">
                            <label for="valor" class="form-label">Valor</label>
                            <input type="text" class="form-control" name="valor" id="editarValor" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cargarDatosEditar(config) {
            document.getElementById('editarIdConfiguracion').value = config.id_configuracion;
            document.getElementById('editarClave').value = config.clave;
            document.getElementById('editarValor').value = config.valor;
        }
    </script>
</body>
</html>
