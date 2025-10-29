<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php");
    exit();
}

// El @ suprime errores si PHP no tiene permisos para acceder a la información del disco.
$informacion = [
    'PHP Version' => phpversion(),
    'Sistema Operativo' => PHP_OS,
    'Espacio en Disco Libre' => @round(disk_free_space("/") / 1024 / 1024, 2) . ' MB',
    'Espacio en Disco Total' => @round(disk_total_space("/") / 1024 / 1024, 2) . ' MB',
    'Nombre Servidor' => gethostname(),
    'Dirección IP' => $_SERVER['SERVER_ADDR'],
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información del Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos personalizados para el botón de regreso */
        .btn-back {
            background-color: #6c757d; /* Color gris estándar de Bootstrap (secondary) */
            border-color: #6c757d;
            color: white;
            transition: background-color 0.3s;
        }
        .btn-back:hover {
            background-color: #5a6268;
            border-color: #5a6268;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center text-primary">Información del Sistema</h1>

        <div class="d-flex justify-content-start mb-3">
            <a href="herramientas.php" class="btn btn-back">
                Regresar a Herramientas
            </a>
        </div>
        
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Atributo</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($informacion as $atributo => $valor): ?>
                    <tr>
                        <td><?php echo $atributo; ?></td>
                        <td><?php echo $valor; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>