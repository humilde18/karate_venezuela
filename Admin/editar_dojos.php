<?php
session_start();
include '../conexion.php';

// Verificar si el usuario está logueado y es admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Procesar edición de dojo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_dojo'])) {
    $id = intval($_POST['editar_dojo_id']);
    $nombre = $_POST['editar_nombre'];
    $direccion = $_POST['editar_direccion'];

    // Actualizar el dojo
    $stmt = $conexion->prepare("UPDATE dojos SET nombre = ?, direccion = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nombre, $direccion, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: editar_dojos.php");
    exit();
}

// Agregar dojo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_dojo'])) {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];

    // Insertar el dojo en la base de datos
    $stmt = $conexion->prepare("INSERT INTO dojos (nombre, direccion) VALUES (?, ?)");
    $stmt->bind_param("ss", $nombre, $direccion);
    $stmt->execute();
    $stmt->close();

    // Recargar los datos de la tabla
    $dojos = $conexion->query("SELECT id, nombre, direccion FROM dojos");
}

// Eliminar dojo
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);

    // Eliminar dojo
    $conexion->query("DELETE FROM dojos WHERE id = $id");

    header("Location: editar_dojos.php");
    exit();
}

// Obtener dojo a editar
$dojo_a_editar = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $conexion->prepare("SELECT * FROM dojos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $dojo_a_editar = $resultado->fetch_assoc();
    $stmt->close();
}

// Listar dojos
$dojos = $conexion->query("SELECT id, nombre, direccion FROM dojos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Dojos</title>
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

    .card-blue {
      background-color: #1e3c72 !important;
      color: white;
      border: none;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    h1, label {
      text-shadow: 1px 1px 3px black;
    }

    table {
      background-color: rgba(255,255,255,0.1);
      color: white;
    }

    table th, table td {
      vertical-align: middle !important;
    }

    .form-control, .form-select {
      background-color: rgba(255,255,255,0.8);
    }
  </style>
</head>
<body>

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

<div class="main-content">
  <h1>Gestión de Dojos</h1>

  <!-- Agregar dojo -->
  <div class="card card-blue p-4 mb-5">
    <h4>
      <button class="btn btn-link text-white text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#formularioAgregarDojo" aria-expanded="false" aria-controls="formularioAgregarDojo">
        Agregar Dojo <span>&#9660;</span>
      </button>
    </h4>
    <div class="collapse" id="formularioAgregarDojo">
      <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="nombre" class="form-label">Nombre del Dojo</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" name="direccion" id="direccion" class="form-control" required>
          </div>
        </div>
        <button type="submit" name="agregar_dojo" class="btn btn-light">Agregar Dojo</button>
      </form>
    </div>
  </div>

  <!-- Editar dojo -->
  <?php if ($dojo_a_editar): ?>
  <div class="card card-blue p-4 mb-5">
    <h4>Editar Dojo (ID <?= $dojo_a_editar['id'] ?>)</h4>
    <form method="POST">
      <input type="hidden" name="editar_dojo_id" value="<?= $dojo_a_editar['id'] ?>">
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="editar_nombre" class="form-label">Nombre del Dojo</label>
          <input type="text" name="editar_nombre" class="form-control" value="<?= htmlspecialchars($dojo_a_editar['nombre']) ?>" required>
        </div>
        <div class="col-md-6">
          <label for="editar_direccion" class="form-label">Dirección</label>
          <input type="text" name="editar_direccion" class="form-control" value="<?= htmlspecialchars($dojo_a_editar['direccion']) ?>" required>
        </div>
      </div>
      <button type="submit" name="editar_dojo" class="btn btn-light">Guardar Cambios</button>
    </form>
  </div>
  <?php endif; ?>

  <!-- Tabla de dojos -->
  <div class="card card-blue p-4">
    <h4>Lista de Dojos</h4>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Dirección</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($dojo = $dojos->fetch_assoc()): ?>
          <tr>
            <td><?= $dojo['id'] ?></td>
            <td><?= htmlspecialchars($dojo['nombre']) ?></td>
            <td><?= htmlspecialchars($dojo['direccion']) ?></td>
            <td>
              <a href="?editar=<?= $dojo['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
              <a href="?eliminar=<?= $dojo['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este dojo?')">Eliminar</a>
              <a href="ver_dojo.php?id=<?= $dojo['id'] ?>" class="btn btn-info btn-sm">Ver</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>