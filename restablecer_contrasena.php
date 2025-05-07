<?php
session_start();
include 'conexion.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificar si el token es válido
    $query = "SELECT * FROM usuarios WHERE token = '$token' AND token_expiracion > NOW()";
    $resultado = mysqli_query($conexion, $query);

    if (mysqli_num_rows($resultado) == 1) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nueva_contrasena = $_POST['nueva_contrasena'];
            $confirmar_contrasena = $_POST['confirmar_contrasena'];

            if ($nueva_contrasena == $confirmar_contrasena) {
                // Encriptar la nueva contraseña
                $hashed_password = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

                // Actualizar la contraseña en la base de datos
                $update_query = "UPDATE usuarios SET password = '$hashed_password', token = NULL, token_expiracion = NULL WHERE token = '$token'";
                if (mysqli_query($conexion, $update_query)) {
                    echo "<div class='alert alert-success text-center'>Contraseña restablecida exitosamente. Puedes iniciar sesión.</div>";
                    header("refresh:3; url=login.php");
                    exit();
                } else {
                    echo "<div class='alert alert-danger text-center'>Hubo un problema al restablecer la contraseña. Intenta nuevamente.</div>";
                }
            } else {
                echo "<div class='alert alert-danger text-center'>Las contraseñas no coinciden.</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger text-center'>El token es inválido o ha expirado.</div>";
    }
} else {
    echo "<div class='alert alert-danger text-center'>No se proporcionó un token válido.</div>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Restablecer Contraseña - Karate Venezuela</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(to right, red, blue); min-height: 100vh;">

<div class="container d-flex justify-content-center align-items-center vh-100">
  <form class="bg-light p-5 rounded" method="POST" style="min-width: 350px;">
    <h2 class="text-center mb-4">Restablecer tu Contraseña</h2>

    <div class="mb-3">
      <label for="nueva_contrasena" class="form-label">Nueva Contraseña</label>
      <input type="password" name="nueva_contrasena" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña</label>
      <input type="password" name="confirmar_contrasena" class="form-control" required>
    </div>

    <div class="d-grid">
      <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
    </div>
  </form>
</div>

</body>
</html>
