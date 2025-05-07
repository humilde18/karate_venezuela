<?php
include '../conexion.php';

$id_usuario = $_GET['id'];

// Obtener datos del usuario y del dojo
$sql = "SELECT u.*, d.nombre AS dojo_nombre 
        FROM usuarios u 
        LEFT JOIN dojos d ON u.dojo_id = d.id 
        WHERE u.id = $id_usuario";

$resultado = mysqli_query($conexion, $sql);
$usuario = mysqli_fetch_assoc($resultado);

// Calcular edad si existe fecha de nacimiento
$edad = '';
if (!empty($usuario['fecha_nacimiento'])) {
    $fecha_nac = new DateTime($usuario['fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nac)->y;
}

// Contar participaciones en eventos
$sql_contar = "SELECT COUNT(*) AS total FROM participaciones WHERE usuario_id = $id_usuario";
$res_contar = mysqli_query($conexion, $sql_contar);
$fila = mysqli_fetch_assoc($res_contar);
$participaciones = $fila['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            background: linear-gradient(-45deg, #1e3c72, #2a5298, #e52d27, #b31217);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: white;
            min-height: 100vh;
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
            max-width: 600px;
            width: 90%;
        }

        .card-header {
            background-color: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            padding: 10px;
        }

        .card-body {
            padding: 20px;
        }

        .card-body p {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .card-body p strong {
            color: #ffbb00;
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

        .btn-back {
            display: block;
            margin: 20px auto 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            Perfil de Usuario
        </div>
        <div class="card-body">
            <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?></p>
            <p><strong>Apellido:</strong> <?= htmlspecialchars($usuario['apellido']) ?></p>
            <p><strong>Dojo:</strong> <?= htmlspecialchars($usuario['dojo_nombre']) ?></p>
            <p><strong>Fecha de nacimiento:</strong> <?= htmlspecialchars($usuario['fecha_nacimiento']) ?></p>
            <p><strong>Edad:</strong> <?= $edad ?> años</p>
            <p><strong>Participaciones en eventos:</strong> <?= $participaciones ?></p>

            <?php if ($usuario['rol'] == 2): ?>
                <p><strong>DAN:</strong> <?= htmlspecialchars($usuario['dan']) ?></p>
            <?php elseif ($usuario['rol'] == 3): ?>
                <p><strong>KYU:</strong> <?= htmlspecialchars($usuario['kyu']) ?></p>
            <?php endif; ?>
        </div>
        <a href="mi_dojo.php" class="btn btn-primary btn-back">Volver a Mi Dojo</a>
    </div>
</body>
</html>