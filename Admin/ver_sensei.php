<?php
session_start();
include '../conexion.php';

// Verificar si el usuario está logueado y es admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 1) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: senseis.php");
    exit();
}

$sensei_id = intval($_GET['id']);

// Obtener datos del sensei
$query_sensei = "SELECT u.*, d.nombre AS dojo_nombre FROM usuarios u 
                 LEFT JOIN dojos d ON u.dojo_id = d.id 
                 WHERE u.id = ?";
$stmt = mysqli_prepare($conexion, $query_sensei);
mysqli_stmt_bind_param($stmt, 'i', $sensei_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$sensei = mysqli_fetch_assoc($result);

// Obtener alumnos si tiene dojo asignado
$alumnos = [];
if ($sensei && $sensei['dojo_id']) {
    $query_alumnos = "SELECT * FROM usuarios WHERE rol = 3 AND dojo_id = ?";
    $stmt2 = mysqli_prepare($conexion, $query_alumnos);
    mysqli_stmt_bind_param($stmt2, 'i', $sensei['dojo_id']);
    mysqli_stmt_execute($stmt2);
    $alumnos_result = mysqli_stmt_get_result($stmt2);
    while ($alumno = mysqli_fetch_assoc($alumnos_result)) {
        $alumnos[] = $alumno;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalle del Sensei</title>
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

    .bg-white-container {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .table th, .table td {
      color: black;
      vertical-align: middle;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="text-center text-white">Admin Panel</h4>
    <a href="dojos.php">🏯 Dojos</a>
    <a href="senseis.php">🥋 Senseis</a>
    <a href="alumnos.php">👦 Alumnos</a> <!-- NUEVA OPCIÓN -->
    <a href="eventos.php">📅 Eventos</a>
    <a href="solicitudes.php">📨 Solicitudes</a>
    <a href="../logout.php" class="text-danger">🚪 Cerrar Sesión</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Información del Sensei</h1>

    <div class="bg-white-container">
      <p><strong>Nombre:</strong> <?= htmlspecialchars($sensei['nombre']) . ' ' . htmlspecialchars($sensei['apellido']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($sensei['email']) ?></p>
      <p><strong>Dojo:</strong> <?= $sensei['dojo_nombre'] ? htmlspecialchars($sensei['dojo_nombre']) : 'No asignado' ?></p>
      <p><strong>ID del Dojo:</strong> <?= $sensei['dojo_id'] ? htmlspecialchars($sensei['dojo_id']) : 'No asignado' ?></p>

      <?php if (!empty($alumnos)) : ?>
        <h4 class="mt-4">Alumnos del Dojo</h4>
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Apellido</th>
              <th>Email</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($alumnos as $a) : ?>
              <tr>
                <td><?= htmlspecialchars($a['nombre']) ?></td>
                <td><?= htmlspecialchars($a['apellido']) ?></td>
                <td><?= htmlspecialchars($a['email']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="text-muted">No hay alumnos registrados en su dojo.</p>
      <?php endif; ?>

      <a href="senseis.php" class="btn btn-secondary mt-3">Volver</a>
    </div>
  </div>

</body>
</html>
