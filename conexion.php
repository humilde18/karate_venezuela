<?php
$servidor = "localhost";
$usuario = "root"; // Usualmente "root" en XAMPP
$contrasena = ""; // Contraseña de la base de datos
$base_de_datos = "karate_venezuela"; // El nombre de tu base de datos

// Crear conexión
$conexion = new mysqli($servidor, $usuario, $contrasena, $base_de_datos);

// Verificar si la conexión fue exitosa
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>
