<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}
include 'conexion.php';
include 'includes/header.php';

// Stats logic (simplified for immediate display)
$total_ventas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) as t FROM ventas"))['t'] ?? 0;
$ventas_hoy = mysqli_fetch_assoc(mysqli_query($conn, "SELECT count(*) as c FROM ventas WHERE DATE(fecha) = CURDATE()"))['c'] ?? 0;
$productos_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT count(*) as c FROM productos"))['c'] ?? 0;
?>

<div class="container-fluid fade-in">
    <!-- Welcome Header -->
    <div class="d-flex justify-content-between align-items-center mb-5 pt-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">Panel de Control</h2>
            <p class="text-muted mb-0">Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?></p>
        </div>
        <div>
            <span class="badge bg-white text-muted shadow-sm px-3 py-2 rounded-pill border">
                <i class="fa fa-calendar-alt me-2"></i> <?= date('d/m/Y') ?>
            </span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-md h-100 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-start z-1 position-relative">
                        <div>
                            <p class="text-muted fw-bold text-uppercase small mb-1">Ingresos Totales</p>
                            <h3 class="fw-bold text-dark mb-0">$<?= number_format($total_ventas, 2) ?></h3>
                        </div>
                        <div class="bg-primary-light text-primary rounded-circle p-3 shadow-sm">
                            <i class="fa fa-dollar-sign fa-lg"></i>
                        </div>
                    </div>
                    <div class="position-absolute bottom-0 start-0 w-100 bg-primary opacity-10" style="height: 4px;">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-md h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fw-bold text-uppercase small mb-1">Ventas Hoy</p>
                            <h3 class="fw-bold text-dark mb-0"><?= $ventas_hoy ?></h3>
                        </div>
                        <div class="bg-white text-info border rounded-circle p-3 shadow-sm">
                            <i class="fa fa-shopping-bag fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-md h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fw-bold text-uppercase small mb-1">Productos</p>
                            <h3 class="fw-bold text-dark mb-0"><?= $productos_total ?></h3>
                        </div>
                        <div class="bg-white text-warning border rounded-circle p-3 shadow-sm">
                            <i class="fa fa-box-open fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <h5 class="fw-bold text-dark mb-4">Accesos Rápidos</h5>
    <div class="row g-4">
        <div class="col-md-3">
            <a href="gestion_productos.php" class="card border-0 shadow-sm h-100 text-decoration-none hover-scale">
                <div class="card-body text-center p-5">
                    <div class="text-primary mb-3">
                        <i class="fa fa-cubes fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Inventario</h5>
                    <p class="text-muted small mb-0">Gestionar stock y precios</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="gestion_usuarios.php" class="card border-0 shadow-sm h-100 text-decoration-none hover-scale">
                <div class="card-body text-center p-5">
                    <div class="text-secondary mb-3">
                        <i class="fa fa-users-cog fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Usuarios</h5>
                    <p class="text-muted small mb-0">Administrar personal</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="reporte_ventas.php" class="card border-0 shadow-sm h-100 text-decoration-none hover-scale">
                <div class="card-body text-center p-5">
                    <div class="text-success mb-3">
                        <i class="fa fa-chart-line fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Reportes</h5>
                    <p class="text-muted small mb-0">Ver estadísticas</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="configuraciones.php" class="card border-0 shadow-sm h-100 text-decoration-none hover-scale">
                <div class="card-body text-center p-5">
                    <div class="text-muted mb-3">
                        <i class="fa fa-cog fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Ajustes</h5>
                    <p class="text-muted small mb-0">Configuración general</p>
                </div>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>