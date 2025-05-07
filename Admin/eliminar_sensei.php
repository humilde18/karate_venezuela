<?php
session_start();
include '../conexion.php';

// Verificar si es admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 1) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $sensei_id = intval($_GET['id']);

    // Cambiar su rol a 3 (alumno) y quitar dojo
    $query = "UPDATE usuarios SET rol = 3, dojo_id = NULL WHERE id = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, 'i', $sensei_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: senseis.php");
exit();
?>
