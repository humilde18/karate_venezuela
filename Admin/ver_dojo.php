<?php
session_start();
include '../conexion.php';

// Verificar si el usuario está logueado y es admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Obtener el ID del dojo
$dojo_id = intval($_GET['id']);

// Obtener el dojo
$dojo = $conexion->query("SELECT nombre, direccion FROM dojos WHERE id = $dojo_id")->fetch_assoc();

// Obtener los senseis asociados al dojo
$senseis = $conexion->query("SELECT id, nombre, apellido FROM usuarios WHERE rol = 2 AND dojo_id = $dojo_id");

// Obtener los alumnos asociados al dojo
$alumnos = $conexion->query("SELECT id, nombre, apellido FROM usuarios WHERE rol = 3 AND dojo_id = $dojo_id");

// Obtener senseis sin dojo
$senseis_disponibles = $conexion->query("SELECT id, nombre, apellido FROM usuarios WHERE rol = 2 AND dojo_id IS NULL");

// Obtener alumnos sin dojo
$alumnos_disponibles = $conexion->query("SELECT id, nombre, apellido FROM usuarios WHERE rol = 3 AND dojo_id IS NULL");

// Asignar sensei al dojo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['asignar_sensei'])) {
    $sensei_id = intval($_POST['sensei_id']);
    $conexion->query("UPDATE usuarios SET dojo_id = $dojo_id WHERE id = $sensei_id");
    header("Location: ver_dojo.php?id=$dojo_id");
    exit();
}

// Asignar alumno al dojo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['asignar_alumno'])) {
    $alumno_id = intval($_POST['alumno_id']);
    $conexion->query("UPDATE usuarios SET dojo_id = $dojo_id WHERE id = $alumno_id");
    header("Location: ver_dojo.php?id=$dojo_id");
    exit();
}

// Eliminar sensei del dojo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_sensei_id'])) {
    $sensei_id = intval($_POST['eliminar_sensei_id']);
    $conexion->query("UPDATE usuarios SET dojo_id = NULL WHERE id = $sensei_id");
    header("Location: ver_dojo.php?id=$dojo_id");
    exit();
}

// Eliminar alumno del dojo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_alumno_id'])) {
    $alumno_id = intval($_POST['eliminar_alumno_id']);
    $conexion->query("UPDATE usuarios SET dojo_id = NULL WHERE id = $alumno_id");
    header("Location: ver_dojo.php?id=$dojo_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalles del Dojo</title>
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
      flex-direction: column;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .container {
      background-color: #1e3c72;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
      margin-top: 40px;
      color: white;
    }

    h1, h2, p {
      text-shadow: 1px 1px 3px black;
    }

    table {
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
    }

    table th, table td {
      vertical-align: middle !important;
    }

    .btn-primary {
      background-color:rgb(255, 187, 0);
      border: none;
      transition: background 0.3s;
      color: black;
    }

    .btn-primary:hover {
      background-color:rgb(250, 198, 56);
      color: black;
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <h1>Detalles del Dojo: <?= htmlspecialchars($dojo['nombre']) ?></h1>
  <p><strong>Dirección:</strong> <?= htmlspecialchars($dojo['direccion']) ?></p>

  <h2>Senseis</h2>
  <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalSenseis">Añadir Sensei</button>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($sensei = $senseis->fetch_assoc()): ?>
        <tr>
          <td><?= $sensei['id'] ?></td>
          <td><?= htmlspecialchars($sensei['nombre']) ?></td>
          <td><?= htmlspecialchars($sensei['apellido']) ?></td>
          <td>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="eliminar_sensei_id" value="<?= $sensei['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <h2>Alumnos</h2>
  <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalAlumnos">Añadir Alumno</button>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($alumno = $alumnos->fetch_assoc()): ?>
        <tr>
          <td><?= $alumno['id'] ?></td>
          <td><?= htmlspecialchars($alumno['nombre']) ?></td>
          <td><?= htmlspecialchars($alumno['apellido']) ?></td>
          <td>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="eliminar_alumno_id" value="<?= $alumno['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <a href="editar_dojos.php" class="btn btn-primary">Volver</a>
</div>

<!-- Modal para añadir senseis -->
<div class="modal fade" id="modalSenseis" tabindex="-1" aria-labelledby="modalSenseisLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalSenseisLabel">Añadir Sensei</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST">
          <div class="mb-3">
            <label for="sensei_id" class="form-label">Seleccionar Sensei</label>
            <select name="sensei_id" id="sensei_id" class="form-select" required>
              <option value="">Seleccione un sensei</option>
              <?php while ($sensei = $senseis_disponibles->fetch_assoc()): ?>
                <option value="<?= $sensei['id'] ?>"><?= htmlspecialchars($sensei['nombre'] . ' ' . $sensei['apellido']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <button type="submit" name="asignar_sensei" class="btn btn-primary">Asignar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal para añadir alumnos -->
<div class="modal fade" id="modalAlumnos" tabindex="-1" aria-labelledby="modalAlumnosLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAlumnosLabel">Añadir Alumno</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST">
          <div class="mb-3">
            <label for="alumno_id" class="form-label">Seleccionar Alumno</label>
            <select name="alumno_id" id="alumno_id" class="form-select" required>
              <option value="">Seleccione un alumno</option>
              <?php while ($alumno = $alumnos_disponibles->fetch_assoc()): ?>
                <option value="<?= $alumno['id'] ?>"><?= htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <button type="submit" name="asignar_alumno" class="btn btn-primary">Asignar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>