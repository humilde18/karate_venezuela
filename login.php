<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $mensaje_error = "Todos los campos son obligatorios.";
    } else {
        $query = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            if (password_verify($password, $usuario['password'])) {
                $_SESSION['id_usuario'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol'];

                // Redirigir según el rol
                if ($usuario['rol'] == 1) {
                    header('Location: admin/dashboard_admin.php');
                } elseif ($usuario['rol'] == 2) {
                    header('Location: sensei/dashboard_sensei.php');
                } elseif ($usuario['rol'] == 3) {
                    header('Location: alumno/dashboard_alumno.php');
                } else {
                    header('Location: perfil.php'); // En caso de que no tenga rol definido
                }
                exit();
            } else {
                $mensaje_error = "Contraseña incorrecta.";
            }
        } else {
            $mensaje_error = "Correo no registrado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Karate Venezuela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(to right, red, blue); min-height: 100vh;">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <form class="bg-light p-5 rounded shadow" method="POST" style="min-width: 350px;">
        <h2 class="text-center mb-4">Iniciar Sesión</h2>

        <?php if (isset($mensaje_error)): ?>
            <div class="alert alert-danger text-center"><?= $mensaje_error ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Ingresar</button>
        </div>

        <div class="text-center mt-3">
            <a href="register.php">¿No tienes una cuenta? Regístrate</a><br>
            <a href="olvidaste_contraseña.php">¿Olvidaste tu contraseña?</a>
        </div>
    </form>
</div>

</body>
</html>
