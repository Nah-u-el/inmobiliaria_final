-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-06-2025 a las 17:08:23
-- Versión del servidor: 10.4.13-MariaDB
-- Versión de PHP: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inmobiliaria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contratos`
--

CREATE TABLE `contratos` (
  `ContratoID` int(11) NOT NULL,
  `ClienteID` int(11) NOT NULL,
  `InquilinoID` int(11) NOT NULL,
  `PropiedadID` int(11) NOT NULL,
  `GaranteInquilinoID` int(11) DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `canon_mensual` decimal(10,2) NOT NULL,
  `deposito` decimal(10,2) DEFAULT 0.00,
  `estado` enum('activo','vencido','rescindido') DEFAULT 'activo',
  `alerta_enviada` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `contratos`
--

INSERT INTO `contratos` (`ContratoID`, `ClienteID`, `InquilinoID`, `PropiedadID`, `GaranteInquilinoID`, `fecha_inicio`, `fecha_fin`, `canon_mensual`, `deposito`, `estado`, `alerta_enviada`, `creado_en`) VALUES
(8, 1, 2, 1, 2, '2025-05-26', '2025-06-26', '1111.00', '11.00', 'activo', 0, '2025-05-26 23:31:09'),
(16, 10, 12, 2, 3, '2025-05-27', '2025-06-27', '12.00', '12.00', 'activo', 0, '2025-05-27 22:59:40'),
(17, 10, 12, 2, 3, '2025-05-27', '2025-06-27', '12.00', '12.00', 'activo', 0, '2025-05-27 22:59:40'),
(22, 1, 24, 1, 15, '2025-05-27', '2025-06-28', '12.00', '12.00', 'activo', 0, '2025-05-27 23:28:27'),
(23, 1, 24, 1, 15, '2025-05-27', '2025-06-28', '12.00', '12.00', 'activo', 0, '2025-05-27 23:28:27'),
(24, 1, 25, 1, 17, '2025-05-27', '2025-06-28', '12.00', '12.00', 'activo', 0, '2025-05-27 23:30:47'),
(25, 10, 26, 2, 19, '2025-05-27', '2025-06-27', '123.00', '123.00', 'activo', 0, '2025-05-27 23:37:45'),
(26, 6, 30, 8, 23, '2025-06-25', '2025-07-25', '8000.00', '1000.00', 'activo', 0, '2025-06-25 15:14:06'),
(27, 11, 32, 9, 25, '2025-06-25', '2025-07-25', '120000.00', '1000.00', 'activo', 0, '2025-06-25 15:59:10'),
(28, 11, 33, 10, 27, '2025-06-25', '2025-08-01', '23.00', '123.00', 'activo', 0, '2025-06-25 16:18:05'),
(29, 11, 34, 11, 29, '2025-06-25', '2025-08-25', '11.00', '11.00', 'activo', 0, '2025-06-25 16:25:06'),
(30, 12, 35, 12, 31, '2025-06-25', '2025-07-25', '23.00', '23.00', 'activo', 0, '2025-06-25 16:30:17'),
(32, 12, 38, 14, 34, '2025-06-25', '2025-07-26', '123.00', '123.00', 'activo', 0, '2025-06-25 17:18:43'),
(33, 12, 39, 15, 36, '2025-06-25', '2025-07-25', '12.00', '12.00', 'activo', 0, '2025-06-25 17:33:23');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `contratos`
--
ALTER TABLE `contratos`
  ADD PRIMARY KEY (`ContratoID`),
  ADD KEY `ClienteID` (`ClienteID`),
  ADD KEY `InquilinoID` (`InquilinoID`),
  ADD KEY `PropiedadID` (`PropiedadID`),
  ADD KEY `GaranteInquilinoID` (`GaranteInquilinoID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `contratos`
--
ALTER TABLE `contratos`
  MODIFY `ContratoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `contratos`
--
ALTER TABLE `contratos`
  ADD CONSTRAINT `contratos_ibfk_1` FOREIGN KEY (`ClienteID`) REFERENCES `clientes` (`ClienteID`),
  ADD CONSTRAINT `contratos_ibfk_2` FOREIGN KEY (`InquilinoID`) REFERENCES `inquilinos` (`InquilinoID`),
  ADD CONSTRAINT `contratos_ibfk_3` FOREIGN KEY (`PropiedadID`) REFERENCES `propiedades` (`PropiedadID`),
  ADD CONSTRAINT `contratos_ibfk_4` FOREIGN KEY (`GaranteInquilinoID`) REFERENCES `garantesinquilinos` (`GaranteInquilinoID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
