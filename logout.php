<?php
session_start();
session_destroy(); // Cierra la sesión
header("Location: index.php"); // Redirige al index.php
exit();
?>