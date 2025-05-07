<?php
session_start();
include '../conexion.php';

// Verifica que el usuario sea un sensei
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 2) {
    header("Location: ../login.php");
    exit();
}

// Obtener el dojo del sensei
$id_sensei = $_SESSION['id_usuario'];
$sql_dojo = "SELECT id FROM dojos WHERE sensei_id = $id_sensei";
$res_dojo = mysqli_query($conexion, $sql_dojo);
$dojo = mysqli_fetch_assoc($res_dojo);
$dojo_id = $dojo['id'] ?? 0;

// Crear evento si se envió el formulario
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $fecha = $_POST['fecha'];
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $direccion = mysqli_real_escape_string($conexion, $_POST['direccion']); // Nuevo campo

    $insert = "INSERT INTO eventos (nombre, fecha, descripcion, lugar, dojo_id) 
               VALUES ('$nombre', '$fecha', '$descripcion', '$direccion', $dojo_id)";
    if (mysqli_query($conexion, $insert)) {
        $mensaje = "✅ Evento creado exitosamente.";
    } else {
        $mensaje = "❌ Error al crear el evento.";
    }
}

// Obtener eventos disponibles (de otros dojos)
$sql_disponibles = "SELECT e.*, d.nombre AS dojo_nombre 
                    FROM eventos e 
                    JOIN dojos d ON e.dojo_id = d.id 
                    WHERE e.dojo_id != $dojo_id";
$res_disponibles = mysqli_query($conexion, $sql_disponibles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Evento - Sensei</title>
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
      overflow-y: auto;
      width: 100%;
    }

    .card {
      background-color: rgba(0, 0, 0, 0.7);
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 30px;
    }

    input, textarea {
      background-color: #f8f9fa;
      color: #000;
    }

    table {
      color: white;
    }

    .mensaje {
      margin-bottom: 20px;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="text-center text-white">Sensei Panel</h4>
    <a href="mi_dojo.php">🏯 Mi Dojo</a>
    <a href="mis_eventos.php">📅 Mis Eventos</a>
    <a href="crear_evento.php">➕ Crear Evento</a>
    <a href="../logout.php" class="text-danger">🚪 Cerrar Sesión</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h2>Crear Nuevo Evento</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-info mensaje"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="card">
      <form method="POST">
        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre del Evento</label>
          <input type="text" class="form-control" name="nombre" id="nombre" required>
        </div>
        <div class="mb-3">
          <label for="fecha" class="form-label">Fecha</label>
          <input type="date" class="form-control" name="fecha" id="fecha" required>
        </div>
        <div class="mb-3">
          <label for="descripcion" class="form-label">Descripción</label>
          <textarea class="form-control" name="descripcion" id="descripcion" rows="3"></textarea>
        </div>
        <div class="mb-3">
          <label for="direccion" class="form-label">Dirección</label> <!-- Nuevo campo -->
          <input type="text" class="form-control" name="direccion" id="direccion" required>
        </div>
        <button type="submit" class="btn btn-success">Crear Evento</button>
      </form>
    </div>

    <h2>Eventos Disponibles de Otros Dojos</h2>
    <div class="card">
      <?php if (mysqli_num_rows($res_disponibles) > 0): ?>
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Fecha</th>
              <th>Dojo Organizador</th>
              <th>Descripción</th>
              <th>Dirección</th> <!-- Mostrar dirección -->
            </tr>
          </thead>
          <tbody>
            <?php while ($evento = mysqli_fetch_assoc($res_disponibles)): ?>
              <tr>
                <td><?= htmlspecialchars($evento['nombre']) ?></td>
                <td><?= htmlspecialchars($evento['fecha']) ?></td>
                <td><?= htmlspecialchars($evento['dojo_nombre']) ?></td>
                <td><?= htmlspecialchars($evento['descripcion']) ?></td>
                <td><?= htmlspecialchars($evento['lugar']) ?></td> <!-- Mostrar dirección -->
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No hay eventos disponibles de otros dojos.</p>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>