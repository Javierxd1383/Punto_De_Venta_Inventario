<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

$success = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'];
    if ($accion === 'agregar') {
        $nombre = $_POST['nombre'];
        $usuario = $_POST['usuario'];
        $telefono = $_POST['telefono'];
        $contrasena = $_POST['contrasena'];
        $activo = isset($_POST['activo']) ? 1 : 0;
        $query = "INSERT INTO empleados (nombre, usuario, telefono, contrasena, activo) VALUES ('$nombre', '$usuario', '$telefono', '$contrasena', $activo)";
        if (mysqli_query($conn, $query)) {
            $success = "Empleado agregado.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    } elseif ($accion === 'editar') {
        $id_empleado = $_POST['id_empleado'];
        $nombre = $_POST['nombre'];
        $usuario = $_POST['usuario'];
        $telefono = $_POST['telefono'];
        $contrasena = $_POST['contrasena'];
        $activo = isset($_POST['activo']) ? 1 : 0;
        $query = "UPDATE empleados SET nombre = '$nombre', usuario = '$usuario', telefono = '$telefono', contrasena = '$contrasena', activo = $activo WHERE id_empleado = $id_empleado";
        if (mysqli_query($conn, $query)) {
            $success = "Empleado actualizado.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    } elseif ($accion === 'eliminar') {
        $id_empleado = $_POST['id_empleado'];
        $query = "DELETE FROM empleados WHERE id_empleado = $id_empleado";
        if (mysqli_query($conn, $query)) {
            $success = "Empleado eliminado.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

$empleados = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM empleados"), MYSQLI_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <div>
        <h2 class="fw-bold mb-1">Empleados</h2>
        <p class="text-muted">Gestión de personal de ventas</p>
    </div>
    <div class="d-flex gap-2">
        <a href="gestion_usuarios.php" class="btn btn-light text-muted"><i class="fa fa-arrow-left me-2"></i> Volver</a>
        <button class="btn btn-primary-gradient" data-bs-toggle="modal" data-bs-target="#modalEmpleado">
            <i class="fa fa-user-plus me-2"></i> Nuevo Empleado
        </button>
    </div>
</div>

<?php if ($success): ?>
    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success fw-bold"><i
            class="fa fa-check me-2"></i><?= $success ?></div><?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger fw-bold"><i
            class="fa fa-times me-2"></i><?= $error ?></div><?php endif; ?>

<div class="card-premium fade-in">
    <div class="table-responsive">
        <table class="table-premium align-middle">
            <thead>
                <tr>
                    <th class="ps-4">Empleado</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empleados as $e): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 35px; height: 35px;">
                                    <?= strtoupper(substr($e['nombre'], 0, 1)) ?>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($e['nombre']) ?></h6>
                                    <small class="text-muted">@<?= htmlspecialchars($e['usuario']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted"><?= htmlspecialchars($e['telefono']) ?></td>
                        <td>
                            <?php if ($e['activo']): ?>
                                <span class="badge badge-success">Activo</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-light text-primary" onclick='editar(<?= json_encode($e) ?>)'
                                data-bs-toggle="modal" data-bs-target="#modalEditar">
                                <i class="fa fa-edit"></i>
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar empleado?');">
                                <input type="hidden" name="id_empleado" value="<?= $e['id_empleado'] ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <button class="btn btn-sm btn-light text-danger"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Agregar -->
<div class="modal fade" id="modalEmpleado" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold">Nuevo Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form method="POST">
                    <input type="hidden" name="accion" value="agregar">
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase mb-1">Nombre</label>
                        <input type="text" class="form-control form-control-lg bg-light border-0" name="nombre"
                            required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="fw-bold small text-muted text-uppercase mb-1">Usuario</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" name="usuario"
                                required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="fw-bold small text-muted text-uppercase mb-1">Teléfono</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" name="telefono"
                                required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase mb-1">Contraseña</label>
                        <input type="password" class="form-control form-control-lg bg-light border-0" name="contrasena"
                            required>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="activo" checked>
                        <label class="form-check-label text-muted">Cuenta activa</label>
                    </div>
                    <button type="submit" class="btn btn-primary-gradient w-100 py-3">Guardar Empleado</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold">Editar Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form method="POST">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id_empleado" id="edit_id">
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase mb-1">Nombre</label>
                        <input type="text" class="form-control form-control-lg bg-light border-0" name="nombre"
                            id="edit_nombre" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="fw-bold small text-muted text-uppercase mb-1">Usuario</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" name="usuario"
                                id="edit_usuario" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="fw-bold small text-muted text-uppercase mb-1">Teléfono</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" name="telefono"
                                id="edit_telefono" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase mb-1">Contraseña</label>
                        <input type="password" class="form-control form-control-lg bg-light border-0" name="contrasena"
                            id="edit_pass" required>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="activo" id="edit_active">
                        <label class="form-check-label text-muted">Cuenta activa</label>
                    </div>
                    <button type="submit" class="btn btn-primary-gradient w-100 py-3">Actualizar Empleado</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function editar(data) {
        document.getElementById('edit_id').value = data.id_empleado;
        document.getElementById('edit_nombre').value = data.nombre;
        document.getElementById('edit_usuario').value = data.usuario;
        document.getElementById('edit_telefono').value = data.telefono;
        document.getElementById('edit_pass').value = data.contrasena;
        document.getElementById('edit_active').checked = data.activo;
    }
</script>

<?php include 'includes/footer.php'; ?>