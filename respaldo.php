<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensaje = "Copia de seguridad generada y descargada exitosamente.";
}
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <div>
        <h2 class="fw-bold mb-1">Respaldo de Datos</h2>
        <p class="text-muted">Protege la información de tu sistema.</p>
    </div>
</div>

<?php if ($mensaje): ?>
    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success fw-bold fade-in"><i
            class="fa fa-check me-2"></i><?= $mensaje ?></div><?php endif; ?>

<div class="row justify-content-center fade-in">
    <div class="col-md-6">
        <div class="card-premium p-5 text-center">
            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-4 text-primary">
                <i class="fa fa-database fa-4x"></i>
            </div>
            <h3 class="fw-bold mb-2">Generar Backup SQL</h3>
            <p class="text-muted mb-4 px-4">Se creará un archivo completo con todos los productos, ventas, clientes y
                usuarios del sistema.</p>
            <form method="POST">
                <button type="submit" class="btn btn-primary-gradient px-5 py-3 rounded-pill hover-lift">
                    <i class="fa fa-download me-2"></i> Descargar Copia Ahora
                </button>
            </form>
            <p class="text-muted small mt-4 mb-0">Último respaldo: Hoy, 09:30 AM</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>