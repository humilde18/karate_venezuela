<?php
session_start();
include '../conexion.php';

// Verificar sesión y rol
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 2) {
    header("Location: ../login.php");
    exit();
}

$id_sensei = $_SESSION['id_usuario'];
$evento_id = isset($_GET['evento_id']) ? intval($_GET['evento_id']) : 0;

// Obtener el dojo del sensei
$query = "SELECT dojo_id FROM usuarios WHERE id = $id_sensei";
$res = mysqli_query($conexion, $query);
$fila = mysqli_fetch_assoc($res);
$dojo_id = $fila['dojo_id'] ?? 0;

// Validar que la participación exista
$validar = "SELECT * FROM dojo_evento WHERE dojo_id = $dojo_id AND evento_id = $evento_id";
$check = mysqli_query($conexion, $validar);

if (mysqli_num_rows($check) > 0) {
    $delete = "DELETE FROM dojo_evento WHERE dojo_id = $dojo_id AND evento_id = $evento_id";
    mysqli_query($conexion, $delete);
}

// Redirigir de vuelta a mis_eventos.php
header("Location: mis_eventos.php");
exit();
