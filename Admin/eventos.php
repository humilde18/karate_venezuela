<?php
session_start();
include '../conexion.php';

// Verifica si es admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Obtener la lista de dojos
$dojos = mysqli_query($conexion, "SELECT id, nombre FROM dojos");

// Registrar evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $lugar = $_POST['lugar'];
    $dojo_id = $_POST['dojo_id'];

    $stmt = $conexion->prepare("INSERT INTO eventos (nombre, descripcion, fecha, lugar, dojo_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nombre, $descripcion, $fecha, $lugar, $dojo_id);
    $stmt->execute();
    $stmt->close();
    header("Location: eventos.php");
    exit();
}

// Eliminar evento
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM eventos WHERE id = $id");
    header("Location: eventos.php");
    exit();
}

// Editar evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $lugar = $_POST['lugar'];
    $dojo_id = $_POST['dojo_id'];

    $stmt = $conexion->prepare("UPDATE eventos SET nombre=?, descripcion=?, fecha=?, lugar=?, dojo_id=? WHERE id=?");
    $stmt->bind_param("ssssii", $nombre, $descripcion, $fecha, $lugar, $dojo_id, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: eventos.php");
    exit();
}

// Obtener eventos
$eventos = mysqli_query($conexion, "SELECT e.*, d.nombre AS dojo_nombre FROM eventos e LEFT JOIN dojos d ON e.dojo_id = d.id ORDER BY fecha DESC");

// Evento a editar si aplica
$eventoEditar = null;
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    $res = mysqli_query($conexion, "SELECT * FROM eventos WHERE id = $id");
    $eventoEditar = mysqli_fetch_assoc($res);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Eventos</title>
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
      background-color: rgb(2, 98, 122);
    }
    .main-content {
      margin-left: 240px;
      padding: 40px;
      flex-grow: 1;
    }
    .form-label, .table, .table th, .table td {
      color: black;
    }
    .card-white {
      background-color: #1e3c72 !important;
      color: white;
      padding: 20px;
      border-radius: 10px;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="text-center text-white">Admin Panel</h4>
    <a href="dashboard_admin.php">🏠 Inicio</a>
    <a href="editar_dojos.php">🏯 Dojos</a>
    <a href="senseis.php">🥋 Senseis</a>
    <a href="alumnos.php">👦 Alumnos</a>
    <a href="eventos.php">📅 Eventos</a>
    <a href="solicitudes.php">📨 Solicitudes</a>
    <a href="../logout.php" class="text-danger">🚪 Cerrar Sesión</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Gestión de Eventos</h1>

    <!-- Formulario -->
    <div class="card-white mb-4">
      <h4>
        <button class="btn btn-link text-white text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#formularioEvento" aria-expanded="false" aria-controls="formularioEvento">
          <?= $eventoEditar ? "Editar Evento" : "Registrar nuevo evento" ?> <span>&#9660;</span>
        </button>
      </h4>
      <div class="collapse" id="formularioEvento">
        <form method="POST" action="">
          <input type="hidden" name="accion" value="<?= $eventoEditar ? 'editar' : 'crear' ?>">
          <?php if ($eventoEditar): ?>
            <input type="hidden" name="id" value="<?= $eventoEditar['id'] ?>">
          <?php endif; ?>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre del Evento</label>
              <input type="text" name="nombre" class="form-control" required value="<?= $eventoEditar['nombre'] ?? '' ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control"><?= $eventoEditar['descripcion'] ?? '' ?></textarea>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Fecha</label>
              <input type="date" name="fecha" class="form-control" required value="<?= $eventoEditar['fecha'] ?? '' ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Lugar</label>
              <input type="text" name="lugar" class="form-control" required value="<?= $eventoEditar['lugar'] ?? '' ?>">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Dojo</label>
              <select name="dojo_id" class="form-control" required>
                <option value="">Seleccione un dojo</option>
                <?php while ($dojo = mysqli_fetch_assoc($dojos)): ?>
                  <option value="<?= $dojo['id'] ?>" <?= isset($eventoEditar['dojo_id']) && $eventoEditar['dojo_id'] == $dojo['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dojo['nombre']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
          </div>
          <button type="submit" class="btn btn-<?= $eventoEditar ? 'warning' : 'primary' ?>">
            <?= $eventoEditar ? 'Actualizar' : 'Registrar Evento' ?>
          </button>
          <?php if ($eventoEditar): ?>
            <a href="eventos.php" class="btn btn-secondary">Cancelar</a>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <!-- Lista -->
    <div class="card-white">
      <h4>Eventos Registrados</h4>
      <table class="table table-bordered table-striped mt-3 bg-white">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Fecha</th>
            <th>Lugar</th>
            <th>Dojo</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($evento = mysqli_fetch_assoc($eventos)): ?>
            <tr>
              <td><?= htmlspecialchars($evento['nombre']) ?></td>
              <td><?= htmlspecialchars($evento['descripcion']) ?></td>
              <td><?= htmlspecialchars($evento['fecha']) ?></td>
              <td><?= htmlspecialchars($evento['lugar']) ?></td>
              <td><?= htmlspecialchars($evento['dojo_nombre'] ?? 'No asignado') ?></td>
              <td>
                <a href="ver_evento.php?id=<?= $evento['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                <a href="eventos.php?editar=<?= $evento['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="eventos.php?eliminar=<?= $evento['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este evento?')">Eliminar</a>
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