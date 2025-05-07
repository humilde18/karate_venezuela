-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2025 at 05:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `karate_control_v2_sql`
--

-- --------------------------------------------------------

--
-- Table structure for table `alumnos`
--

CREATE TABLE `alumnos` (
  `id_alumno` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `nivel` varchar(50) DEFAULT NULL,
  `correo` varchar(150) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'default.png',
  `edad` varchar(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumnos`
--

INSERT INTO `alumnos` (`id_alumno`, `nombre`, `apellido`, `fecha_nacimiento`, `nivel`, `correo`, `telefono`, `foto`, `edad`) VALUES
(1, 'Juan', 'Pérez', '2010-05-15', '10mo kyu', 'juan.perez@gmail.com', '123456789', 'default.png', '15'),
(2, 'María', 'Gómez', '2012-08-20', '9no kyu', 'maria.gomez@gmail.com', '987654321', 'default.png', '13'),
(3, 'Carlos', 'López', '2008-03-10', '8vo kyu', 'carlos.lopez@gmail.com', '456789123', 'default.png', '17'),
(4, 'Ana', 'Martínez', '2011-11-25', '7mo kyu', 'ana.martinez@gmail.com', '789123456', 'default.png', '14');

-- --------------------------------------------------------

--
-- Table structure for table `archivos_publicaciones`
--

CREATE TABLE `archivos_publicaciones` (
  `id_archivo` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `tipo_archivo` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `archivos_publicaciones`
--

INSERT INTO `archivos_publicaciones` (`id_archivo`, `id_publicacion`, `ruta_archivo`, `tipo_archivo`) VALUES
(1, 1, 'uploads/publicaciones/bienvenida.pdf', 'application/pdf'),
(2, 2, 'uploads/publicaciones/examenes.png', 'image/png');

-- --------------------------------------------------------

--
-- Table structure for table `logros`
--

CREATE TABLE `logros` (
  `id_logro` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `kyu` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logros`
--

INSERT INTO `logros` (`id_logro`, `id_alumno`, `titulo`, `observaciones`, `fecha`, `kyu`) VALUES
(1, 1, 'Primer examen', 'Excelente desempeño', '2025-01-15', 10),
(2, 2, 'Segundo examen', 'Buen progreso', '2025-02-20', 9),
(3, 3, 'Tercer examen', 'Necesita mejorar', '2025-03-25', 8),
(4, 4, 'Cuarto examen', 'Muy buen desempeño', '2025-04-30', 7);

-- --------------------------------------------------------

--
-- Table structure for table `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL,
  `fecha_pago` date NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('pendiente','completado') DEFAULT 'pendiente',
  `mes` tinyint(4) NOT NULL,
  `anio` year(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_alumno`, `fecha_pago`, `monto`, `descripcion`, `estado`, `mes`, `anio`) VALUES
(1, 1, '2025-01-07', 50.00, 'Mensualidad', 'completado', 1, '2025'),
(2, 2, '2025-02-07', 50.00, 'Mensualidad', 'pendiente', 2, '2025'),
(3, 3, '2025-03-07', 50.00, 'Mensualidad', 'completado', 3, '2025'),
(4, 4, '2025-04-07', 50.00, 'Mensualidad', 'completado', 4, '2025');

-- --------------------------------------------------------

--
-- Table structure for table `publicaciones`
--

CREATE TABLE `publicaciones` (
  `id_publicacion` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `cuerpo` text NOT NULL,
  `fecha_publicacion` timestamp NULL DEFAULT current_timestamp(),
  `archivos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`archivos`))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `publicaciones`
--

INSERT INTO `publicaciones` (`id_publicacion`, `titulo`, `cuerpo`, `fecha_publicacion`, `archivos`) VALUES
(1, 'Bienvenida', 'Bienvenidos al sistema de control de Karate.', '2025-01-01 14:00:00', NULL),
(2, 'Examenes', 'El próximo examen será el 15 de mayo.', '2025-04-01 16:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` enum('administrador','alumno') DEFAULT 'administrador'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `contraseña`, `rol`) VALUES
(1, 'admin', 'admin@karatecontrol.com', '123456', 'administrador'),
(2, 'pedro', 'pedro@gmail.com', 'password123', 'administrador');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id_video` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `fecha_subida` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id_video`, `id_alumno`, `titulo`, `url`, `fecha_subida`) VALUES
(1, 1, 'Técnicas básicas', 'https://www.youtube.com/watch?v=example1', '2025-01-10'),
(2, 2, 'Kata inicial', 'https://www.youtube.com/watch?v=example2', '2025-02-15'),
(3, 3, 'Defensa personal', 'https://www.youtube.com/watch?v=example3', '2025-03-20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id_alumno`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indexes for table `archivos_publicaciones`
--
ALTER TABLE `archivos_publicaciones`
  ADD PRIMARY KEY (`id_archivo`),
  ADD KEY `id_publicacion` (`id_publicacion`);

--
-- Indexes for table `logros`
--
ALTER TABLE `logros`
  ADD PRIMARY KEY (`id_logro`),
  ADD KEY `id_alumno` (`id_alumno`);

--
-- Indexes for table `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_alumno` (`id_alumno`);

--
-- Indexes for table `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD PRIMARY KEY (`id_publicacion`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id_video`),
  ADD KEY `id_alumno` (`id_alumno`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id_alumno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `archivos_publicaciones`
--
ALTER TABLE `archivos_publicaciones`
  MODIFY `id_archivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `logros`
--
ALTER TABLE `logros`
  MODIFY `id_logro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `publicaciones`
--
ALTER TABLE `publicaciones`
  MODIFY `id_publicacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id_video` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
