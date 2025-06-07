-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-03-2025 a las 20:36:17
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
-- Estructura de tabla para la tabla `caja`
--

CREATE TABLE `caja` (
  `CajaID` int(11) NOT NULL,
  `Fecha` date NOT NULL,
  `Concepto` varchar(100) NOT NULL,
  `RecibidoEnviado` decimal(10,0) DEFAULT NULL,
  `FormaPago` varchar(50) NOT NULL,
  `ClienteInmueble` varchar(100) NOT NULL,
  `Observaciones` text DEFAULT NULL,
  `ClienteID` int(11) DEFAULT NULL,
  `PropiedadID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `caja`
--

INSERT INTO `caja` (`CajaID`, `Fecha`, `Concepto`, `RecibidoEnviado`, `FormaPago`, `ClienteInmueble`, `Observaciones`, `ClienteID`, `PropiedadID`) VALUES
(1, '2025-01-10', '', '1000', '', '', '', NULL, NULL),
(2, '2025-02-08', '', '100', '', '', '', NULL, NULL),
(3, '2025-02-15', '', '-20', '', '', '', NULL, NULL),
(4, '2025-02-14', '', '20000', '', '', '', NULL, NULL),
(5, '2025-03-17', 'pago alquier', '0', 'transferencia', 'Pedro', 'pago a tiempo', NULL, NULL),
(6, '2025-03-17', 'Pago de alquiler	', '-5000', 'Efectivo', 'pedro', 'pago', NULL, NULL),
(7, '2025-03-17', 'Comisión por venta', '10000', 'Efectivo', 'María Gómez	', 'Comisión venta - Casa Calle 456', NULL, NULL),
(8, '2025-03-17', 'Prueba de adjuncion', '300000', 'Transferencia', 'pedro', 'pago', NULL, NULL),
(9, '2025-03-19', 'prueba 2', '-21', 'Transferencia', 'Nahuel', 'no hay observaciones', NULL, NULL),
(10, '2025-03-19', 'Prueba3', '-300', 'Efectivo', 'ClientePrueba1', 'no hay observaciones', NULL, NULL),
(11, '2025-03-19', 'Prueba4', '100000', 'Efectivo', 'ClientePrueba4', 'no hay observaciones', NULL, NULL),
(12, '2025-03-19', 'Pago de alquiler	', '21', 'Transferencia', 'ClientePrueba1', 'pago', NULL, NULL),
(13, '2025-03-19', 'Prueba5', '21', 'Transferencia', 'Nahuel', 'no hay observaciones', NULL, NULL),
(14, '2025-03-19', 'Prueba5', '21', 'Transferencia', 'Nahuel', 'no hay observaciones', NULL, NULL),
(15, '2025-03-19', 'Prueba6', '24', 'Transferencia', 'ClientePrueba4', 'no hay observaciones', NULL, NULL),
(16, '2025-03-19', 'Prueba6', '24', 'Transferencia', 'ClientePrueba4', 'no hay observaciones', NULL, NULL),
(17, '2025-03-19', 'Prueba6', '24', 'Transferencia', 'ClientePrueba4', 'no hay observaciones', NULL, NULL),
(18, '2025-03-19', 'Prueba6', '25', 'Transferencia', 'ClientePrueba4', 'no hay observaciones', NULL, NULL),
(19, '2025-03-19', 'Prueba7', '34', 'Transferencia', 'pedro', 'pago', NULL, NULL),
(20, '2025-03-19', 'ajuste', '9800', 'Efectivo', 'María Gómez	', 'ajuste', NULL, NULL),
(21, '2025-03-19', 'prueba 2', '9000', 'Transferencia', 'pedro', 'pago', NULL, NULL),
(22, '2025-03-19', 'ajuste', '18000', 'Transferencia', 'ClientePrueba4', 'pago', NULL, NULL),
(23, '2025-03-19', 'ajuste', '36000', 'Efectivo', 'María Gómez	', 'no hay observaciones', NULL, NULL),
(24, '2025-03-19', 'Pago de alquiler	', '72000', 'Transferencia', 'ClientePrueba4', 'no hay observaciones', NULL, NULL),
(25, '2025-03-19', 'Pago de alquiler	', '-10', 'Transferencia', 'ClientePrueba1', 'ajuste', NULL, NULL),
(26, '2025-03-19', 'prueba 2', '-211', 'Efectivo', 'María Gómez	', 'Comisión venta - Casa Calle 456', NULL, NULL),
(27, '2025-03-19', 'Prueba 10', '-2222222', 'Efectivo', 'ClientePrueba4', 'no hay observaciones', NULL, NULL),
(28, '2025-03-19', 'prueba 2', '1970000', 'Transferencia', 'ClientePrueba1', 'pago', NULL, NULL),
(29, '2025-03-19', 'ajuste', '7780', 'Transferencia', 'pedro', 'ajuste', NULL, NULL),
(30, '2025-03-19', 'varios', '5', 'Transferencia', 'María Gómez	', 'Comisión venta - Casa Calle 456', NULL, NULL),
(31, '2025-03-20', 'Vision de editar', '500', 'Transferencia', 'Nahuel', 'no hay observaciones', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `ClienteID` int(11) NOT NULL,
  `Fecha` date NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Apellido` varchar(50) NOT NULL,
  `Direccion` varchar(100) NOT NULL,
  `DNI` varchar(20) NOT NULL,
  `DireccionPersonal` varchar(100) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Mail` varchar(100) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`ClienteID`, `Fecha`, `Nombre`, `Apellido`, `Direccion`, `DNI`, `DireccionPersonal`, `Telefono`, `Mail`, `estado`) VALUES
(1, '2023-10-01', 'Juan', 'Pérez', 'Calle Falsa 123', '12345678', 'Calle Verdadera 456', '555-1234', 'juan@example.com', 'inactivo'),
(2, '0000-00-00', 'Pepeasd', 'Aguilar', 'Direccion Falsa 2', '12345677', 'Direccion falas', '3408-345678', 'ailfalso@gmail.co', 'inactivo'),
(5, '2025-03-09', 'Nahuel angel', 'Abalos', 'Guemes 1883', '12345671', 'Guemes 1883', '03408579184', '', 'inactivo'),
(6, '2025-03-09', 'Angel', 'Abalos', 'Guemes 1883', '87654323', 'Guemes 1883', '03408579189', '', 'activo'),
(7, '2025-03-09', 'Nahuel angel', 'Abalos', 'Guemes 1883', '12121212', 'San Cristóbal, Santa Fe', '1212121', 'nahuelab12os77@gmail.com', 'activo'),
(8, '2025-03-10', 'Nahuel angel', 'Abalos', 'Guemes 1883', '78787878', 'San Cristobal', '7878', 'nahuelabalos77@gmail.com', 'activo'),
(9, '2025-03-10', 'Nahuel angel', 'Abalos', 'Guemes 1883', '45565656', 'San Cristobal', '5656', 'nahuelabalos77@gmail.com', 'activo'),
(10, '2025-03-10', 'pedro', 'ttt', 'gherso', '45454545', 'campo', '234234', 'pedro@gmail.com', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `garantes`
--

CREATE TABLE `garantes` (
  `GaranteID` int(11) NOT NULL,
  `Fecha` date NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Apellido` varchar(50) NOT NULL,
  `Direccion` varchar(100) NOT NULL,
  `DNI` varchar(20) NOT NULL,
  `DireccionPersonal` varchar(100) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Mail` varchar(100) DEFAULT NULL,
  `ClienteID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `garantes`
--

INSERT INTO `garantes` (`GaranteID`, `Fecha`, `Nombre`, `Apellido`, `Direccion`, `DNI`, `DireccionPersonal`, `Telefono`, `Mail`, `ClienteID`) VALUES
(1, '2025-03-10', 'Nahuel', 'Abalos', 'Guemes 1883', '96969696', 'San Cristóbal, Santa Fe', '03408579184', 'asdasd@gmail.com', 9),
(2, '2025-03-10', 'peter', 'Apellido1', 'CALLE SIN NOMBRE S/N', '90909090', 'campo', '123123', 'baabab@gmail.com', 10),
(3, '2025-03-10', 'Isa', 'asasas', 'aqui', '45454545', 'hersilia', '4545', 'lala@gmail.com', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `garantesinquilinos`
--

CREATE TABLE `garantesinquilinos` (
  `GaranteInquilinoID` int(11) NOT NULL,
  `Fecha` date NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Apellido` varchar(50) NOT NULL,
  `Direccion` varchar(100) NOT NULL,
  `DNI` varchar(20) NOT NULL,
  `DireccionPersonal` varchar(100) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Mail` varchar(100) DEFAULT NULL,
  `InquilinoID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `garantesinquilinos`
--

INSERT INTO `garantesinquilinos` (`GaranteInquilinoID`, `Fecha`, `Nombre`, `Apellido`, `Direccion`, `DNI`, `DireccionPersonal`, `Telefono`, `Mail`, `InquilinoID`) VALUES
(1, '2025-03-13', 'GInquilino1', 'GIApellido', 'direccion1', '12121212', 'DP1', '3408-122332', 'correo@gmail.com', 1),
(2, '2025-03-13', 'GInquilino12', 'GIApellido', 'direccion2', '12121213', 'DP2', '3408-122333', 'correo@gmail.com', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inquilinos`
--

CREATE TABLE `inquilinos` (
  `InquilinoID` int(11) NOT NULL,
  `Fecha` date DEFAULT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Apellido` varchar(50) NOT NULL,
  `Direccion` varchar(100) NOT NULL,
  `DNI` varchar(20) NOT NULL,
  `DireccionPersonal` varchar(100) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Mail` varchar(100) DEFAULT NULL,
  `ClienteID` int(11) DEFAULT NULL,
  `PropiedadID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `inquilinos`
--

INSERT INTO `inquilinos` (`InquilinoID`, `Fecha`, `Nombre`, `Apellido`, `Direccion`, `DNI`, `DireccionPersonal`, `Telefono`, `Mail`, `ClienteID`, `PropiedadID`) VALUES
(1, '2025-03-13', 'inquilino1', 'apellido', 'direccion1', '12345678', 'direccionP1', '3408-343434', 'email@gmail.com', 10, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propiedades`
--

CREATE TABLE `propiedades` (
  `PropiedadID` int(11) NOT NULL,
  `Fecha` date NOT NULL,
  `Barrio` varchar(100) NOT NULL,
  `Ciudad` varchar(100) NOT NULL,
  `Direccion` varchar(100) NOT NULL,
  `Nro` varchar(20) DEFAULT NULL,
  `Dominio` varchar(50) DEFAULT NULL,
  `NroPartida` varchar(50) DEFAULT NULL,
  `Estado` enum('Alquilada','En Venta') NOT NULL,
  `ClienteID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `propiedades`
--

INSERT INTO `propiedades` (`PropiedadID`, `Fecha`, `Barrio`, `Ciudad`, `Direccion`, `Nro`, `Dominio`, `NroPartida`, `Estado`, `ClienteID`) VALUES
(1, '2023-10-01', 'Centro', 'Ciudad Ejemplo', 'Av. Siempre Viva 742', '1A', 'ABC123', '987654', 'En Venta', 1),
(2, '2025-03-12', 'Barrio1', 'San Cristobal', 'Direccion1', '0', 'D1', '1', 'Alquilada', 10),
(3, '2025-03-13', 'Ferro', '(Seleccionar)', 'GUEMES', '1883', '002', '003', 'Alquilada', 10),
(4, '2025-03-13', 'Barrio1', 'Ciudad1', '0001', '002', '003', '000001', 'Alquilada', 10),
(5, '2025-03-13', 'Barrio2', 'Ciudad3', 'Direccion2', '0003', '0004', '0002', 'En Venta', 10);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `caja`
--
ALTER TABLE `caja`
  ADD PRIMARY KEY (`CajaID`),
  ADD KEY `ClienteID` (`ClienteID`),
  ADD KEY `PropiedadID` (`PropiedadID`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`ClienteID`),
  ADD UNIQUE KEY `DNI` (`DNI`);

--
-- Indices de la tabla `garantes`
--
ALTER TABLE `garantes`
  ADD PRIMARY KEY (`GaranteID`),
  ADD UNIQUE KEY `DNI` (`DNI`),
  ADD KEY `ClienteID` (`ClienteID`);

--
-- Indices de la tabla `garantesinquilinos`
--
ALTER TABLE `garantesinquilinos`
  ADD PRIMARY KEY (`GaranteInquilinoID`),
  ADD UNIQUE KEY `DNI` (`DNI`),
  ADD KEY `InquilinoID` (`InquilinoID`);

--
-- Indices de la tabla `inquilinos`
--
ALTER TABLE `inquilinos`
  ADD PRIMARY KEY (`InquilinoID`),
  ADD UNIQUE KEY `DNI` (`DNI`),
  ADD KEY `ClienteID` (`ClienteID`),
  ADD KEY `fk_inquilinos_propiedades` (`PropiedadID`);

--
-- Indices de la tabla `propiedades`
--
ALTER TABLE `propiedades`
  ADD PRIMARY KEY (`PropiedadID`),
  ADD KEY `ClienteID` (`ClienteID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja`
  MODIFY `CajaID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `ClienteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `garantes`
--
ALTER TABLE `garantes`
  MODIFY `GaranteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `garantesinquilinos`
--
ALTER TABLE `garantesinquilinos`
  MODIFY `GaranteInquilinoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `inquilinos`
--
ALTER TABLE `inquilinos`
  MODIFY `InquilinoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `propiedades`
--
ALTER TABLE `propiedades`
  MODIFY `PropiedadID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `caja`
--
ALTER TABLE `caja`
  ADD CONSTRAINT `caja_ibfk_1` FOREIGN KEY (`ClienteID`) REFERENCES `clientes` (`ClienteID`),
  ADD CONSTRAINT `caja_ibfk_2` FOREIGN KEY (`PropiedadID`) REFERENCES `propiedades` (`PropiedadID`);

--
-- Filtros para la tabla `garantes`
--
ALTER TABLE `garantes`
  ADD CONSTRAINT `garantes_ibfk_1` FOREIGN KEY (`ClienteID`) REFERENCES `clientes` (`ClienteID`);

--
-- Filtros para la tabla `garantesinquilinos`
--
ALTER TABLE `garantesinquilinos`
  ADD CONSTRAINT `garantesinquilinos_ibfk_1` FOREIGN KEY (`InquilinoID`) REFERENCES `inquilinos` (`InquilinoID`);

--
-- Filtros para la tabla `inquilinos`
--
ALTER TABLE `inquilinos`
  ADD CONSTRAINT `fk_inquilinos_propiedades` FOREIGN KEY (`PropiedadID`) REFERENCES `propiedades` (`PropiedadID`);

--
-- Filtros para la tabla `propiedades`
--
ALTER TABLE `propiedades`
  ADD CONSTRAINT `propiedades_ibfk_1` FOREIGN KEY (`ClienteID`) REFERENCES `clientes` (`ClienteID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
