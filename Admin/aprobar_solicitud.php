<?php
session_start();
include '../conexion.php';

// Verificar si el usuario está logueado y es admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Validar ID recibido
if (isset($_GET['id'])) {
    $id_solicitud = intval($_GET['id']);

    // Buscar solicitud
    $consulta = mysqli_query($conexion, "SELECT * FROM solicitudes WHERE id = $id_solicitud AND estado = 0");
    if ($solicitud = mysqli_fetch_assoc($consulta)) {
        $id_usuario = $solicitud['usuario_id'];

        // 1. Cambiar rol del usuario a 2 (sensei)
        mysqli_query($conexion, "UPDATE usuarios SET rol = 2 WHERE id = $id_usuario");

        // 2. Marcar la solicitud como aprobada (estado = 1)
        mysqli_query($conexion, "UPDATE solicitudes SET estado = 1 WHERE id = $id_solicitud");
    }
}

header("Location: solicitudes.php");
exit();
?>
