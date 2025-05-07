<?php
session_start();
include 'conexion.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Validar si existe el correo
    $consulta = $conexion->prepare("SELECT * FROM usuarios WHERE email = ?");
    $consulta->bind_param("s", $email);
    $consulta->execute();
    $resultado = $consulta->get_result();

    if ($resultado->num_rows === 1) {
        // Generar un código de recuperación
        $codigo = rand(100000, 999999); // Código aleatorio
        $_SESSION['codigo_recuperacion'] = $codigo;
        $_SESSION['email_recuperacion'] = $email;

        // Enviar correo (usa mail simple si no tienes PHPMailer configurado)
        $asunto = "Código de recuperación - Karate Venezuela";
        $mensaje_correo = "Tu código de recuperación es: $codigo";
        $cabeceras = "From: no-reply@karatevzla.com";

        // Intentar enviar el correo (puedes usar PHPMailer si lo tienes configurado)
        if (mail($email, $asunto, $mensaje_correo, $cabeceras)) {
            header("Location: verificar_codigo.php"); // Redirigir a la página de verificación del código
            exit;
        } else {
            $mensaje = "No se pudo enviar el correo. Intenta nuevamente.";
        }
    } else {
        $mensaje = "El correo electrónico no está registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(to right, orange, purple); min-height: 100vh;">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <form class="bg-light p-5 rounded shadow" method="POST">
        <h3 class="text-center mb-4">¿Olvidaste tu contraseña?</h3>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-danger text-center"><?= $mensaje ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="email" class="form-label">Ingresa tu correo electrónico</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Enviar Código</button>
        </div>

        <div class="text-center mt-3">
            <a href="login.php">Volver al login</a>
        </div>
    </form>
</div>

</body>
</html>
