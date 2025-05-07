<!-- filepath: c:\xampp\htdocs\Karate\register.php -->
<?php
session_start();
include 'conexion.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $fecha_nacimiento = $_POST['fecha_nacimiento']; // Capturar la fecha de nacimiento
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validar contraseñas
    if ($password !== $confirm_password) {
        $mensaje = "<div class='alert alert-danger'>Las contraseñas no coinciden.</div>";
    } else {
        // Verificar si ya existe ese correo
        $query = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensaje = "<div class='alert alert-danger'>El correo ya está registrado.</div>";
        } else {
            // Encriptar la contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insertar nuevo usuario como alumno (rol = 3)
            $insert_query = "INSERT INTO usuarios (nombre, apellido, email, fecha_nacimiento, password, rol)
                             VALUES (?, ?, ?, ?, ?, 3)";
            $insert_stmt = $conexion->prepare($insert_query);
            $insert_stmt->bind_param("sssss", $nombre, $apellido, $correo, $fecha_nacimiento, $hashed_password);

            if ($insert_stmt->execute()) {
                $_SESSION['mensaje_exito'] = "Usuario registrado correctamente. Ya puedes iniciar sesión.";
                header("Location: login.php");
                exit();
            } else {
                $mensaje = "<div class='alert alert-danger'>Error al registrar el usuario. Intenta nuevamente.</div>";
            }
        }

        // Cerrar consultas
        $stmt->close();
        if (isset($insert_stmt)) $insert_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registro - Karate Venezuela</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(to right, red, blue); min-height: 100vh;">

<div class="container d-flex justify-content-center align-items-center vh-100">
  <form class="bg-light p-5 rounded" style="min-width: 350px;" action="register.php" method="POST">
    <h2 class="text-center mb-4">Registro</h2>

    <?= $mensaje ?>

    <!-- Nombre -->
    <div class="mb-3">
      <label for="nombre" class="form-label">Nombre</label>
      <input type="text" class="form-control" id="nombre" name="nombre" required>
    </div>

    <!-- Apellido -->
    <div class="mb-3">
      <label for="apellido" class="form-label">Apellido</label>
      <input type="text" class="form-control" id="apellido" name="apellido" required>
    </div>

    <!-- Correo -->
    <div class="mb-3">
      <label for="correo" class="form-label">Correo Electrónico</label>
      <input type="email" class="form-control" id="correo" name="correo" required>
    </div>

    <!-- Fecha de Nacimiento -->
    <div class="mb-3">
      <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
      <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
    </div>

    <!-- Contraseña -->
    <div class="mb-3">
      <label for="password" class="form-label">Contraseña</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>

    <!-- Confirmar Contraseña -->
    <div class="mb-3">
      <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
      <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
    </div>

    <!-- Botón -->
    <div class="d-grid">
      <button type="submit" class="btn btn-primary">Registrar</button>
    </div>
  </form>
</div>

</body>
</html>