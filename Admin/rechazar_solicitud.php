<?php
session_start();
include '../conexion.php';

// Verificar si el usuario está logueado y es admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 1) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_solicitud = intval($_GET['id']);

    // Cambiar estado a 2 (rechazada)
    mysqli_query($conexion, "UPDATE solicitudes SET estado = 2 WHERE id = $id_solicitud AND estado = 0");
}

header("Location: solicitudes.php");
exit();
?>
