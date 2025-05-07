<?php 
session_start();
include '../conexion.php';

// Verificar si el usuario está logueado y es admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Consultas para estadísticas
$total_usuarios = mysqli_fetch_row(mysqli_query($conexion, "SELECT COUNT(*) FROM usuarios"))[0];
$total_dojos = mysqli_fetch_row(mysqli_query($conexion, "SELECT COUNT(*) FROM dojos"))[0];
$total_senseis = mysqli_fetch_row(mysqli_query($conexion, "SELECT COUNT(*) FROM usuarios WHERE rol = 2"))[0];
$total_atletas = mysqli_fetch_row(mysqli_query($conexion, "SELECT COUNT(*) FROM usuarios WHERE rol = 3"))[0];
$total_eventos = mysqli_fetch_row(mysqli_query($conexion, "SELECT COUNT(*) FROM eventos"))[0];
$total_solicitudes = mysqli_fetch_row(mysqli_query($conexion, "SELECT COUNT(*) FROM solicitudes WHERE estado = 'pendiente'"))[0];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administrador</title>
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

    h1, p {
      text-shadow: 1px 1px 3px black;
    }

    .card-blue {
      background-color: #1e3c72 !important;
      color: white;
      border: none;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .card-blue h5 {
      font-size: 1.1rem;
      margin-bottom: 10px;
      border-bottom: 1px solid white;
      padding-bottom: 5px;
    }

    .card-blue .number {
      font-size: 2rem;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
 <!-- Sidebar -->
 <div class="sidebar">
  <h4 class="text-center text-white">Admin Panel</h4>
  <a href="dashboard_admin.php">🏠 Inicio</a>
  <a href="editar_dojos.php">🏯 Dojos</a>
  <a href="senseis.php">🥋 Senseis</a>
  <a href="alumnos.php">👦 Alumnos</a>
  <a href="eventos.php">📅 Eventos</a>
  <a href="solicitudes.php">📨 Solicitudes</a>
  <a href="../index.php" class="text-danger">🚪 Cerrar Sesión</a> <!-- Modificado -->
</div>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?> (Admin)</h1>
    <p>Este es tu panel de administración. Usa la barra lateral para gestionar el sistema.</p>

    <div class="row mb-4">
      <div class="col-md-4 mb-3">
        <div class="card card-blue text-center p-3">
          <h5>Dojos</h5>
          <div class="number"><?= $total_dojos ?></div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card card-blue text-center p-3">
          <h5>Senseis</h5>
          <div class="number"><?= $total_senseis ?></div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card card-blue text-center p-3">
          <h5>Atletas</h5>
          <div class="number"><?= $total_atletas ?></div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4 mb-3">
        <div class="card card-blue text-center p-3">
          <h5>Eventos</h5>
          <div class="number"><?= $total_eventos ?></div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card card-blue text-center p-3">
          <h5>Solicitudes</h5>
          <div class="number"><?= $total_solicitudes ?></div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card card-blue text-center p-3">
          <h5>Usuarios</h5>
          <div class="number"><?= $total_usuarios ?></div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
