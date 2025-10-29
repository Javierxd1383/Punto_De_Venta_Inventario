<?php
session_start();
include 'conexion.php'; // Archivo de conexión a la base de datos

// Verificar si el usuario tiene el rol de empleado
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php"); // Redirige al login si no es empleado
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herramientas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #8e44ad, #3498db);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            color: #fff;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 900px;
            margin: auto;
            color: #333;
        }
        .tool-section {
            margin-bottom: 30px;
        }
        .btn-tool {
            width: 100%;
            text-align: left;
            background: #3498db;
            color: white;
            border: none;
            padding: 15px;
            font-size: 18px;
            border-radius: 10px;
            transition: background 0.3s;
        }
        .btn-tool:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-primary mb-4">Herramientas</h1>

        <!-- Botón para regresar al menú -->
        <div class="mb-3">
            <a href="loginempleado.php" class="btn btn-secondary">Regresar al Menú</a>
        </div>

        <!-- Opciones de herramientas -->
        <div class="tool-section">
            <button class="btn-tool" onclick="location.href='configuracion.php'">Configuración del Sistema</button>
        </div>

        <div class="tool-section">
            <button class="btn-tool" onclick="location.href='respaldo.php'">Respaldo de la Base de Datos</button>
        </div>

        <div class="tool-section">
            <button class="btn-tool" onclick="location.href='restauracion.php'">Restaurar Base de Datos</button>
        </div>

        <div class="tool-section">
            <button class="btn-tool" onclick="location.href='informacion_sistema.php'">Información del Sistema</button>
        </div>
    </div>
</body>
</html>
