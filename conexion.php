<?php
$host = "localhost";      // Cambia esto si tu servidor no es local
$usuario = "root";        // Usuario de tu base de datos
$contrasena = "";         // Contraseña de tu base de datos
$base_datos = "dulceria"; // Nombre de tu base de datos

// Crear conexión (estilo orientado a objetos)
$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer codificación
$conn->set_charset("utf8mb4");
?>
