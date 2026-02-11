<?php
if (!isset($_SESSION['rol'])) {
    // Guest or redirect
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Dulcería Candy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>

<body>

    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="d-flex align-items-center mb-4 px-2">
                <img src="Imagenes/logo.png" alt="Logo" height="40" class="me-2">
                <div>
                    <h4 class="fw-bold mb-0 text-primary">Candy</h4>
                    <small class="text-muted">Sistema</small>
                </div>
            </div>

            <div class="nav-menu">
                <small class="text-uppercase text-muted fw-bold px-3 mb-2 d-block" style="font-size: 0.75rem;">Menu
                    Principal</small>

                <?php if ($_SESSION['rol'] == 'administrador'): ?>
                    <a href="administrador.php"
                        class="nav-item-custom <?= basename($_SERVER['PHP_SELF']) == 'administrador.php' ? 'active' : '' ?>">
                        <i class="fas fa-chart-pie"></i> Dashboard
                    </a>
                    <a href="gestion_productos.php"
                        class="nav-item-custom <?= basename($_SERVER['PHP_SELF']) == 'gestion_productos.php' ? 'active' : '' ?>">
                        <i class="fas fa-box-open"></i> Productos
                    </a>
                    <a href="gestion_ventas.php"
                        class="nav-item-custom <?= basename($_SERVER['PHP_SELF']) == 'gestion_ventas.php' ? 'active' : '' ?>">
                        <i class="fas fa-shopping-bag"></i> Ventas
                    </a>
                    <a href="gestion_usuarios.php"
                        class="nav-item-custom <?= basename($_SERVER['PHP_SELF']) == 'gestion_usuarios.php' ? 'active' : '' ?>">
                        <i class="fas fa-users"></i> Usuarios
                    </a>
                    <a href="reporte_ventas.php"
                        class="nav-item-custom <?= basename($_SERVER['PHP_SELF']) == 'reporte_ventas.php' ? 'active' : '' ?>">
                        <i class="fas fa-file-invoice"></i> Reportes
                    </a>
                <?php else: ?>
                    <a href="loginempleado.php"
                        class="nav-item-custom <?= basename($_SERVER['PHP_SELF']) == 'loginempleado.php' ? 'active' : '' ?>">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                    <a href="nueva.php"
                        class="nav-item-custom <?= basename($_SERVER['PHP_SELF']) == 'nueva.php' ? 'active' : '' ?>">
                        <i class="fas fa-cash-register"></i> Nueva Venta
                    </a>
                    <a href="historial_ventas.php"
                        class="nav-item-custom <?= basename($_SERVER['PHP_SELF']) == 'historial_ventas.php' ? 'active' : '' ?>">
                        <i class="fas fa-history"></i> Historial
                    </a>
                    <a href="inventario.php"
                        class="nav-item-custom <?= basename($_SERVER['PHP_SELF']) == 'inventario.php' ? 'active' : '' ?>">
                        <i class="fas fa-search"></i> Consultar
                    </a>
                    <a href="herramientas.php"
                        class="nav-item-custom <?= basename($_SERVER['PHP_SELF']) == 'herramientas.php' ? 'active' : '' ?>">
                        <i class="fas fa-tools"></i> Herramientas
                    </a>
                    <a href="configuracion.php"
                        class="nav-item-custom <?= basename($_SERVER['PHP_SELF']) == 'configuracion.php' ? 'active' : '' ?>">
                        <i class="fas fa-cog"></i> Configuración
                    </a>
                <?php endif; ?>
            </div>

            <div class="mt-auto border-top pt-3">
                <a href="logout.php" class="nav-item-custom text-danger">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="d-flex align-items-center">
                    <button class="btn btn-light rounded-circle shadow-sm d-lg-none me-3" id="sidebarToggle"><i
                            class="fa fa-bars"></i></button>
                    <h4 class="mb-0 fw-bold"><?= ucfirst(str_replace(".php", "", basename($_SERVER['PHP_SELF']))) ?>
                    </h4>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="text-end d-none d-md-block">
                        <p class="mb-0 fw-bold small text-dark"><?= $_SESSION['nombre'] ?></p>
                        <p class="mb-0 small text-muted text-uppercase" style="font-size: 0.7rem;">
                            <?= $_SESSION['rol'] ?>
                        </p>
                    </div>
                    <div class="bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 40px; height: 40px;">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="p-4 container-fluid">