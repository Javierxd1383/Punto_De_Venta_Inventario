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
        <h2 class="fw-bold mb-1">Información del Sistema</h2>
        <p class="text-muted">Detalles técnicos del servidor y la versión.</p>
    </div>
</div>

<div class="row g-4 fade-in">
    <div class="col-md-6">
        <div class="card-premium p-4 h-100">
            <h5 class="fw-bold mb-4">Entorno del Servidor</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between bg-transparent px-0">
                    <span class="text-muted">Software</span>
                    <span class="fw-bold"><?= $_SERVER['SERVER_SOFTWARE'] ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-transparent px-0">
                    <span class="text-muted">Versión PHP</span>
                    <span class="badge badge-success"><?= phpversion() ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-transparent px-0">
                    <span class="text-muted">Dirección IP</span>
                    <span class="fw-bold font-monospace"><?= $_SERVER['REMOTE_ADDR'] ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-transparent px-0">
                    <span class="text-muted">Base de Datos</span>
                    <span class="fw-bold">MySQLi (MariaDB)</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-premium p-4 h-100 d-flex flex-column justify-content-center text-center">
            <div class="mb-4">
                <i class="fa-solid fa-layer-group fa-4x text-primary mb-3"></i>
                <h3 class="fw-bold">Candy PRO</h3>
                <p class="text-muted">v2.5 Enterprise Edition</p>
            </div>
            <p class="small text-muted mb-0">Desarrollado con <i class="fa fa-heart text-danger"></i> para máxima
                eficiencia.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>