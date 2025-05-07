<?php
session_start();
include '../conexion.php';

// Verificar sesión y rol
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 2) {
    header("Location: ../login.php");
    exit();
}

$id_sensei = $_SESSION['id_usuario'];

// Obtener el dojo del sensei
$query_dojo = "SELECT dojo_id FROM usuarios WHERE id = $id_sensei";
$res_dojo = mysqli_query($conexion, $query_dojo);
$fila_dojo = mysqli_fetch_assoc($res_dojo);
$dojo_id = $fila_dojo['dojo_id'] ?? 0;

// Si el sensei no tiene dojo, mostrar mensaje y detener ejecución
if ($dojo_id == 0) {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Mis Eventos</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
        <style>
            body {
                margin: 0;
                height: 100vh;
                background: linear-gradient(-45deg, #1e3c72, #2a5298, #e52d27, #b31217);
                background-size: 400% 400%;
                animation: gradientBG 15s ease infinite;
                color: white;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            @keyframes gradientBG {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            .card {
                background-color: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                text-align: center;
                width: 400px;
            }
        </style>
    </head>
    <body>
        <div class='card'>
            <h2>No tienes un dojo asignado</h2>
            <p>Para participar en eventos, primero debes tener un dojo asignado.</p>
        </div>
    </body>
    </html>";
    exit();
}

// Eventos creados por su dojo
$query_mis_eventos = "SELECT * FROM eventos WHERE dojo_id = $dojo_id ORDER BY fecha DESC";
$res_mis_eventos = mysqli_query($conexion, $query_mis_eventos);

// Eventos de otros dojos (disponibles para participar)
$query_disponibles = "SELECT e.*, d.nombre AS dojo_nombre 
                      FROM eventos e 
                      INNER JOIN dojos d ON e.dojo_id = d.id 
                      WHERE e.dojo_id != $dojo_id 
                      AND e.id NOT IN (SELECT evento_id FROM dojo_evento WHERE dojo_id = $dojo_id)
                      ORDER BY fecha DESC";
$res_disponibles = mysqli_query($conexion, $query_disponibles);

// Eventos donde el dojo participa
$query_participando = "
  SELECT e.*, d.nombre AS dojo_nombre
  FROM dojo_evento de
  INNER JOIN eventos e ON e.id = de.evento_id
  INNER JOIN dojos d ON e.dojo_id = d.id
  WHERE de.dojo_id = $dojo_id AND e.dojo_id != $dojo_id
  ORDER BY e.fecha DESC
";
$res_participando = mysqli_query($conexion, $query_participando);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Eventos</title>
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
      margin-bottom: 20px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .card h5 {
      border-bottom: 1px solid white;
      padding-bottom: 5px;
      margin-bottom: 10px;
    }

    .btn-outline-light {
      border-color: white;
      color: white;
    }

    .btn-outline-light:hover {
      background-color: white;
      color: black;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="text-center text-white">Sensei Panel</h4>
    <a href="mi_dojo.php">🏯 Mi Dojo</a>
    <a href="mis_eventos.php">📅 Mis Eventos</a>
    <a href="../logout.php" class="text-danger">🚪 Cerrar Sesión</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>📅 Eventos de mi Dojo</h2>
      <a href="crear_evento.php" class="btn btn-success">➕ Crear Evento</a>
    </div>

    <?php if (mysqli_num_rows($res_mis_eventos) > 0): ?>
      <?php while($evento = mysqli_fetch_assoc($res_mis_eventos)): ?>
        <div class="card">
          <h5><?= htmlspecialchars($evento['nombre']) ?></h5>
          <p><strong>Fecha:</strong> <?= htmlspecialchars($evento['fecha']) ?></p>
          <p><strong>Descripción:</strong> <?= htmlspecialchars($evento['descripcion']) ?></p>
          <a href="eventos_mi_dojo.php?evento_id=<?= $evento['id'] ?>" class="btn btn-outline-info btn-sm mt-2">👁️ Ver Detalles</a>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No has creado eventos aún.</p>
    <?php endif; ?>

    <hr>
    <h2>📌 Eventos disponibles de otros dojos</h2>
    <?php if (mysqli_num_rows($res_disponibles) > 0): ?>
      <?php while($evento = mysqli_fetch_assoc($res_disponibles)): ?>
        <div class="card">
          <h5><?= htmlspecialchars($evento['nombre']) ?></h5>
          <p><strong>Fecha:</strong> <?= htmlspecialchars($evento['fecha']) ?></p>
          <p><strong>Organizado por:</strong> <?= htmlspecialchars($evento['dojo_nombre']) ?></p>
          <p><strong>Descripción:</strong> <?= htmlspecialchars($evento['descripcion']) ?></p>
          <a href="unirse_evento.php?evento_id=<?= $evento['id'] ?>" class="btn btn-outline-light btn-sm">Solicitar participación</a>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No hay eventos disponibles por otros dojos.</p>
    <?php endif; ?>

    <hr>
    <h2>✅ Eventos en los que mi dojo participa</h2>
    <?php if (mysqli_num_rows($res_participando) > 0): ?>
      <?php while($evento = mysqli_fetch_assoc($res_participando)): ?>
        <div class="card">
          <h5><?= htmlspecialchars($evento['nombre']) ?></h5>
          <p><strong>Fecha:</strong> <?= htmlspecialchars($evento['fecha']) ?></p>
          <p><strong>Organizado por:</strong> <?= htmlspecialchars($evento['dojo_nombre']) ?></p>
          <p><strong>Descripción:</strong> <?= htmlspecialchars($evento['descripcion']) ?></p>
          <a href="ver_evento.php?evento_id=<?= $evento['id'] ?>" class="btn btn-outline-info btn-sm">👁️ Ver Detalles</a>
          <a href="cancelar_participacion.php?evento_id=<?= $evento['id'] ?>" class="btn btn-outline-danger btn-sm mt-2">❌ Cancelar Participación</a>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>Tu dojo aún no participa en eventos externos.</p>
    <?php endif; ?>
  </div>

</body>
</html>