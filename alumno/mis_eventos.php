<?php
session_start();
include '../conexion.php';

// Verificar si el usuario está logueado y es alumno
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 3) {
    header("Location: ../login.php");
    exit();
}

$id_alumno = $_SESSION['id_usuario'];

// Obtener los eventos asignados al alumno
$query_eventos = "
    SELECT e.id AS evento_id, e.nombre AS evento_nombre, e.fecha AS evento_fecha, d.nombre AS dojo_nombre
    FROM participaciones_evento pe
    INNER JOIN eventos e ON pe.evento_id = e.id
    INNER JOIN dojos d ON e.dojo_id = d.id
    WHERE pe.usuario_id = $id_alumno
";
$res_eventos = mysqli_query($conexion, $query_eventos);

// Verificar si el alumno tiene eventos asignados
if (mysqli_num_rows($res_eventos) == 0) {
    // Si no tiene eventos asignados, mostrar el mensaje con la interfaz gráfica
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
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            @keyframes gradientBG {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            .card {
                background-color: rgba(0, 0, 0, 0.7);
                color: white;
                padding: 20px;
                border-radius: 10px;
                margin-bottom: 20px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.3);
                width: 80%;
                text-align: center;
            }

            .btn-outline-light:hover {
                background-color: white;
                color: black;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <h2 class="text-danger">Eventos no asignados.</h2>
        </div>
        <a href="dashboard_alumno.php" class="btn btn-outline-light mt-3">Volver al Inicio</a>
    </body>
    </html>
    <?php
    exit();
}
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

        .btn-outline-light:hover {
            background-color: white;
            color: black;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h4 class="text-center text-white">Alumno Panel</h4>
    <a href="dashboard_alumno.php">🏠 Inicio</a>
    <a href="../logout.php" class="text-danger">🚪 Cerrar Sesión</a>
</div>

<div class="main-content">
    <h2>📅 Mis Eventos</h2>
    <?php while ($evento = mysqli_fetch_assoc($res_eventos)): ?>
        <div class="card">
            <h4><?= htmlspecialchars($evento['evento_nombre']) ?></h4>
            <p><strong>Fecha:</strong> <?= htmlspecialchars($evento['evento_fecha']) ?></p>
            <p><strong>Dojo:</strong> <?= htmlspecialchars($evento['dojo_nombre']) ?></p>
            <a href="ver_evento.php?evento_id=<?= $evento['evento_id'] ?>" class="btn btn-outline-light">Ver Detalles</a>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>