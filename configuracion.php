<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php");
    exit();
}

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
    foreach ($_POST['config'] as $clave => $valor) {
        $valor_limpio = mysqli_real_escape_string($conn, $valor);
        $clave_limpia = mysqli_real_escape_string($conn, $clave);
        mysqli_query($conn, "UPDATE configuraciones SET valor = '$valor_limpio' WHERE clave = '$clave_limpia'");
    }
    header("Location: configuracion.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <div>
        <h2 class="fw-bold mb-1">Ajustes de Local</h2>
        <p class="text-muted">Información visible en tickets.</p>
    </div>
    <a href="loginempleado.php" class="btn btn-light text-muted"><i class="fa fa-arrow-left me-2"></i> Volver</a>
</div>

<div class="row justify-content-center fade-in">
    <div class="col-md-8">
        <div class="card-premium p-5">
            <div class="text-center mb-4">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 text-primary">
                    <i class="fa fa-store fa-3x"></i>
                </div>
            </div>
            <form method="POST">
                <div class="mb-4">
                    <label class="text-muted small fw-bold text-uppercase mb-2">Nombre del Negocio</label>
                    <input type="text" name="config[nombre_dulceria]" value="<?= htmlspecialchars($nombre_dulceria) ?>"
                        class="form-control form-control-lg bg-light border-0">
                </div>
                <div class="mb-4">
                    <label class="text-muted small fw-bold text-uppercase mb-2">Dirección Física</label>
                    <input type="text" name="config[direccion]" value="<?= htmlspecialchars($direccion) ?>"
                        class="form-control form-control-lg bg-light border-0">
                </div>
                <div class="mb-5">
                    <label class="text-muted small fw-bold text-uppercase mb-2">Teléfono de Contacto</label>
                    <input type="text" name="config[telefono]" value="<?= htmlspecialchars($telefono) ?>"
                        class="form-control form-control-lg bg-light border-0">
                </div>
                <button type="submit" class="btn btn-primary-gradient w-100 py-3 rounded-pill fw-bold hover-lift">
                    Guardar Cambios
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>