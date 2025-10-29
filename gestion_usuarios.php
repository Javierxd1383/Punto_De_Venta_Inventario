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
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #a29bfe, #6c5ce7, #dfe6e9);
            background-size: 200% 200%;
            animation: gradientShift 8s ease infinite;
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Arial', sans-serif;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .menu-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        .menu-title {
            color: #6c5ce7;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .menu-button {
            display: block;
            width: 100%;
            margin: 15px 0;
            padding: 15px;
            border: none;
            border-radius: 30px;
            font-size: 18px;
            font-weight: bold;
            background: linear-gradient(45deg, #6c5ce7, #a29bfe);
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }

        .menu-button:hover {
            transform: translateY(-3px);
            background: linear-gradient(45deg, #a29bfe, #6c5ce7);
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
        }

        .menu-button:active {
            transform: translateY(0);
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
        }

        .menu-button:last-child {
            background: #d63031;
        }

        .menu-button:last-child:hover {
            background: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="menu-container">
        <h1 class="menu-title">Gestión de Usuarios</h1>
        <button class="menu-button" onclick="location.href='gestion_administradores.php'">Gestionar Administradores</button>
        <button class="menu-button" onclick="location.href='gestion_empleados.php'">Gestionar Empleados</button>
        <button class="menu-button" onclick="location.href='administrador.php'">Volver al Panel</button>
    </div>
</body>
</html>
