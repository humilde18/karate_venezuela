<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 2) {
    header("Location: ../login.php");
    exit();
}

$usuario_id = $_SESSION['id_usuario'];
$alerta = "";

// Obtener datos del usuario
$consulta_usuario = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$consulta_usuario->bind_param("i", $usuario_id);
$consulta_usuario->execute();
$usuario = $consulta_usuario->get_result()->fetch_assoc();

// Obtener el dojo del usuario
$mi_dojo = null;
if ($usuario['dojo_id']) {
    $consulta_dojo = $conexion->prepare("SELECT * FROM dojos WHERE id = ?");
    $consulta_dojo->bind_param("i", $usuario['dojo_id']);
    $consulta_dojo->execute();
    $mi_dojo = $consulta_dojo->get_result()->fetch_assoc();
}

// Crear dojo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_dojo'])) {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];

    $crear = $conexion->prepare("INSERT INTO dojos (nombre, direccion, sensei_id) VALUES (?, ?, ?)");
    $crear->bind_param("ssi", $nombre, $direccion, $usuario_id);
    $crear->execute();
    $nuevo_dojo_id = $conexion->insert_id;

    $asignar = $conexion->prepare("UPDATE usuarios SET dojo_id = ? WHERE id = ?");
    $asignar->bind_param("ii", $nuevo_dojo_id, $usuario_id);
    $asignar->execute();

    $alerta = "¡Dojo creado correctamente!";
    header("Location: mi_dojo.php?alert=dojo_creado");
    exit();
}

// Agregar usuario al dojo
if (isset($_POST['agregar_usuario']) && isset($_POST['usuario_id'])) {
    $usuario_agregar = $_POST['usuario_id'];
    $asignar_usuario = $conexion->prepare("UPDATE usuarios SET dojo_id = ? WHERE id = ?");
    $asignar_usuario->bind_param("ii", $usuario['dojo_id'], $usuario_agregar);
    $asignar_usuario->execute();
    header("Location: mi_dojo.php?alert=usuario_agregado");
    exit();
}

