<?php
session_start();
include '../conexion.php';

// Verificar si el usuario está logueado y es alumno
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 3) {
    header("Location: ../login.php");
    exit();
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_SESSION['id_usuario'];
    $certificado = $_FILES['certificado'];

    // Validar y mover el archivo subido
    $upload_dir = '../uploads/';
    
    // Asegurarse de que la carpeta exista
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Crear la carpeta
    }

    $file_name = time() . '_' . basename($certificado['name']); // Evitar nombres duplicados
    $target_file = $upload_dir . $file_name;

    if (move_uploaded_file($certificado['tmp_name'], $target_file)) {
        // Insertar la solicitud en la base de datos con la ruta del archivo
        $query = "INSERT INTO solicitudes (usuario_id, fecha, estado, imagen) VALUES ($usuario_id, NOW(), 0, '$target_file')";
        if (mysqli_query($conexion, $query)) {
            $mensaje = "✅ Solicitud enviada correctamente.";
        } else {
            $mensaje = "❌ Error al enviar la solicitud.";
        }
    } else {
        $mensaje = "❌ Error al subir el archivo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Enviar Solicitud</title>
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
            justify-content: center;
            align-items: center;
        }

        .card {
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            width: 400px;
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

        .btn-secondary {
            margin-top: 10px;
            background-color: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
        }

        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.4);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center text-white">Alumno Panel</h4>
        <a href="mi_dojo.php">📚 Mi Dojo</a>
        <a href="mis_eventos.php">📅 Mis Eventos</a>
        <a href="procesar_solicitud.php">📨 Solicitudes</a>
        <a href="../logout.php" class="text-danger">🚪 Cerrar Sesión</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="card">
            <h2 class="text-center">Enviar Solicitud</h2>
            <?php if ($mensaje): ?>
                <div class="alert alert-info"><?= $mensaje ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="certificado" class="form-label">Subir Certificado</label>
                    <input type="file" class="form-control" name="certificado" id="certificado" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Enviar Solicitud</button>
            </form>
            <a href="dashboard_alumno.php" class="btn btn-secondary w-100">Regresar</a>
        </div>
    </div>
</body>
</html>