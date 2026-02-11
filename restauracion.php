<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <div>
        <h2 class="fw-bold mb-1">Restauración de Sistema</h2>
        <p class="text-muted">Recupera datos desde un archivo previo (Precaución).</p>
    </div>
</div>

<div class="row justify-content-center fade-in">
    <div class="col-md-6">
        <div class="card-premium p-5 text-center">
            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-4 mb-4 text-warning">
                <i class="fa fa-history fa-4x"></i>
            </div>
            <h3 class="fw-bold mb-2">Cargar Archivo SQL</h3>
            <p class="text-muted mb-4 px-4">Selecciona un archivo de respaldo (.sql) para restaurar la base de datos al
                estado anterior.</p>

            <form action="importar.php" method="POST" enctype="multipart/form-data">
                <div class="mb-4 text-start bg-light p-3 rounded-4 border">
                    <label class="text-muted small fw-bold text-uppercase mb-2">Archivo de Respaldo</label>
                    <input type="file" class="form-control" name="backup_file" required>
                </div>
                <button type="submit" class="btn btn-warning w-100 py-3 rounded-pill fw-bold text-dark hover-lift">
                    <i class="fa fa-upload me-2"></i> Iniciar Restauración
                </button>
            </form>
            <div
                class="alert alert-warning border-0 bg-warning bg-opacity-10 text-dark small mt-4 mb-0 rounded-3 text-start">
                <i class="fa fa-exclamation-triangle me-2"></i> <strong>Advertencia:</strong> Esta acción sobrescribirá
                los datos actuales. Asegúrate de tener un respaldo reciente antes de continuar.
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>