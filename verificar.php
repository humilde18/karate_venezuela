<?php
session_start();

// Mostrar errores (solo desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Si no se ha establecido el código, redirigir a la página de recuperación
if (!isset($_SESSION['codigo_recuperacion'])) {
    header("Location: olvido_contraseña.php"); // Redirigir al archivo olvido_contraseña.php
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_ingresado = trim($_POST['codigo']);

    if ($codigo_ingresado == $_SESSION['codigo_recuperacion']) {
        header("Location: cambiar_contraseña.php"); // Redirigir a cambiar la contraseña
        exit;
    } else {
        $mensaje = "El código ingresado es incorrecto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Verificar Código</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(to right, orange, purple); min-height: 100vh;">

<div class="container d-flex justify-content-center align-items-center vh-100">
  <form class="bg-light p-5 rounded shadow" method="POST">
    <h3 class="text-center mb-4">Verifica tu Código</h3>

    <?php if (!empty($mensaje)): ?>
      <div class="alert alert-danger text-center"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="mb-3">
      <label for="codigo" class="form-label">Ingresa el código de recuperación</label>
      <input type="text" name="codigo" class="form-control" required>
    </div>

    <div class="d-grid">
      <button type="submit" class="btn btn-primary">Verificar Código</button>
    </div>

    <div class="text-center mt-3">
      <a href="login.php">Volver al login</a>
    </div>
  </form>
</div>

</body>
</html>
