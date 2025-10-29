<?php
session_start();

// Verificar si el usuario tiene el rol de empleado
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php"); // Redirige al login si no es empleado
    exit();
}

// Mensaje de bienvenida
$nombreEmpleado = $_SESSION['nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Empleados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #8e44ad, #3498db);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 1200px;
        }
        h1 {
            color: #6c5ce7;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .menu-item {
            background: #f9f9f9;
            color: #6c5ce7;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #dcdde1;
        }
        .menu-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .menu-item i {
            font-size: 50px;
            margin-bottom: 15px;
        }
        .menu-item h3 {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .menu-item:hover h3 {
            color: #6c5ce7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>¡Bienvenido, <?php echo htmlspecialchars($nombreEmpleado); ?>!</h1>
        <div class="menu-grid">
            <!-- Nueva Venta -->
            <a href="nueva.php" class="menu-item text-decoration-none">
                <i class="fa-solid fa-cart-shopping"></i>
                <h3>Nueva Venta</h3>
            </a>
            <!-- Historial de Ventas -->
            <a href="historial_ventas.php" class="menu-item text-decoration-none">
                <i class="fa-solid fa-receipt"></i>
                <h3>Historial de Ventas</h3>
            </a>
            <!-- Consulta de Productos -->
            <a href="inventario.php" class="menu-item text-decoration-none">
                <i class="fa-solid fa-box-open"></i>
                <h3>Inventario</h3>
            </a>
            <!-- Herramientas -->
            <a href="herramientas.php" class="menu-item text-decoration-none">
                <i class="fa-solid fa-tools"></i>
                <h3>Herramientas</h3>
            </a>
            <!-- Cerrar Sesión -->
            <a href="logout.php" class="menu-item text-decoration-none">
                <i class="fa-solid fa-right-from-bracket"></i>
                <h3>Cerrar Sesión</h3>
            </a>
        </div>
    </div>

    <!-- Agregar FontAwesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
