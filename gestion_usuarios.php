<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-5 fade-in">
    <div>
        <h2 class="fw-bold mb-1">Gestión de Usuarios</h2>
        <p class="text-muted">Control de accesos y personal.</p>
    </div>
</div>

<div class="row g-4 fade-in">
    <!-- Administrators Card -->
    <div class="col-md-6">
        <a href="gestion_administradores.php"
            class="card-premium p-5 text-decoration-none d-block h-100 group-hover hover-lift">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary p-3 rounded-4 text-white shadow-lg me-3">
                    <i class="fa fa-user-shield fa-2x"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-dark mb-1">Administradores</h4>
                    <small class="text-muted">Acceso total al sistema</small>
                </div>
            </div>
            <p class="text-muted mb-4 opacity-75">Configura cuentas con privilegios elevados para la gestión del
                negocio.</p>
            <div class="d-flex align-items-center text-primary fw-bold">
                Gestionar ahora <i class="fa fa-arrow-right ms-2 transition-icon"></i>
            </div>
        </a>
    </div>

    <!-- Employees Card -->
    <div class="col-md-6">
        <a href="gestion_empleados.php"
            class="card-premium p-5 text-decoration-none d-block h-100 group-hover hover-lift">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-success p-3 rounded-4 text-white shadow-lg me-3">
                    <i class="fa fa-user-tie fa-2x"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-dark mb-1">Empleados</h4>
                    <small class="text-muted">Personal de ventas</small>
                </div>
            </div>
            <p class="text-muted mb-4 opacity-75">Controla el acceso de cajeros y vendedores a las herramientas de
                venta.</p>
            <div class="d-flex align-items-center text-primary fw-bold">
                Gestionar ahora <i class="fa fa-arrow-right ms-2 transition-icon"></i>
            </div>
        </a>
    </div>
</div>

<style>
    .hover-lift:hover {
        transform: translateY(-5px);
    }

    .group-hover:hover .transition-icon {
        transform: translateX(5px);
    }
</style>

<?php include 'includes/footer.php'; ?>