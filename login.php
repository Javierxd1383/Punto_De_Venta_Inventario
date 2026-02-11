<?php
include 'conexion.php';
session_start();
$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = mysqli_real_escape_string($conn, $_POST['usuario']);
    $contrasena = mysqli_real_escape_string($conn, $_POST['contrasena']);
    $rol = $_POST['rol'];

    $table = ($rol === 'empleado') ? 'empleados' : 'administradores';
    $id_field = ($rol === 'empleado') ? 'id_empleado' : 'id_admin';

    $query = "SELECT * FROM $table WHERE usuario = '$usuario' AND contrasena = '$contrasena'";
    if ($rol === 'empleado') {
        $query .= " AND activo = 1";
    }

    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) === 1) {
        $data = mysqli_fetch_assoc($result);
        $_SESSION['id_usuario'] = $data[$id_field];
        $_SESSION['nombre'] = $data['nombre'];
        $_SESSION['rol'] = $rol;
        header("Location: " . ($rol === 'empleado' ? "loginempleado.php" : "administrador.php"));
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Dulcería Candy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2962FF;
            --primary-dark: #0039cb;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: #4e54c8;
            /* Fallback for old browsers */
            background: -webkit-linear-gradient(to left, #8f94fb, #4e54c8);
            /* Chrome 10-25, Safari 5.1-6 */
            background: linear-gradient(to left, #8f94fb, #4e54c8);
            /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
            overflow: hidden;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        /* Professional "Area" Animation */
        .circles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            margin: 0;
            padding: 0;
            z-index: -1;
        }

        .circles li {
            position: absolute;
            display: block;
            list-style: none;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.2);
            animation: animate 25s linear infinite;
            bottom: -150px;
            border-radius: 20%;
            /* Soft squares */
        }

        .circles li:nth-child(1) {
            left: 25%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
        }

        .circles li:nth-child(2) {
            left: 10%;
            width: 20px;
            height: 20px;
            animation-delay: 2s;
            animation-duration: 12s;
        }

        .circles li:nth-child(3) {
            left: 70%;
            width: 20px;
            height: 20px;
            animation-delay: 4s;
        }

        .circles li:nth-child(4) {
            left: 40%;
            width: 60px;
            height: 60px;
            animation-delay: 0s;
            animation-duration: 18s;
        }

        .circles li:nth-child(5) {
            left: 65%;
            width: 20px;
            height: 20px;
            animation-delay: 0s;
        }

        .circles li:nth-child(6) {
            left: 75%;
            width: 110px;
            height: 110px;
            animation-delay: 3s;
        }

        .circles li:nth-child(7) {
            left: 35%;
            width: 150px;
            height: 150px;
            animation-delay: 7s;
        }

        .circles li:nth-child(8) {
            left: 50%;
            width: 25px;
            height: 25px;
            animation-delay: 15s;
            animation-duration: 45s;
        }

        .circles li:nth-child(9) {
            left: 20%;
            width: 15px;
            height: 15px;
            animation-delay: 2s;
            animation-duration: 35s;
        }

        .circles li:nth-child(10) {
            left: 85%;
            width: 150px;
            height: 150px;
            animation-delay: 0s;
            animation-duration: 11s;
        }

        @keyframes animate {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
                border-radius: 0;
            }

            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
                border-radius: 50%;
            }
        }

        .login-card {
            width: 100%;
            max-width: 360px;
            /* Reduced width */
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            /* Reduced padding */
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
            position: relative;
            z-index: 10;
        }

        .form-control {
            background: #ffffff;
            border: 1px solid #ced4da;
            /* Simpler border */
            border-radius: 8px;
            height: 42px;
            /* Reduced height */
            padding: 0 15px;
            font-size: 0.95rem;
            transition: all 0.2s;
            color: #495057;
        }

        .form-control:focus {
            background: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(41, 98, 255, 0.15);
        }

        .form-label {
            font-weight: 700;
            font-size: 0.75rem;
            /* Smaller label */
            letter-spacing: 0.5px;
            color: #6c757d;
            margin-bottom: 5px;
            display: block;
            text-transform: uppercase;
        }

        .btn-login {
            height: 45px;
            /* Reduced button height */
            border-radius: 8px;
            background: var(--primary);
            color: white;
            font-weight: 600;
            letter-spacing: 0.5px;
            border: none;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(41, 98, 255, 0.2);
            font-size: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(41, 98, 255, 0.3);
            background: var(--primary-dark);
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .logo-container {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 50%;
            margin: 0 auto 15px auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <!-- Professional Background Animation -->
    <ul class="circles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>

    <div class="login-card animate-up">
        <div class="text-center mb-4">
            <div class="logo-container">
                <img src="Imagenes/logo.png" alt="Logo" height="40">
            </div>
            <h4 class="fw-bold text-dark mb-1">Bienvenido</h4>
            <p class="text-muted small mb-0">Ingresa tus credenciales</p>
        </div>

        <?php if ($error): ?>
            <div
                class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 py-2 text-center mb-3 fw-bold small">
                <i class="fa fa-exclamation-circle me-1"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" name="usuario" class="form-control" required placeholder="Usuario">
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="contrasena" class="form-control" required placeholder="••••••••">
            </div>

            <div class="mb-4">
                <label class="form-label mb-2 text-center w-100">Rol</label>
                <div class="d-flex justify-content-center gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="rol" id="r1" value="empleado" checked>
                        <label class="form-check-label ms-1 small fw-bold text-dark" for="r1">Empleado</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="rol" id="r2" value="administrador">
                        <label class="form-check-label ms-1 small fw-bold text-dark" for="r2">Admin</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3">
                Entrar
            </button>
        </form>

        <div class="text-center">
            <a href="index.php" class="text-decoration-none text-secondary small fw-bold hover-primary"
                style="font-size: 0.85rem;">
                <i class="fa fa-arrow-left me-1"></i> Volver al sitio
            </a>
        </div>
    </div>

</body>

</html>