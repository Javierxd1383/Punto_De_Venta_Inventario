<?php
// Incluir la conexión a la base de datos
include 'conexion.php';

// Iniciar sesión
session_start();

$error = null; // Variable para mostrar mensajes de error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar que los campos estén definidos
    $usuario = isset($_POST['usuario']) ? mysqli_real_escape_string($conn, $_POST['usuario']) : null;
    $contrasena = isset($_POST['contrasena']) ? mysqli_real_escape_string($conn, $_POST['contrasena']) : null;
    $rol = isset($_POST['rol']) ? mysqli_real_escape_string($conn, $_POST['rol']) : null;

    // Validar que los campos no estén vacíos
    if ($usuario && $contrasena && $rol) {
        // Consulta para verificar el usuario según su rol
        $query = ($rol === 'empleado') 
            ? "SELECT * FROM empleados WHERE usuario = '$usuario' AND contrasena = '$contrasena' AND activo = 1" 
            : "SELECT * FROM administradores WHERE usuario = '$usuario' AND contrasena = '$contrasena'";

        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $usuario_data = mysqli_fetch_assoc($result);
            $_SESSION['id_usuario'] = $usuario_data['id_admin'] ?? $usuario_data['id_empleado'];
            $_SESSION['nombre'] = $usuario_data['nombre'];
            $_SESSION['rol'] = $rol;

            // Redirigir según el rol
            header("Location: " . ($rol === 'empleado' ? "loginempleado.php" : "administrador.php"));
            exit();
        } else {
            $error = "Credenciales incorrectas o usuario inactivo.";
        }
    } else {
        $error = "Por favor, complete todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
            overflow: hidden;
            background: linear-gradient(45deg, #fbc2eb, #a18cd1, #fbc2eb);
            background-size: 300% 300%;
            animation: gradientChange 10s ease infinite;
        }

        @keyframes gradientChange {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        .login-container {
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            max-width: 400px;
            width: 100%;
            text-align: center;
            animation: fadeIn 1.5s ease;
        }

        .login-container img {
            max-width: 100px;
            margin-bottom: 1.5rem;
        }

        .login-container h3 {
            color: #d35400;
            margin-bottom: 1.5rem;
            font-weight: bold;
        }

        .form-control, .form-select {
            border-radius: 30px;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e5e5;
            margin-bottom: 1rem;
        }

        .btn-primary {
            background: #d35400;
            border: none;
            border-radius: 30px;
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.3s ease-in-out;
            color: #fff;
            font-weight: bold;
        }

        .btn-primary:hover {
            background: #e67e22;
        }

        .alert {
            border-radius: 10px;
            animation: fadeInUp 0.5s ease;
        }

        /* Animaciones */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Logo de la dulcería -->
        <img src="imagenes/logo.png" alt="Logo de la Dulcería">
        <h3>Acceso al Sistema</h3>
        <h7>Dulcería Candy: Donde la dulzura cobra vida.</h7>
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Usuario" required>
            </div>
            <div class="mb-3">
                <input type="password" name="contrasena" id="contrasena" class="form-control" placeholder="Contraseña" required>
            </div>
            <div class="mb-3">
                <select name="rol" id="rol" class="form-select" required>
                    <option value="" disabled selected>Seleccione su rol</option>
                    <option value="empleado">Empleado</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
