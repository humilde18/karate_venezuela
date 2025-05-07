<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 3) {
    header("Location: ../login.php");
    exit();
}

include '../conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$nombre = trim($_POST['nombre']);
$email = trim($_POST['email']);

$sql = "UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $nombre, $email, $id_usuario);

if ($stmt->execute()) {
    $_SESSION['nombre'] = $nombre;
    header("Location: dashboard_alumno.php");
    exit();
} else {
    echo "Error al actualizar perfil.";
}
?>