// Eliminar usuario del dojo
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id_eliminar = $_GET['eliminar'];

    $res = $conexion->prepare("SELECT rol FROM usuarios WHERE id = ? AND dojo_id = ?");
    $res->bind_param("ii", $id_eliminar, $usuario['dojo_id']);
    $res->execute();
    $datos = $res->get_result()->fetch_assoc();

    if ($datos) {
        $rol_a_eliminar = $datos['rol'];
        $soy_creador = $mi_dojo && $mi_dojo['sensei_id'] == $usuario_id;
        $puede_eliminar = ($rol_a_eliminar == 3) || ($rol_a_eliminar == 2 && $soy_creador);

        if ($puede_eliminar) {
            $eliminar = $conexion->prepare("UPDATE usuarios SET dojo_id = NULL WHERE id = ?");
            $eliminar->bind_param("i", $id_eliminar);
            $eliminar->execute();
        }
    }

    header("Location: mi_dojo.php?alert=usuario_eliminado");
    exit();
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Dojo</title>
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

  h1, p, h4, h5, label {
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

  select.form-select, input.form-control, .btn {
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
  }

  .btn-light {
    background-color: white;
    color: black;
  }

  .btn-danger {
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
  }
</style>

</head>
<body>

<div class="sidebar">
  <h4 class="text-center text-white">Sensei Panel</h4>
  <a href="dashboard_sensei.php">🏠 Inicio</a>
  <a href="mi_dojo.php">🏯 Mi Dojo</a>
  <a href="mis_eventos.php">📅 Eventos</a>
  <a href="../logout.php" class="text-danger">🚪 Cerrar Sesión</a>
</div>

<div class="main-content">
  <h1 class="mb-4">Mi Dojo</h1>

  <?php if (isset($_GET['alert'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php
      switch ($_GET['alert']) {
        case 'dojo_creado': echo '✅ Dojo creado exitosamente.'; break;
        case 'usuario_agregado': echo '👤 Usuario agregado al dojo.'; break;
        case 'usuario_eliminado': echo '❌ Usuario eliminado del dojo.'; break;
      }
      ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  <?php endif; ?>

  <?php if (!$mi_dojo): ?>
    <!-- Crear dojo -->
    <div class="card card-blue p-4 mb-4">
      <h4 class="mb-3">Crear un dojo</h4>
      <form method="POST">
        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre del Dojo</label>
          <input type="text" class="form-control" name="nombre" required>
        </div>
        <div class="mb-3">
          <label for="direccion" class="form-label">Dirección</label>
          <input type="text" class="form-control" name="direccion" required>
        </div>
        <button type="submit" name="crear_dojo" class="btn btn-light">Crear</button>
      </form>
    </div>
  <?php else: ?>
    <!-- Info Dojo -->
    <div class="card card-blue p-4 mb-4">
      <h4>Nombre: <?= htmlspecialchars($mi_dojo['nombre']) ?></h4>
      <p>Dirección: <?= htmlspecialchars($mi_dojo['direccion']) ?></p>
    </div>

    <!-- Senseis -->
    <h5>Senseis del Dojo</h5>
    <ul class="list-group mb-4">
      <?php
      $senseis = $conexion->prepare("SELECT * FROM usuarios WHERE dojo_id = ? AND rol = 2");
      $senseis->bind_param("i", $mi_dojo['id']);
      $senseis->execute();
      $res_senseis = $senseis->get_result();
      while ($fila = $res_senseis->fetch_assoc()):
      ?>
        <li class="list-group-item text-dark d-flex justify-content-between align-items-center">
          <?= htmlspecialchars($fila['nombre'] . ' ' . $fila['apellido']) ?>
          <div>
            <a href="ver_usuario.php?id=<?= $fila['id'] ?>" class="btn btn-primary btn-sm">Ver info</a>
            <?php if ($mi_dojo['sensei_id'] == $usuario_id && $fila['id'] != $usuario_id): ?>
              <a href="?eliminar=<?= $fila['id'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
            <?php endif; ?>
          </div>
        </li>
      <?php endwhile; ?>
    </ul>

    <!-- Alumnos -->
    <h5>Alumnos del Dojo</h5>
    <ul class="list-group mb-4">
      <?php
      $alumnos = $conexion->prepare("SELECT * FROM usuarios WHERE dojo_id = ? AND rol = 3");
      $alumnos->bind_param("i", $mi_dojo['id']);
      $alumnos->execute();
      $res_alumnos = $alumnos->get_result();
      while ($fila = $res_alumnos->fetch_assoc()):
      ?>
        <li class="list-group-item text-dark d-flex justify-content-between align-items-center">
          <?= htmlspecialchars($fila['nombre'] . ' ' . $fila['apellido']) ?>
          <div>
            <a href="ver_usuario.php?id=<?= $fila['id'] ?>" class="btn btn-primary btn-sm">Ver info</a>
            <a href="?eliminar=<?= $fila['id'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
          </div>
        </li>
      <?php endwhile; ?>
    </ul>

    <!-- Agregar Sensei -->
    <h5>Agregar Sensei</h5>
    <form method="POST" class="row g-3 mb-3">
      <div class="col-md-6">
        <select name="usuario_id" class="form-select" required>
          <option value="">-- Seleccionar Sensei sin dojo --</option>
          <?php
          $sin_dojo_sensei = $conexion->query("SELECT * FROM usuarios WHERE dojo_id IS NULL AND rol = 2");
          while ($u = $sin_dojo_sensei->fetch_assoc()) {
            echo "<option value='{$u['id']}'>{$u['nombre']} {$u['apellido']} (Sensei)</option>";
          }
          ?>
        </select>
      </div>
      <div class="col-md-6 align-self-end">
        <button type="submit" name="agregar_usuario" class="btn btn-light">Agregar</button>
      </div>
    </form>

    <!-- Agregar Alumno -->
    <h5>Agregar Alumno</h5>
    <form method="POST" class="row g-3">
      <div class="col-md-6">
        <select name="usuario_id" class="form-select" required>
          <option value="">-- Seleccionar Alumno sin dojo --</option>
          <?php
          $sin_dojo_alumno = $conexion->query("SELECT * FROM usuarios WHERE dojo_id IS NULL AND rol = 3");
          while ($u = $sin_dojo_alumno->fetch_assoc()) {
            echo "<option value='{$u['id']}'>{$u['nombre']} {$u['apellido']} (Alumno)</option>";
          }
          ?>
        </select>
      </div>
      <div class="col-md-6 align-self-end">
        <button type="submit" name="agregar_usuario" class="btn btn-light">Agregar</button>
      </div>
    </form>

  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
