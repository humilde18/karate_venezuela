<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Asociación de Karate Barinas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Estilos personalizados */
    body {
      font-family: Arial, sans-serif;
    }

    /* Redondear esquinas de botones */
    .btn {
      border-radius: 10px;
    }

    /* Estilo amigable para los botones de navegación */
    .navbar-nav .nav-link {
      color: #ffffff !important; /* Texto blanco */
      background-color:rgb(184, 43, 78)
      padding: 10px 15px;
      border-radius: 10px;
      transition: background-color 0.3s ease;
    }

    .navbar-nav .nav-link:hover {
      background-color:rgb(184, 43, 78); /* Verde más oscuro al pasar el mouse */
    }

    /* Redondear esquinas de todas las secciones */
    section {
      border-radius: 10px;
      overflow: hidden; /* Asegura que el contenido no sobresalga de las esquinas redondeadas */
    }

    /* Redondear esquinas de la barra de navegación */
    .navbar {
      background-color: #343a40;
      border-radius: 10px;
    }

    .navbar-brand {
      font-size: 1.5rem;
      font-weight: bold;
      color: #fff;
    }

    /* Redondear esquinas de los elementos del acordeón (FAQ) */
    .accordion-item {
      border-radius: 10px;
    }

    .hero-section {
      background: linear-gradient(270deg, #004aad, rgb(134, 26, 26), #004aad);
      background-size: 600% 600%;
      animation: gradientAnimation 10s ease infinite;
      color: white;
      padding: 100px 20px;
    }

    @keyframes gradientAnimation {
      0% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
      100% {
        background-position: 0% 50%;
      }
    }

    .hero-section .content {
      max-width: 50%;
    }

    .hero-section h1 {
      font-size: 2.5rem;
      font-weight: bold;
    }

    .hero-section p {
      font-size: 1.2rem;
      margin-top: 20px;
    }

    .hero-section .btn-primary {
      background-color: #ff3366;
      border: none;
      padding: 10px 20px;
      font-size: 1rem;
      margin-top: 20px;
    }

    .hero-section .image-container {
      max-width: 50%;
    }

    .hero-section img {
      max-width: 100%;
      border-radius: 10px;
    }

    .features-section {
      padding: 40px 100px;
      background-color: rgb(255, 255, 255);
    }

    .features-section h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    .features-section .feature {
      text-align: center;
      padding: 20px;
    }

    .features-section .feature img {
      max-width: 80px;
      margin-bottom: 15px;
    }

    .testimonials-section {
      background-color: #fff;
      padding: 60px 20px;
    }

    .testimonials-section h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    .testimonials-section .testimonial {
      text-align: center;
      margin-bottom: 30px;
    }

    .testimonials-section img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      margin-bottom: 15px;
    }

    .faq-section {
      background-color: #f8f9fa;
      padding: 60px 20px;
    }

    .faq-section h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    .cta-section {
      background-color: #004aad;
      color: white;
      padding: 60px 20px;
      text-align: center;
    }

    footer {
      background-color: #343a40;
      color: white;
      padding: 20px 0;
      text-align: center;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark" style="padding: 1.5rem 1rem;">
    <div class="container">
      <a class="navbar-brand" href="#">Karate</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" href="#">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="register.php">Registrarse</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="login.php">Iniciar Sesión</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero-section d-flex align-items-center">
    <div class="container d-flex justify-content-between">
      <div class="content">
        <h1>Todo lo que necesitas para optimizar tu entrenamiento</h1>
        <p>
          Descubre cómo nuestro sistema puede ayudarte a mejorar tu rendimiento físico y mental, mientras participas en competencias locales y nacionales.
        </p>
        <a href="register.php" class="btn btn-primary">Conoce más</a>
      </div>
      <div class="image-container">
        <img src="assest/img/karate-do.jpg" alt="Karate">
      </div>
    </div>
  </section>

 

  <!-- Testimonials Section -->
  <section class="testimonials-section">
    <div class="container">
      <h2>Lo que dicen nuestros usuarios</h2>
      <div class="row">
        <div class="col-md-4 testimonial">
          <img src="assest/img/karate-douglas-arawaza-brasil.jpg" alt="Usuario 1">
          <p>"Gracias a este sistema, he mejorado mi técnica y he ganado confianza en competencias."</p>
          <h5>- Juan Pérez</h5>
        </div>
        <div class="col-md-4 testimonial">
          <img src="assest/img/karate-douglas-arawaza-brasil.jpg" alt="Usuario 2">
          <p>"La disciplina y los valores que he aprendido aquí son invaluables."</p>
          <h5>- María López</h5>
        </div>
        <div class="col-md-4 testimonial">
          <img src="assest/img/karate-douglas-arawaza-brasil.jpg" alt="Usuario 3">
          <p>"Un sistema completo que me ha ayudado a alcanzar mis metas."</p>
          <h5>- Carlos García</h5>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ Section -->
  <section class="faq-section">
    <div class="container">
      <h2>Preguntas Frecuentes</h2>
      <div class="accordion" id="faqAccordion">
        <div class="accordion-item">
          <h2 class="accordion-header" id="faq1">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
              ¿Cómo puedo registrarme?
            </button>
          </h2>
          <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Puedes registrarte haciendo clic en el botón "Registrarse" en la parte superior de la página.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="faq2">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
              ¿Qué beneficios obtengo al usar este sistema?
            </button>
          </h2>
          <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Obtendrás acceso a entrenamientos personalizados, competencias y formación en valores.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="faq3">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
              ¿Es necesario tener experiencia previa en karate?
            </button>
          </h2>
          <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              No, nuestro sistema está diseñado para todos los niveles, desde principiantes hasta avanzados.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Call to Action Section -->
  <section class="cta-section">
    <div class="container">
      <h2>¡Únete a nuestra comunidad de karatekas hoy mismo!</h2>
      <p>Regístrate ahora y comienza a mejorar tu rendimiento físico y mental.</p>
      <a href="register.php" class="btn btn-light btn-lg">Registrarse</a>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>© 2025 Karate Venezuela - Todos los derechos reservados</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>