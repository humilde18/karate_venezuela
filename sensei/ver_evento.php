<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 2) {
    header("Location: ../login.php");
    exit();
}

$id_sensei = $_SESSION['id_usuario'];
$evento_id = isset($_GET['evento_id']) ? intval($_GET['evento_id']) : 0;

// Obtener el dojo del sensei
$query_dojo = "SELECT dojo_id FROM usuarios WHERE id = $id_sensei";
$res_dojo = mysqli_query($conexion, $query_dojo);
$fila_dojo = mysqli_fetch_assoc($res_dojo);
$dojo_id = $fila_dojo['dojo_id'] ?? 0;

// Verificar si el dojo participa en el evento
$verif = mysqli_query($conexion, "SELECT * FROM dojo_evento WHERE dojo_id = $dojo_id AND evento_id = $evento_id");

// Obtener información del evento
$query_evento = "SELECT * FROM eventos WHERE id = $evento_id";
$res_evento = mysqli_query($conexion, $query_evento);
$evento = mysqli_fetch_assoc($res_evento);

if (!$evento) {
    echo "Evento no encontrado.";
    exit();
}

// Acción: Agregar alumno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $usuario_id = intval($_POST['usuario_id']);
    $query_insert = "INSERT INTO participaciones_evento (evento_id, usuario_id, participante_role) VALUES ($evento_id, $usuario_id, 'alumno')";
    mysqli_query($conexion, $query_insert);
    header("Location: ver_evento.php?evento_id=$evento_id");
    exit();
}

// Acción: Eliminar alumno
if (isset($_GET['eliminar']) && isset($_GET['usuario_id'])) {
    $usuario_id = intval($_GET['usuario_id']);
    $query_delete = "DELETE FROM participaciones_evento WHERE evento_id = $evento_id AND usuario_id = $usuario_id";
    mysqli_query($conexion, $query_delete);
    header("Location: ver_evento.php?evento_id=$evento_id");
    exit();
}

// Alumnos del dojo
$query_alumnos = "SELECT * FROM usuarios WHERE dojo_id = $dojo_id AND rol = 3";
$res_alumnos = mysqli_query($conexion, $query_alumnos);

// Alumnos ya inscritos
$query_participantes = "SELECT u.id, u.nombre, u.apellido 
                        FROM participaciones_evento pe
                        INNER JOIN usuarios u ON pe.usuario_id = u.id
                        WHERE pe.evento_id = $evento_id AND u.dojo_id = $dojo_id";
$res_participantes = mysqli_query($conexion, $query_participantes);
$participantes_ids = array_column(mysqli_fetch_all($res_participantes, MYSQLI_ASSOC), 'id');
mysqli_data_seek($res_participantes, 0);

// Participantes de otros dojos
$query_otros_dojos = "SELECT d.id AS dojo_id, d.nombre AS dojo_nombre, u.id AS usuario_id, u.nombre, u.apellido 
                      FROM participaciones_evento pe
                      INNER JOIN usuarios u ON pe.usuario_id = u.id
                      INNER JOIN dojos d ON u.dojo_id = d.id
                      WHERE pe.evento_id = $evento_id AND u.dojo_id != $dojo_id";
$res_otros_dojos = mysqli_query($conexion, $query_otros_dojos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalles del Evento</title>
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

    .btn-outline-light:hover {
      background-color: white;
      color: black;
    }

    select, .list-group-item {
      color: black !important;
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
    <div class="card">
      <h2>📄 Detalles del Evento</h2>
      <p><strong>Nombre:</strong> <?= htmlspecialchars($evento['nombre']) ?></p>
      <p><strong>Fecha:</strong> <?= htmlspecialchars($evento['fecha']) ?></p>
      <p><strong>Descripción:</strong> <?= htmlspecialchars($evento['descripcion']) ?></p>
    </div>

    <?php if (mysqli_num_rows($verif) > 0): ?>
      <div class="card">
        <h4>👤 Agregar Alumno al Evento</h4>
        <form method="post" class="mb-3">
          <input type="hidden" name="accion" value="agregar">
          <div class="row g-2 align-items-center">
            <div class="col-md-6">
              <select name="usuario_id" class="form-select" required>
                <option value="">-- Seleccionar Alumno --</option>
                <?php while($alumno = mysqli_fetch_assoc($res_alumnos)): ?>
                  <?php if (!in_array($alumno['id'], $participantes_ids)): ?>
                    <option value="<?= $alumno['id'] ?>"><?= htmlspecialchars($alumno['nombre'] . " " . $alumno['apellido']) ?></option>
                  <?php endif; ?>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="col-md-auto">
              <button type="submit" class="btn btn-outline-success">➕ Agregar</button>
            </div>
          </div>
        </form>
      </div>

      <div class="card">
        <h4>👥 Alumnos Inscritos</h4>
        <ul class="list-group">
          <?php while($p = mysqli_fetch_assoc($res_participantes)): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <?= htmlspecialchars($p['nombre'] . " " . $p['apellido']) ?>
              <a href="ver_usuario.php?id=<?= $p['id'] ?>" class="btn btn-outline-info btn-sm">👁️ Ver Información</a>
              <a href="ver_evento.php?evento_id=<?= $evento_id ?>&eliminar=1&usuario_id=<?= $p['id'] ?>" class="btn btn-outline-danger btn-sm">🗑️ Eliminar</a>
            </li>
          <?php endwhile; ?>
        </ul>
      </div>

      <div class="card">
        <h4>🏯 Participantes de Otros Dojos</h4>
        <ul class="list-group">
          <?php while($o = mysqli_fetch_assoc($res_otros_dojos)): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <?= htmlspecialchars($o['nombre'] . " " . $o['apellido']) ?> (<?= htmlspecialchars($o['dojo_nombre']) ?>)
              <a href="ver_usuario.php?id=<?= $o['usuario_id'] ?>" class="btn btn-outline-info btn-sm">👁️ Ver Información</a>
            </li>
          <?php endwhile; ?>
        </ul>
      </div>
    <?php else: ?>
      <p class="text-warning mt-3">Tu dojo no está participando en este evento. Primero solicita participación.</p>
    <?php endif; ?>

    <a href="mis_eventos.php" class="btn btn-outline-light mt-4">🔙 Volver</a>
  </div>

</body>
</html>