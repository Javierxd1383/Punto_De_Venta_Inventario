<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="row fade-in">
    <div class="col-12 mb-4">
        <h2 class="fw-bold mb-0 text-primary">¡Hola, <?= htmlspecialchars($_SESSION['nombre']) ?>! 👋</h2>
        <p class="text-muted">Listo para repartir dulzura el día de hoy.</p>
    </div>
</div>

<div class="row g-4 fade-in">
    <!-- Big Action Card -->
    <div class="col-md-6">
        <a href="nueva.php"
            class="card-premium p-5 h-100 text-decoration-none text-center d-flex flex-column justify-content-center align-items-center bg-primary-gradient text-white position-relative overflow-hidden">
            <span class="position-absolute top-0 start-0 w-100 h-100 bg-white opacity-10"></span>
            <i class="fa fa-cash-register fa-5x mb-4 drop-shadow"></i>
            <h2 class="fw-bold mb-1">Nueva Venta</h2>
            <p class="opacity-75 fs-5">Cobrar productos ahora</p>
        </a>
    </div>

    <!-- Secondary Actions -->
    <div class="col-md-6">
        <div class="row g-4 h-100">
            <div class="col-12">
                <a href="historial_ventas.php"
                    class="card-premium p-4 h-100 text-decoration-none d-flex align-items-center hover-scale">
                    <div class="bg-success text-white p-3 rounded-circle me-4 fs-3 shadow-sm">
                        <i class="fa fa-history"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Mi Historial</h4>
                        <p class="text-muted mb-0">Revisa tus ventas del día</p>
                    </div>
                </a>
            </div>
            <div class="col-12">
                <a href="inventario.php"
                    class="card-premium p-4 h-100 text-decoration-none d-flex align-items-center hover-scale">
                    <div class="bg-warning text-white p-3 rounded-circle me-4 fs-3 shadow-sm">
                        <i class="fa fa-search"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Consultar Precios</h4>
                        <p class="text-muted mb-0">Busca productos rápido</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5 fade-in">
    <div class="col-12">
        <div class="card-premium p-4 bg-light border">
            <h5 class="fw-bold text-muted mb-3"><i class="fa fa-bullhorn me-2 text-warning"></i>Avisos Importantes</h5>
            <div class="d-flex align-items-center bg-white p-3 rounded-4 shadow-sm">
                <span class="badge bg-danger rounded-pill me-3 px-3 py-2">Oferta</span>
                <p class="mb-0 fw-bold text-dark">Recuerda ofrecer la promoción de 2x1 en chocolates Crunch.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-scale:hover {
        transform: scale(1.02);
    }
</style>

<?php include 'includes/footer.php'; ?>