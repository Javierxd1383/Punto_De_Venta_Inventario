<?php
session_start();
include 'conexion.php'; // Archivo de conexión a la base de datos

// Verificar si el usuario tiene el rol de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php"); // Redirige al login si no es administrador
    exit();
}

// Variables para mensajes
$success = null;
$error = null;

// Manejo de operaciones (Agregar, Editar, Eliminar)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'];
    if ($accion === 'agregar') {
        $nombre = $_POST['nombre'];
        $usuario = $_POST['usuario'];
        $telefono = $_POST['telefono'];
        $contrasena = $_POST['contrasena'];
        $activo = isset($_POST['activo']) ? 1 : 0;

        $query = "INSERT INTO empleados (nombre, usuario, telefono, contrasena, activo) 
                  VALUES ('$nombre', '$usuario', '$telefono', '$contrasena', $activo)";
        if (mysqli_query($conn, $query)) {
            $success = "Empleado agregado correctamente.";
        } else {
            $error = "Error al agregar empleado: " . mysqli_error($conn);
        }
    } elseif ($accion === 'editar') {
        $id_empleado = $_POST['id_empleado'];
        $nombre = $_POST['nombre'];
        $usuario = $_POST['usuario'];
        $telefono = $_POST['telefono'];
        $contrasena = $_POST['contrasena'];
        $activo = isset($_POST['activo']) ? 1 : 0;

        $query = "UPDATE empleados 
                  SET nombre = '$nombre', usuario = '$usuario', telefono = '$telefono', contrasena = '$contrasena', activo = $activo 
                  WHERE id_empleado = $id_empleado";
        if (mysqli_query($conn, $query)) {
            $success = "Empleado actualizado correctamente.";
        } else {
            $error = "Error al actualizar empleado: " . mysqli_error($conn);
        }
    } elseif ($accion === 'eliminar') {
        $id_empleado = $_POST['id_empleado'];
        $query = "DELETE FROM empleados WHERE id_empleado = $id_empleado";
        if (mysqli_query($conn, $query)) {
            $success = "Empleado eliminado correctamente.";
        } else {
            $error = "Error al eliminar empleado: " . mysqli_error($conn);
        }
    }
}

// Obtener todos los empleados
$query = "SELECT * FROM empleados";
$result = mysqli_query($conn, $query);
$empleados = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center text-primary">Gestión de Empleados</h1>

        <!-- Mensajes de éxito o error -->
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Botón para regresar al menú anterior -->
        <div class="d-flex justify-content-end mb-4">
            <a href="gestion_usuarios.php" class="btn btn-secondary">Volver al Menú Anterior</a>
        </div>

        <!-- Tabla de empleados -->
        <table class="table table-striped table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Teléfono</th>
                    <th>Activo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empleados as $empleado): ?>
                    <tr>
                        <td><?php echo $empleado['id_empleado']; ?></td>
                        <td><?php echo $empleado['nombre']; ?></td>
                        <td><?php echo $empleado['usuario']; ?></td>
                        <td><?php echo $empleado['telefono']; ?></td>
                        <td><?php echo $empleado['activo'] ? 'Sí' : 'No'; ?></td>
                        <td>
                            <!-- Botón para editar -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarEmpleadoModal" 
                                onclick="cargarDatosEditar(<?php echo htmlspecialchars(json_encode($empleado)); ?>)">Editar</button>

                            <!-- Botón para eliminar -->
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id_empleado" value="<?php echo $empleado['id_empleado']; ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este empleado?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Botón para agregar empleado -->
        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#agregarEmpleadoModal">Agregar Empleado</button>
    </div>

    <!-- Modal para agregar empleado -->
    <div class="modal fade" id="agregarEmpleadoModal" tabindex="-1" aria-labelledby="agregarEmpleadoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarEmpleadoModalLabel">Agregar Empleado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="agregar">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" name="usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" required>
                        </div>
                        <div class="mb-3">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="contrasena" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Activo</label>
                            <input type="checkbox" name="activo" value="1"> Sí
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

    <!-- Modal para editar empleado -->
    <div class="modal fade" id="editarEmpleadoModal" tabindex="-1" aria-labelledby="editarEmpleadoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarEmpleadoModalLabel">Editar Empleado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id_empleado" id="editarIdEmpleado">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="editarNombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" name="usuario" id="editarUsuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" id="editarTelefono" required>
                        </div>
                        <div class="mb-3">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="contrasena" id="editarContrasena" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Activo</label>
                            <input type="checkbox" name="activo" id="editarActivo" value="1"> Sí
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
        function cargarDatosEditar(empleado) {
            document.getElementById('editarIdEmpleado').value = empleado.id_empleado;
            document.getElementById('editarNombre').value = empleado.nombre;
            document.getElementById('editarUsuario').value = empleado.usuario;
            document.getElementById('editarTelefono').value = empleado.telefono;
            document.getElementById('editarContrasena').value = empleado.contrasena;
            document.getElementById('editarActivo').checked = empleado.activo === 1;
        }
    </script>
</body>
</html>
