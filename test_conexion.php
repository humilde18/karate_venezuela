<?php
$host = "localhost";
$usuario = "root";
$contrasena = ""; // Si usas XAMPP, normalmente la contraseña está vacía
$basedatos = "karate_venezuela";

// Intentar conexión con la base de datos
$conexion = mysqli_connect($host, $usuario, $contrasena, $basedatos);

// Verificar la conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
} else {
    echo "Conexión exitosa a la base de datos.";
}

// Cerrar conexión
mysqli_close($conexion);
?>
