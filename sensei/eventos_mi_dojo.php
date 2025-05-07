<?php
session_start();
include '../conexion.php';

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

// Validar evento_id
$evento_id = isset($_GET['evento_id']) ? intval($_GET['evento_id']) : 0;
if ($evento_id <= 0) {
    echo "Evento no válido.";
    exit();
}

// Verificar que el evento pertenezca al dojo del sensei
$ver_evento = mysqli_query($conexion, "SELECT * FROM eventos WHERE id = $evento_id AND dojo_id = $dojo_id");
if (mysqli_num_rows($ver_evento) == 0) {
    echo "No tienes permisos para ver este evento.";
    exit();
}

// Dojos inscritos en este evento
$query_dojos = "
    SELECT d.id, d.nombre 
    FROM dojo_evento de
    INNER JOIN dojos d ON d.id = de.dojo_id
    WHERE de.evento_id = $evento_id
";
$res_dojos = mysqli_query($conexion, $query_dojos);

// Alumnos de tu dojo
$alumnos_dojo = mysqli_query($conexion, "SELECT id, nombre, apellido FROM usuarios WHERE dojo_id = $dojo_id AND rol = 3");

// Alumnos ya inscritos al evento
$alumnos_inscritos = mysqli_query($conexion, "
    SELECT u.id, u.nombre, u.apellido 
    FROM participaciones_evento pe
    INNER JOIN usuarios u ON u.id = pe.usuario_id
    WHERE pe.evento_id = $evento_id AND u.dojo_id = $dojo_id
");

// Manejar inscripción de alumno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alumno_id'])) {
    $alumno_id = intval($_POST['alumno_id']);
    $verificar = mysqli_query($conexion, "SELECT * FROM participaciones_evento WHERE evento_id = $evento_id AND usuario_id = $alumno_id");
    if (mysqli_num_rows($verificar) == 0) {
        mysqli_query($conexion, "INSERT INTO participaciones_evento (evento_id, usuario_id, participante_role) VALUES ($evento_id, $alumno_id, 'alumno')");
        header("Location: eventos_mi_dojo.php?evento_id=$evento_id");
        exit();
    }
}

// Manejar eliminación de alumno
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $alumno_id = intval($_GET['eliminar']);
    mysqli_query($conexion, "DELETE FROM participaciones_evento WHERE evento_id = $evento_id AND usuario_id = $alumno_id");
    header("Location: eventos_mi_dojo.php?evento_id=$evento_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Evento - Participantes</title>
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

<div class="sidebar">
    <h4 class="text-center text-white">Sensei Panel</h4>
    <a href="mi_dojo.php">🏯 Mi Dojo</a>
    <a href="mis_eventos.php">📅 Mis Eventos</a>
    <a href="../logout.php" class="text-danger">🚪 Cerrar Sesión</a>
</div>

<div class="main-content">
    <h2>👥 Participantes del Evento</h2>

    <div class="card">
        <h5>Agregar alumno de tu dojo a este evento</h5>
        <form method="POST" class="d-flex">
            <select name="alumno_id" class="form-select me-2" required>
                <option value="">Seleccionar alumno...</option>
                <?php while ($a = mysqli_fetch_assoc($alumnos_dojo)): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre'] . ' ' . $a['apellido']) ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-outline-success">Agregar</button>
        </form>
    </div>

    <div class="card">
        <h5>Alumnos de tu dojo inscritos</h5>
        <ul class="list-group">
            <?php while ($ins = mysqli_fetch_assoc($alumnos_inscritos)): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center bg-dark text-white">
                    <?= htmlspecialchars($ins['nombre'] . ' ' . $ins['apellido']) ?>
                    <div>
                        <a href="ver_usuario.php?id=<?= $ins['id'] ?>" class="btn btn-sm btn-outline-info">Ver Información</a>
                        <a href="eventos_mi_dojo.php?evento_id=<?= $evento_id ?>&eliminar=<?= $ins['id'] ?>" class="btn btn-sm btn-outline-danger">Eliminar</a>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <h5>Otros dojos inscritos</h5>
    <?php while ($dojo = mysqli_fetch_assoc($res_dojos)): ?>
        <div class="card">
            <h6>🏯 <?= htmlspecialchars($dojo['nombre']) ?></h6>
            <ul class="list-group">
                <?php
                $dojo_id_actual = $dojo['id'];
                $usuarios = mysqli_query($conexion, "
                    SELECT u.id, u.nombre, u.apellido 
                    FROM participaciones_evento pe
                    INNER JOIN usuarios u ON u.id = pe.usuario_id
                    WHERE pe.evento_id = $evento_id AND u.dojo_id = $dojo_id_actual
                ");
                while ($u = mysqli_fetch_assoc($usuarios)):
                ?>
                    <li class="list-group-item bg-dark text-white d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?>
                        <a href="ver_usuario.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-info">Ver Información</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    <?php endwhile; ?>

</div>

</body>
</html>