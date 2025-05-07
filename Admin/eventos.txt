<?php
session_start();
include '../conexion.php';

// Verifica si es admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Obtener el evento seleccionado
if (isset($_GET['id'])) {
    $evento_id = $_GET['id'];
    $stmt = $conexion->prepare("SELECT * FROM eventos WHERE id = ?");
    $stmt->bind_param("i", $evento_id);
    $stmt->execute();
    $evento = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Si no existe el evento
    if (!$evento) {
        echo "<p class='text-danger'>Evento no encontrado.</p>";
        exit();
    }
} else {
    echo "<p class='text-danger'>ID de evento no válido.</p>";
    exit();
}

// Obtener los participantes con dojo y sensei, ordenados por dojo y alumno
$stmt = $conexion->prepare("
    SELECT u.nombre AS alumno_nombre, u.apellido AS alumno_apellido,
           d.nombre AS dojo_nombre,
           s.nombre AS sensei_nombre, s.apellido AS sensei_apellido
    FROM participaciones_evento p
    JOIN usuarios u ON p.usuario_id = u.id
    JOIN dojos d ON u.dojo_id = d.id
    JOIN usuarios s ON d.sensei_id = s.id
    WHERE p.evento_id = ?
    ORDER BY d.nombre, u.apellido
");
$stmt->bind_param("i", $evento_id);
$stmt->execute();
$resultado = $stmt->get_result();
$stmt->close();

// Agrupar participantes por dojo
$agrupado = [];
while ($row = $resultado->fetch_assoc()) {
    $clave = $row['dojo_nombre'] . '|' . $row['sensei_nombre'] . ' ' . $row['sensei_apellido'];
    $agrupado[$clave][] = [
        'alumno_nombre' => $row['alumno_nombre'],
        'alumno_apellido' => $row['alumno_apellido']
    ];
}
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

        .dojo-card {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .dojo-card h3 {
            color: #ffbb00;
            text-shadow: 1px 1px 3px black;
        }

        .dojo-card ul {
            list-style: none;
            padding: 0;
        }

        .dojo-card ul li {
            margin-bottom: 5px;
        }

        .btn-primary {
            background-color: rgb(255, 187, 0);
            border: none;
            transition: background 0.3s;
            color: black;
        }

        .btn-primary:hover {
            background-color: rgb(250, 198, 56);
            color: black;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Detalles del Evento</h1>
    <div class="mb-4">
        <h2><?= htmlspecialchars($evento['nombre']) ?></h2>
        <p><strong>Descripción:</strong> <?= htmlspecialchars($evento['descripcion']) ?></p>
        <p><strong>Fecha:</strong> <?= htmlspecialchars($evento['fecha']) ?></p>
        <p><strong>Lugar:</strong> <?= htmlspecialchars($evento['lugar']) ?></p>
    </div>

    <h2>Participantes por Dojo</h2>
    <?php if (!empty($agrupado)): ?>
        <?php foreach ($agrupado as $clave => $alumnos): 
            [$dojo_nombre, $sensei_completo] = explode('|', $clave);
        ?>
            <div class="dojo-card">
                <h3>🏯 <?= htmlspecialchars($dojo_nombre) ?></h3>
                <p><strong>Sensei:</strong> <?= htmlspecialchars($sensei_completo) ?></p>
                <h4>Alumnos Participantes:</h4>
                <ul>
                    <?php foreach ($alumnos as $alumno): ?>
                        <li>🥋 <?= htmlspecialchars($alumno['alumno_nombre'] . ' ' . $alumno['alumno_apellido']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay participantes registrados en este evento.</p>
    <?php endif; ?>

    <a href="eventos.php" class="btn btn-primary mt-4">Regresar a la lista de eventos</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>