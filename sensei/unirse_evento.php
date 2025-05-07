<?php
session_start();
include '../conexion.php';

// Verificar si el usuario está autenticado y es sensei
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 2) {
    header("Location: ../login.php");
    exit();
}

$id_sensei = $_SESSION['id_usuario'];
$evento_id = isset($_GET['evento_id']) ? intval($_GET['evento_id']) : 0;

// Obtener el dojo del sensei
$query_dojo = "SELECT dojo_id FROM usuarios WHERE id = $id_sensei";
$res_dojo = mysqli_query($conexion, $query_dojo);
$fila_dojo = mysqli_fetch_assoc($res_dojo);
$dojo_id = $fila_dojo['dojo_id'] ?? 0;

// Verificar si ya está registrado en el evento
$query_verificar = "SELECT * FROM dojo_evento WHERE dojo_id = $dojo_id AND evento_id = $evento_id";
$res_verificar = mysqli_query($conexion, $query_verificar);

if (mysqli_num_rows($res_verificar) > 0) {
    // Ya está registrado
    header("Location: mis_eventos.php?msg=ya-registrado");
    exit();
}

// Registrar participación
$query_insertar = "INSERT INTO dojo_evento (dojo_id, evento_id) VALUES ($dojo_id, $evento_id)";
if (mysqli_query($conexion, $query_insertar)) {
    header("Location: mis_eventos.php?msg=registro-exitoso");
    exit();
} else {
    echo "Error al registrar participación: " . mysqli_error($conexion);
}
?>
