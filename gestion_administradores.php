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
        $email = $_POST['email'];
        $contrasena = $_POST['contrasena'];

        $query = "INSERT INTO administradores (nombre, usuario, email, contrasena) 
                  VALUES ('$nombre', '$usuario', '$email', '$contrasena')";
        if (mysqli_query($conn, $query)) {
            $success = "Administrador agregado correctamente.";
        } else {
            $error = "Error al agregar administrador: " . mysqli_error($conn);
        }
    } elseif ($accion === 'editar') {
        $id_admin = $_POST['id_admin'];
        $nombre = $_POST['nombre'];
        $usuario = $_POST['usuario'];
        $email = $_POST['email'];
        $contrasena = $_POST['contrasena'];

        $query = "UPDATE administradores 
                  SET nombre = '$nombre', usuario = '$usuario', email = '$email', contrasena = '$contrasena' 
                  WHERE id_admin = $id_admin";
        if (mysqli_query($conn, $query)) {
            $success = "Administrador actualizado correctamente.";
        } else {
            $error = "Error al actualizar administrador: " . mysqli_error($conn);
        }
    } elseif ($accion === 'eliminar') {
        $id_admin = $_POST['id_admin'];
        $query = "DELETE FROM administradores WHERE id_admin = $id_admin";
        if (mysqli_query($conn, $query)) {
            $success = "Administrador eliminado correctamente.";
        } else {
            $error = "Error al eliminar administrador: " . mysqli_error($conn);
        }
    }
}

// Obtener todos los administradores
$query = "SELECT * FROM administradores";
$result = mysqli_query($conn, $query);
$administradores = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Administradores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center text-primary">Gestión de Administradores</h1>

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

        <!-- Tabla de administradores -->
        <table class="table table-striped table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($administradores as $admin): ?>
                    <tr>
                        <td><?php echo $admin['id_admin']; ?></td>
                        <td><?php echo $admin['nombre']; ?></td>
                        <td><?php echo $admin['usuario']; ?></td>
                        <td><?php echo $admin['email']; ?></td>
                        <td>
                            <!-- Botón para editar -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarAdministradorModal" 
                                onclick="cargarDatosEditar(<?php echo htmlspecialchars(json_encode($admin)); ?>)">Editar</button>

                            <!-- Botón para eliminar -->
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id_admin" value="<?php echo $admin['id_admin']; ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este administrador?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Botón para agregar administrador -->
        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#agregarAdministradorModal">Agregar Administrador</button>
    </div>

    <!-- Modal para agregar administrador -->
    <div class="modal fade" id="agregarAdministradorModal" tabindex="-1" aria-labelledby="agregarAdministradorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarAdministradorModalLabel">Agregar Administrador</h5>
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
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="contrasena" required>
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

    <!-- Modal para editar administrador -->
    <div class="modal fade" id="editarAdministradorModal" tabindex="-1" aria-labelledby="editarAdministradorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarAdministradorModalLabel">Editar Administrador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id_admin" id="editarIdAdmin">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="editarNombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" name="usuario" id="editarUsuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="editarEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="contrasena" id="editarContrasena" required>
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
        function cargarDatosEditar(admin) {
            document.getElementById('editarIdAdmin').value = admin.id_admin;
            document.getElementById('editarNombre').value = admin.nombre;
            document.getElementById('editarUsuario').value = admin.usuario;
            document.getElementById('editarEmail').value = admin.email;
            document.getElementById('editarContrasena').value = admin.contrasena;
        }
    </script>
</body>
</html>
