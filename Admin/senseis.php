<?php
session_start();
include '../conexion.php';

// Verificar si el usuario está logueado y es admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Procesar actualización de datos si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_sensei'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $dan = $_POST['dan'];

    $query_actualizar = "UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, dan = ? WHERE id = ?";
    $stmt = $conexion->prepare($query_actualizar);
    $stmt->bind_param('sssii', $nombre, $apellido, $email, $dan, $id);

    if ($stmt->execute()) {
        $mensaje = "Sensei actualizado correctamente.";
    } else {
        $mensaje = "Error al actualizar el sensei.";
    }
}

// Obtener lista de senseis (incluyendo el nivel de Dan actualizado)
$query = "SELECT usuarios.*, dan.nivel AS nivel_dan FROM usuarios 
          LEFT JOIN dan ON usuarios.dan = dan.id 
          WHERE usuarios.rol = 2";
$resultado = mysqli_query($conexion, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Senseis</title>
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

    h1, p {
      text-shadow: 1px 1px 3px black;
    }

    .bg-white-container {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .table th, .table td {
      color: black;
      vertical-align: middle;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="text-center text-white">Admin Panel</h4>
    <a href="dashboard_admin.php">🏠 Inicio</a>
    <a href="editar_dojos.php">🏯 Dojos</a>
    <a href="senseis.php">🥋 Senseis</a>
    <a href="alumnos.php">👦 Alumnos</a>
    <a href="eventos.php">📅 Eventos</a>
    <a href="solicitudes.php">📨 Solicitudes</a>
    <a href="../index.php" class="text-danger">🚪 Cerrar Sesión</a> <!-- Modificado -->
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Senseis Registrados</h1>
    <p>A continuación se muestra la lista de usuarios con rol de sensei.</p>

    <?php if (isset($mensaje)) : ?>
      <div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <div class="bg-white-container">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Email</th>
            <th>Dan</th> <!-- Nueva columna para Dan -->
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($resultado)) : ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['nombre']) ?></td>
              <td><?= htmlspecialchars($row['apellido']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td>
                <?= isset($row['nivel_dan']) ? "Dan {$row['nivel_dan']}" : 'Sin nivel' ?>
              </td> <!-- Mostrar el nivel de Dan -->
              <td>
                <a href="ver_usuario.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Ver</a>
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar" onclick="llenarFormulario(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nombre']) ?>', '<?= htmlspecialchars($row['apellido']) ?>', '<?= htmlspecialchars($row['email']) ?>', <?= $row['dan'] ?? 'null' ?>)">Editar</button>
                <a href="eliminar_sensei.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este sensei?');">Eliminar</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal para editar sensei -->
  <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title text-black" id="modalEditarLabel">Editar Sensei</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST">
          <div class="modal-body">
            <input type="hidden" name="id" id="editar-id">
            <div class="mb-3">
              <label for="editar-nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="editar-nombre" name="nombre" required>
            </div>
            <div class="mb-3">
              <label for="editar-apellido" class="form-label">Apellido</label>
              <input type="text" class="form-control" id="editar-apellido" name="apellido" required>
            </div>
            <div class="mb-3">
              <label for="editar-email" class="form-label">Email</label>
              <input type="email" class="form-control" id="editar-email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="editar-dan" class="form-label">Nivel de Dan</label>
              <select class="form-control" id="editar-dan" name="dan" required>
                <option value="">Seleccione un nivel</option>
                <?php
                // Obtener niveles de Dan desde la base de datos
                $query_dan = "SELECT id, nivel FROM dan";
                $resultado_dan = mysqli_query($conexion, $query_dan);
                while ($row_dan = mysqli_fetch_assoc($resultado_dan)) {
                    echo "<option value='{$row_dan['id']}'>Dan {$row_dan['nivel']}</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success" name="editar_sensei">Guardar Cambios</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function llenarFormulario(id, nombre, apellido, email, dan) {
      document.getElementById('editar-id').value = id;
      document.getElementById('editar-nombre').value = nombre;
      document.getElementById('editar-apellido').value = apellido;
      document.getElementById('editar-email').value = email;
      document.getElementById('editar-dan').value = dan;
    }
  </script>

</body>
</html>