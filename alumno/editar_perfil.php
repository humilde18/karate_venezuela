<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 3) {
    header("Location: ../login.php");
    exit();
}

include '../conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$sql = "SELECT nombre, email FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Perfil Alumno</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="container">
    <h2>Editar Perfil</h2>
    <form action="procesar_editar_perfil.php" method="POST">
      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($usuario['nombre']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Correo Electrónico</label>
        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($usuario['email']) ?>">
      </div>
      <button type="submit" class="btn btn-primary">Guardar cambios</button>
      <a href="dashboard_alumno.php" class="btn btn-secondary">Cancelar</a>
    </form>
  </div>
</body>
</html>
