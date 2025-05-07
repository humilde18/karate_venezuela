<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// Consulta del usuario
$sql = "SELECT u.nombre, u.apellido, u.rol, u.fecha_nacimiento, 
               d.nombre AS dojo_nombre,
               u.dan, u.kyu
        FROM usuarios u
        LEFT JOIN dojos d ON u.dojo_id = d.id
        WHERE u.id = $id_usuario";

$resultado = $conn->query($sql);
$usuario = $resultado->fetch_assoc();

$fecha_nac = new DateTime($usuario['fecha_nacimiento']);
$hoy = new DateTime();
$edad = $hoy->diff($fecha_nac)->y;

// Contar participaciones en eventos
$sql_eventos = "SELECT COUNT(*) AS total FROM participaciones WHERE usuario_id = $id_usuario";
$resultado_eventos = $conn->query($sql_eventos);
$eventos = $resultado_eventos->fetch_assoc();
$cant_eventos = $eventos['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Perfil</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      height: 100vh;
      background: linear-gradient(-45deg, #1e3c72, #2a5298, #e52d27, #b31217);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      color: white;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .container {
      margin-top: 80px;
      background-color: rgba(0,0,0,0.6);
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.5);
    }

    h2, p {
      text-shadow: 1px 1px 3px black;
    }

    .info {
      font-size: 1.2rem;
    }
  </style>
</head>
<body>
  <div class="container text-white">
    <h2 class="mb-4">Perfil de Usuario</h2>

    <p class="info"><strong>Nombre:</strong> <?= $usuario['nombre'] ?></p>
    <p class="info"><strong>Apellido:</strong> <?= $usuario['apellido'] ?></p>
    <p class="info"><strong>Dojo:</strong> <?= $usuario['dojo_nombre'] ?? 'No asignado' ?></p>
    <p class="info"><strong>Fecha de nacimiento:</strong> <?= $usuario['fecha_nacimiento'] ?></p>
    <p class="info"><strong>Edad:</strong> <?= $edad ?> años</p>

    <?php if ($usuario['rol'] === 'sensei'): ?>
      <p class="info"><strong>DAN:</strong> <?= $usuario['dan'] ?? 'No especificado' ?></p>
    <?php elseif ($usuario['rol'] === 'alumno'): ?>
      <p class="info"><strong>KYU:</strong> <?= $usuario['kyu'] ?? 'No especificado' ?></p>
    <?php endif; ?>

    <p class="info"><strong>Participaciones en eventos:</strong> <?= $cant_eventos ?></p>
  </div>
</body>
</html>
