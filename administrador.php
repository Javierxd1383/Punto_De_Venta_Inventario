<?php
session_start();

// Verificar si el usuario tiene el rol de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php"); // Redirige al login si no es administrador
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(120deg, #a29bfe, #dfe6e9, #6c5ce7);
            background-size: 200% 200%;
            animation: gradientShift 8s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .menu-container {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 1100px;
            width: 100%;
        }

        .menu-title {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #6c5ce7;
            margin-bottom: 20px;
        }

        .menu-item {
            background: #f5f5f5;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .menu-item:hover {
            transform: translateY(-10px);
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
        }

        .menu-icon {
            font-size: 45px;
            color: #6c5ce7;
            margin-bottom: 15px;
        }

        .menu-title-item {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
            transition: color 0.3s ease;
        }

        .menu-item:hover .menu-title-item {
            color: #6c5ce7;
        }

        .text-muted {
            margin-bottom: 30px;
            font-size: 16px;
        }

        a.menu-item {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="menu-container">
        <h1 class="menu-title">Panel de Administrador</h1>
        <p class="text-center text-muted">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></p>
        <div class="row g-4">
            <!-- Gestión de Usuarios -->
            <div class="col-md-4">
                <a href="gestion_usuarios.php" class="menu-item">
                    <i class="fa fa-users menu-icon"></i>
                    <div class="menu-title-item">Gestión de Usuarios</div>
                </a>
            </div>
            <!-- Gestión de Productos -->
            <div class="col-md-4">
                <a href="gestion_productos.php" class="menu-item">
                    <i class="fa fa-box menu-icon"></i>
                    <div class="menu-title-item">Gestión de Productos</div>
                </a>
            </div>
            <!-- Gestión de Ventas -->
            <div class="col-md-4">
                <a href="gestion_ventas.php" class="menu-item">
                    <i class="fa fa-shopping-cart menu-icon"></i>
                    <div class="menu-title-item">Gestión de Ventas</div>
                </a>
            </div>
            <!-- Reportes -->
            <div class="col-md-4">
                <a href="reporte_ventas.php" class="menu-item">
                    <i class="fa fa-chart-line menu-icon"></i>
                    <div class="menu-title-item">Reportes</div>
                </a>
            </div>
            <!-- Configuraciones -->
            <div class="col-md-4">
                <a href="configuraciones.php" class="menu-item">
                    <i class="fa fa-cogs menu-icon"></i>
                    <div class="menu-title-item">Configuraciones</div>
                </a>
            </div>
            <!-- Cerrar Sesión -->
            <div class="col-md-4">
                <a href="logout.php" class="menu-item">
                    <i class="fa fa-sign-out-alt menu-icon"></i>
                    <div class="menu-title-item">Cerrar Sesión</div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
