<?php
session_start();
include '../conexion.php';

// Verificar si el usuario está logueado y es alumno
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 3) {
    header("Location: ../login.php");
    exit();
}

$id_alumno = $_SESSION['id_usuario'];

// Obtener los datos personales del alumno, incluyendo el ID del KYU
$query_alumno = "SELECT u.nombre, u.apellido, u.email, u.fecha_nacimiento, u.kyu AS kyu_id, d.nombre AS dojo_nombre 
                 FROM usuarios u 
                 LEFT JOIN dojos d ON u.dojo_id = d.id 
                 WHERE u.id = $id_alumno";
$res_alumno = mysqli_query($conexion, $query_alumno);
$alumno = mysqli_fetch_assoc($res_alumno);

// Obtener el nombre del KYU si está asignado
$kyu_nombre = 'No asignado';
if (!empty($alumno['kyu_id'])) {
    $query_kyu = "SELECT nombre FROM kyu WHERE id = " . intval($alumno['kyu_id']);
    $res_kyu = mysqli_query($conexion, $query_kyu);
    $kyu = mysqli_fetch_assoc($res_kyu);
    $kyu_nombre = $kyu['nombre'] ?? 'No disponible';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Alumno</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      height: 100vh;
      background: linear-gradient(-45deg, #1e3c72, #2a5298, #e52d27, #b31217);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      color: white;
      display: flex;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .sidebar {
      width: 240px;
      background-color: rgba(0, 0, 0, 0.8);
      height: 100vh;
      padding-top: 20px;
      position: fixed;
    }

    .sidebar a {
      display: block;
      padding: 15px 20px;
      color: white;
      text-decoration: none;
      transition: background 0.3s;
    }

    .sidebar a:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }

    .main-content {
      margin-left: 240px;
      padding: 40px;
      flex-grow: 1;
    }

    .card {
      background-color: rgba(0, 0, 0, 0.7);
      color: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
      margin-bottom: 20px;
    }

    .card h4 {
      border-bottom: 1px solid white;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    .card p {
      margin: 5px 0;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="text-center text-white">Alumno Panel</h4>
    <a href="mi_dojo.php">📚 Mi Dojo</a>
    <a href="mis_eventos.php">📅 Mis Eventos</a>
    <a href="procesar_solicitud.php">📨 Solicitudes</a>
    <a href="../logout.php" class="text-danger">🚪 Cerrar Sesión</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Bienvenido, <?= htmlspecialchars($alumno['nombre'] ?? 'Alumno') ?> (Alumno)</h1>
    <p>Este es tu panel de administración. Aquí puedes ver tus datos personales.</p>

    <!-- Datos personales -->
    <div class="card">
      <h4>📋 Datos Personales</h4>
      <p><strong>Nombre:</strong> <?= htmlspecialchars($alumno['nombre'] ?? 'No disponible') ?></p>
      <p><strong>Apellido:</strong> <?= htmlspecialchars($alumno['apellido'] ?? 'No disponible') ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($alumno['email'] ?? 'No disponible') ?></p>
      <p><strong>Fecha de Nacimiento:</strong> <?= htmlspecialchars($alumno['fecha_nacimiento'] ?? 'No disponible') ?></p>
      <p><strong>KYU:</strong> <?= htmlspecialchars($kyu_nombre) ?></p>
      <p><strong>Dojo:</strong> <?= htmlspecialchars($alumno['dojo_nombre'] ?? 'No asignado') ?></p>
    </div>
  </div>

</body>
</html>