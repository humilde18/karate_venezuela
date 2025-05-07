<?php
session_start();
include '../conexion.php';

// Verificar si el usuario está logueado y es admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Procesar actualización de datos si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_alumno'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $kyu = $_POST['kyu'];

    $query_actualizar = "UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, kyu = ? WHERE id = ?";
    $stmt = $conexion->prepare($query_actualizar);
    $stmt->bind_param('sssii', $nombre, $apellido, $email, $kyu, $id);

    if ($stmt->execute()) {
        $mensaje = "Alumno actualizado correctamente.";
    } else {
        $mensaje = "Error al actualizar el alumno.";
    }
}

// Obtener lista de alumnos (incluyendo el nivel de Kyu actualizado)
$query = "SELECT usuarios.*, kyu.nombre AS nombre_kyu FROM usuarios 
          LEFT JOIN kyu ON usuarios.kyu = kyu.id 
          WHERE usuarios.rol = 3"; // Asumiendo que el rol 3 corresponde a alumnos
$resultado = mysqli_query($conexion, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Alumnos</title>
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
    <h1>Alumnos Registrados</h1>
    <p>A continuación se muestra la lista de usuarios con rol de alumno.</p>

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
            <th>Kyu</th> <!-- Nueva columna para Kyu -->
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
                <?= isset($row['nombre_kyu']) ? htmlspecialchars($row['nombre_kyu']) : 'Sin nivel' ?>
              </td> <!-- Mostrar el nivel de Kyu -->
              <td>
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar" onclick="llenarFormulario(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nombre']) ?>', '<?= htmlspecialchars($row['apellido']) ?>', '<?= htmlspecialchars($row['email']) ?>', <?= $row['kyu'] ?? 'null' ?>)">Editar</button>
                <a href="eliminar_alumno.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este alumno?');">Eliminar</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal para editar alumno -->
  <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-black" id="modalEditarLabel">Editar Alumno</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST">
          <div class="modal-body">
            <input type="hidden" name="id" id="editar-id">
            <div class="mb-3">
              <label for="editar-nombre" class="text-black">Nombre</label>
              <input type="text" class="form-control" id="editar-nombre" name="nombre" required>
            </div>
            <div class="mb-3">
              <label for="editar-apellido" class="text-black">Apellido</label>
              <input type="text" class="form-control" id="editar-apellido" name="apellido" required>
            </div>
            <div class="mb-3">
              <label for="editar-email" class="text-black">Email</label>
              <input type="email" class="form-control" id="editar-email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="editar-kyu" class="text-black">Nivel de Kyu</label>
              <select class="form-control" id="editar-kyu" name="kyu" required>
              <option value="">Seleccione un nivel</option>
                <?php
                // Obtener niveles de Kyu desde la base de datos
                $query_kyu = "SELECT id, nombre FROM kyu";
                $resultado_kyu = mysqli_query($conexion, $query_kyu);
                while ($row_kyu = mysqli_fetch_assoc($resultado_kyu)) {
                    echo "<option value='{$row_kyu['id']}'>{$row_kyu['nombre']}</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success" name="editar_alumno">Guardar Cambios</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function llenarFormulario(id, nombre, apellido, email, kyu) {
      document.getElementById('editar-id').value = id;
      document.getElementById('editar-nombre').value = nombre;
      document.getElementById('editar-apellido').value = apellido;
      document.getElementById('editar-email').value = email;
      document.getElementById('editar-kyu').value = kyu;
    }
  </script>

</body>
</html>