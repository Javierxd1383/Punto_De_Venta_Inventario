<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php");
    exit();
}

$nombre_archivo = 'respaldo_' . date('Y-m-d_H-i-s') . '.sql';
header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=$nombre_archivo");

$comando = "mysqldump -u root -p [nombre_base_datos] > " . $nombre_archivo;
system($comando);
?>
