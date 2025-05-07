<?php
session_start();
include '../conexion.php';

// Verifica si el usuario está logueado y es Admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 1) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Dojos</title>
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
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }

    .card-option {
      height: 200px;
      width: 300px;
      margin: 20px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.5);
      font-size: 1.5rem;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
    }

    .bg-ver {
      background-color: #28a745; /* Verde */
    }

    .bg-editar {
      background-color: #007bff; /* Azul */
    }

    .card-option:hover {
      opacity: 0.9;
    }

  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="text-center text-white">Admin Panel</h4>
    <a href="dashboard_admin.php">🏠 Dashboard</a>
    <a href="dojos.php">🏯 Dojos</a>
    <a href="senseis.php">🥋 Senseis</a>
    <a href="alumnos.php">👦 Alumnos</a> <!-- NUEVA OPCIÓN -->
    <a href="eventos.php">📅 Eventos</a>
    <a href="solicitudes.php">📨 Solicitudes</a>
    
  </div>

  <!-- Contenido Principal -->
  <div class="main-content">
    <div class="d-flex flex-wrap justify-content-center">
      <a href="ver_dojos.php" class="card-option bg-ver text-white">
        Ver Dojos
      </a>
      <a href="editar_dojos.php" class="card-option bg-editar text-white">
        Editar / Crear Dojos
      </a>
    </div>
  </div>

</body>
</html>
