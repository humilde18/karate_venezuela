<?php
session_start();
include '../conexion.php';

// Verificar si el usuario está logueado y es alumno
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 3) {
    header("Location: ../login.php");
    exit();
}

$id_alumno = $_SESSION['id_usuario'];

// Obtener el dojo del alumno
$query_dojo = "SELECT d.id AS dojo_id, d.nombre AS dojo_nombre 
               FROM usuarios u 
               LEFT JOIN dojos d ON u.dojo_id = d.id 
               WHERE u.id = $id_alumno";
$res_dojo = mysqli_query($conexion, $query_dojo);
$dojo = mysqli_fetch_assoc($res_dojo);

if (!$dojo || !$dojo['dojo_id']) {
    // Si el alumno no está asignado a un dojo, mostrar el mensaje con la interfaz gráfica
    ?>
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
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
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
        <div class="sidebar">
            <h4 class="text-center text-white">Alumno Panel</h4>
            <a href="dashboard_alumno.php">🏠 Inicio</a>
            <a href="../logout.php" class="text-danger">🚪 Cerrar Sesión</a>
        </div>
        <div class="main-content">
            <div class="card">
                <h2 class="text-danger">Este usuario no está asignado a un dojo.</h2>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

$dojo_id = $dojo['dojo_id'];

// Obtener los senseis del dojo
$query_senseis = "SELECT id, nombre, apellido 
                  FROM usuarios 
                  WHERE dojo_id = $dojo_id AND rol = 2";
$res_senseis = mysqli_query($conexion, $query_senseis);

// Obtener los compañeros (otros alumnos del dojo)
$query_companeros = "SELECT id, nombre, apellido 
                     FROM usuarios 
                     WHERE dojo_id = $dojo_id AND rol = 3 AND id != $id_alumno";
$res_companeros = mysqli_query($conexion, $query_companeros);
?>

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

        .card {
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            width: 80%;
        }

        .btn-outline-light:hover {
            background-color: white;
            color: black;
        }

        .list-group-item {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .list-group-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
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
    <div class="card">
        <h2 class="text-center">🏯 Mi Dojo</h2>
        <h4 class="text-center"><?= htmlspecialchars($dojo['dojo_nombre']) ?></h4>
    </div>

    <div class="card">
        <h4>👨‍🏫 Senseis</h4>
        <ul class="list-group">
            <?php while ($sensei = mysqli_fetch_assoc($res_senseis)): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($sensei['nombre'] . ' ' . $sensei['apellido']) ?>
                    <a href="ver_usuario.php?id=<?= $sensei['id'] ?>" class="btn btn-sm btn-outline-info">Ver Información</a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <div class="card">
        <h4>👥 Compañeros</h4>
        <ul class="list-group">
            <?php while ($companero = mysqli_fetch_assoc($res_companeros)): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($companero['nombre'] . ' ' . $companero['apellido']) ?>
                    <a href="ver_usuario.php?id=<?= $companero['id'] ?>" class="btn btn-sm btn-outline-info">Ver Información</a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>

</body>
</html>