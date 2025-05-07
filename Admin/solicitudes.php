<!-- filepath: c:\xampp\htdocs\Karate\Admin\solicitudes.php -->
<?php
session_start();
include '../conexion.php';

// Verificar si el usuario está logueado y es admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Consulta SQL para obtener las solicitudes
$resultado_solicitudes = mysqli_query($conexion, "
  SELECT s.id AS id_solicitud, s.fecha, s.estado, s.imagen,
         u.id AS id_usuario, u.nombre, u.apellido, u.email
  FROM solicitudes s
  LEFT JOIN usuarios u ON s.usuario_id = u.id
");

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Solicitudes</title>
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
      flex-direction: column;
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
      height: 100%;
    }

    .container {
      background-color: #1e3c72;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
      color: white;
    }

    h1, h2, p {
      text-shadow: 1px 1px 3px black;
    }

    table {
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
      border-radius: 10px;
      overflow: hidden;
    }

    table th, table td {
      vertical-align: middle !important;
      padding: 15px;
      text-align: center;
    }

    table th {
      background-color: #444;
    }

    .btn-success {
      background-color: rgb(255, 187, 0);
      border: none;
      transition: background 0.3s;
      color: black;
    }

    .btn-success:hover {
      background-color: rgb(250, 198, 56);
      color: black;
    }

    .btn-danger {
      background-color: #e52d27;
      border: none;
      transition: background 0.3s;
    }

    .btn-danger:hover {
      background-color: #b31217;
    }

    .btn-primary {
      background-color: #007bff;
      border: none;
      transition: background 0.3s;
    }

    .btn-primary:hover {
      background-color: #0056b3;
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
    <a href="../index.php" class="text-danger">🚪 Cerrar Sesión</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="container">
      <h2 class="mb-4">Lista de Solicitudes</h2>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Fecha Solicitud</th>
            <th>Estado</th>
            <th>Certificado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($solicitud = mysqli_fetch_assoc($resultado_solicitudes)) { ?>
            <tr>
              <td><?php echo htmlspecialchars($solicitud['nombre'] . ' ' . $solicitud['apellido']); ?></td>
              <td><?php echo htmlspecialchars($solicitud['email']); ?></td>
              <td><?php echo htmlspecialchars($solicitud['fecha']); ?></td>
              <td>
                <?php
                  if ($solicitud['estado'] == 1) {
                    echo 'Aprobada';
                  } elseif ($solicitud['estado'] == 2) {
                    echo 'Rechazada';
                  } else {
                    echo 'Pendiente';
                  }
                ?>
              </td>
              <td>
                <?php if (!empty($solicitud['imagen'])) { ?>
                  <a href="http://localhost/Karate/<?php echo htmlspecialchars($solicitud['imagen']); ?>" download="<?php echo basename($solicitud['imagen']); ?>" class="btn btn-primary">Descargar Certificado</a>
                <?php } else { ?>
                  <span class="text-muted">Sin certificado</span>
                <?php } ?>
              </td>
              <td>
                <?php if ($solicitud['estado'] == 0) { ?>
                  <a href="aprobar_solicitud.php?id=<?php echo $solicitud['id_solicitud']; ?>" class="btn btn-success">Aprobar</a>
                  <a href="rechazar_solicitud.php?id=<?php echo $solicitud['id_solicitud']; ?>" class="btn btn-danger">Rechazar</a>
                <?php } else { ?>
                  <span class="text-muted">Procesada</span>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>