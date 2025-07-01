-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-07-2025 a las 22:02:44
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `caja`
--

INSERT INTO `caja` (`CajaID`, `Fecha`, `Concepto`, `RecibidoEnviado`, `FormaPago`, `ClienteInmueble`, `Observaciones`, `ClienteID`, `PropiedadID`) VALUES
(1, '2025-01-10', '', 1000, '', '', '', NULL, NULL),
(2, '2025-02-08', '', 100, '', '', '', NULL, NULL),
(3, '2025-02-15', '', -20, '', '', '', NULL, NULL),
(4, '2025-02-14', '', 20000, '', '', '', NULL, NULL),
(5, '2025-03-17', 'pago alquier', 0, 'transferencia', 'Pedro', 'pago a tiempo', NULL, NULL),
(6, '2025-03-17', 'Pago de alquiler	', -5000, 'Efectivo', 'pedro', 'pago', NULL, NULL),
(7, '2025-03-17', 'Comisión por venta', 10000, 'Efectivo', 'María Gómez	', 'Comisión venta - Casa Calle 456', NULL, NULL),
(8, '2025-03-17', 'Prueba de adjuncion', 300000, 'Transferencia', 'pedro', 'pago', NULL, NULL),
(9, '2025-03-19', 'prueba 2', -21, 'Transferencia', 'Nahuel', 'no hay observaciones', NULL, NULL),
(10, '2025-03-19', 'Prueba3', -300, 'Efectivo', 'ClientePrueba1', 'no hay observaciones', NULL, NULL),
(11, '2025-03-19', 'Prueba4', 100000, 'Efectivo', 'ClientePrueba4', 'no hay observaciones', NULL, NULL),
(12, '2025-03-19', 'Pago de alquiler	', 21, 'Transferencia', 'ClientePrueba1', 'pago', NULL, NULL),
(13, '2025-03-19', 'Prueba5', 21, 'Transferencia', 'Nahuel', 'no hay observaciones', NULL, NULL),
(14, '2025-03-19', 'Prueba5', 21, 'Transferencia', 'Nahuel', 'no hay observaciones', NULL, NULL),
(15, '2025-03-19', 'Prueba6', 24, 'Transferencia', 'ClientePrueba4', 'no hay observaciones', NULL, NULL),
(16, '2025-03-19', 'Prueba6', 24, 'Transferencia', 'ClientePrueba4', 'no hay observaciones', NULL, NULL),
(17, '2025-03-19', 'Prueba6', 24, 'Transferencia', 'ClientePrueba4', 'no hay observaciones', NULL, NULL),
(18, '2025-03-19', 'Prueba6', 25, 'Transferencia', 'ClientePrueba4', 'no hay observaciones', NULL, NULL),
(19, '2025-03-19', 'Prueba7', 34, 'Transferencia', 'pedro', 'pago', NULL, NULL),
(20, '2025-03-19', 'ajuste', 9800, 'Efectivo', 'María Gómez	', 'ajuste', NULL, NULL),
(21, '2025-03-19', 'prueba 2', 9000, 'Transferencia', 'pedro', 'pago', NULL, NULL),
(22, '2025-03-19', 'ajuste', 18000, 'Transferencia', 'ClientePrueba4', 'pago', NULL, NULL),
(23, '2025-03-19', 'ajuste', 36000, 'Efectivo', 'María Gómez	', 'no hay observaciones', NULL, NULL),
(24, '2025-03-19', 'Pago de alquiler	', 72000, 'Transferencia', 'ClientePrueba4', 'no hay observaciones', NULL, NULL),
(25, '2025-03-19', 'Pago de alquiler	', -10, 'Transferencia', 'ClientePrueba1', 'ajuste', NULL, NULL),
(26, '2025-03-19', 'prueba 2', -211, 'Efectivo', 'María Gómez	', 'Comisión venta - Casa Calle 456', NULL, NULL),
(27, '2025-03-19', 'Prueba 10', -2222222, 'Efectivo', 'ClientePrueba4', 'no hay observaciones', NULL, NULL),
(28, '2025-03-19', 'prueba 2', 1970000, 'Transferencia', 'ClientePrueba1', 'pago', NULL, NULL),
(29, '2025-03-19', 'ajuste', 7780, 'Transferencia', 'pedro', 'ajuste', NULL, NULL),
(30, '2025-03-19', 'varios', 5, 'Transferencia', 'María Gómez	', 'Comisión venta - Casa Calle 456', NULL, NULL),
(31, '2025-03-20', 'Vision de editar', 500, 'Transferencia', 'Nahuel', 'no hay observaciones', NULL, NULL),
(32, '2025-06-25', 'Arreglo', 23000, 'Transferencia', 'Perro', 'Ninguna', NULL, NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`ClienteID`, `Fecha`, `Nombre`, `Apellido`, `Direccion`, `DNI`, `DireccionPersonal`, `Telefono`, `Mail`, `estado`) VALUES
(1, '2023-10-01', 'Juan', 'Pérez', 'Calle Falsa 123', '12345678', 'Calle Verdadera 456', '555-1234', 'juan@example.com', 'inactivo'),
(2, '0000-00-00', 'Pepeasd', 'Aguilar', 'Direccion Falsa 2', '12345677', 'Direccion falas', '3408-345678', 'ailfalso@gmail.co', 'inactivo'),
(5, '2025-03-09', 'Nahuel angel', 'Abalos', 'Guemes 1883', '12345671', 'Guemes 1883', '03408579184', '', 'inactivo'),
(6, '2025-03-09', 'Angel', 'Abalos', 'Guemes 1883', '87654323', 'Guemes 1883', '03408579189', 'angel@gmail.com', 'activo'),
(7, '2025-03-09', 'Nahuel angel', 'Abalos', 'Guemes 1883', '12121212', 'San Cristóbal, Santa Fe', '1212121', 'nahuelab12os77@gmail.com', 'activo'),
(8, '2025-03-10', 'Nahuel angel', 'Abalos', 'Guemes 1883', '78787878', 'San Cristobal', '7878', 'nahuelabalos77@gmail.com', 'activo'),
(9, '2025-03-10', 'Nahuel angel', 'Abalos', 'Guemes 1883', '45565656', 'San Cristobal', '5656', 'nahuelabalos77@gmail.com', 'activo'),
(10, '2025-03-10', 'pedro', 'ttt', 'gherso', '45454545', 'campo', '234234', 'pedro@gmail.com', 'activo'),
(11, '2025-06-25', 'Angel', 'Pereyra', 'Aguara', '42000000', 'campo', '34081956', 'correo@gmail.com', 'activo'),
(12, '2025-06-25', 'Belkis', 'Torres', 'Aguara', '42000009', 'campo', '34081956', 'correo@gmail.com', 'activo');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contratos`
--

INSERT INTO `contratos` (`ContratoID`, `ClienteID`, `InquilinoID`, `PropiedadID`, `GaranteInquilinoID`, `fecha_inicio`, `fecha_fin`, `canon_mensual`, `deposito`, `estado`, `alerta_enviada`, `creado_en`) VALUES
(8, 1, 2, 1, 2, '2025-05-26', '2025-06-26', 1111.00, 11.00, 'vencido', 0, '2025-05-26 23:31:09'),
(16, 10, 12, 2, 3, '2025-05-27', '2025-06-27', 12.00, 12.00, 'vencido', 0, '2025-05-27 22:59:40'),
(17, 10, 12, 2, 3, '2025-05-27', '2025-06-27', 12.00, 12.00, 'vencido', 0, '2025-05-27 22:59:40'),
(22, 1, 24, 1, 15, '2025-05-27', '2025-06-28', 12.00, 12.00, 'vencido', 0, '2025-05-27 23:28:27'),
(23, 1, 24, 1, 15, '2025-05-27', '2025-06-28', 12.00, 12.00, 'vencido', 0, '2025-05-27 23:28:27'),
(24, 1, 25, 1, 17, '2025-05-27', '2025-06-28', 12.00, 12.00, 'vencido', 0, '2025-05-27 23:30:47'),
(25, 10, 26, 2, 19, '2025-05-27', '2025-06-27', 123.00, 123.00, 'vencido', 0, '2025-05-27 23:37:45'),
(26, 6, 30, 8, 23, '2025-06-25', '2025-07-25', 8000.00, 1000.00, 'activo', 0, '2025-06-25 15:14:06'),
(27, 11, 32, 9, 25, '2025-06-25', '2025-07-25', 120000.00, 1000.00, 'activo', 0, '2025-06-25 15:59:10'),
(28, 11, 33, 10, 27, '2025-06-25', '2025-08-01', 23.00, 123.00, 'activo', 0, '2025-06-25 16:18:05'),
(29, 11, 34, 11, 29, '2025-06-25', '2025-08-25', 11.00, 11.00, 'activo', 0, '2025-06-25 16:25:06'),
(30, 12, 35, 12, 31, '2025-06-25', '2025-07-25', 23.00, 23.00, 'activo', 0, '2025-06-25 16:30:17'),
(32, 12, 38, 14, 34, '2025-06-25', '2025-07-26', 123.00, 123.00, 'activo', 0, '2025-06-25 17:18:43'),
(33, 12, 39, 15, 36, '2025-06-25', '2025-07-25', 12.00, 12.00, 'activo', 0, '2025-06-25 17:33:23'),
(34, 10, 47, 3, 43, '2025-07-01', '2025-07-31', 20.00, 10.00, 'activo', 0, '2025-07-01 19:05:02'),
(35, 12, 48, 13, 44, '2025-07-01', '2025-07-31', 30.00, 50.00, 'activo', 0, '2025-07-01 19:22:42');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `garantes`
--

INSERT INTO `garantes` (`GaranteID`, `Fecha`, `Nombre`, `Apellido`, `Direccion`, `DNI`, `DireccionPersonal`, `Telefono`, `Mail`, `ClienteID`) VALUES
(1, '2025-03-10', 'Nahuel', 'Abalos', 'Guemes 1883', '96969696', 'San Cristóbal, Santa Fe', '03408579184', 'asdasd@gmail.com', 9),
(2, '2025-03-10', 'peter', 'Apellido1', 'CALLE SIN NOMBRE S/N', '90909090', 'campo', '123123', 'baabab@gmail.com', 10),
(3, '2025-03-10', 'Isa', 'asasas', 'aqui', '45454545', 'hersilia', '4545', 'lala@gmail.com', 10),
(4, '0000-00-00', 'garanteInquilino 1', '', '', '12345678', NULL, NULL, NULL, NULL),
(5, '0000-00-00', 'garanteInquilino2', '', '', '12345677', NULL, NULL, NULL, NULL),
(22, '0000-00-00', 'garanteInquilino 1', '', '', '12345600', NULL, NULL, NULL, NULL),
(23, '0000-00-00', 'garanteInquilino2', '', '', '12345601', NULL, NULL, NULL, NULL),
(24, '0000-00-00', 'garanteInquilino 1', '', '', '12345612', NULL, NULL, NULL, NULL),
(25, '0000-00-00', 'garanteInquilino2', '', '', '123456123', NULL, NULL, NULL, NULL),
(26, '0000-00-00', 'garanteInquilino 1', '', '', '39384756', NULL, NULL, NULL, NULL),
(27, '0000-00-00', 'garanteInquilino2', '', '', '23487645', NULL, NULL, NULL, NULL),
(28, '2025-06-25', 'Belkis', 'Torres', 'Aguara', '42000001', 'Campo', '34088899', 'belkis@gmail.com', 11),
(29, '2025-06-25', 'NAhuel', 'Abalos', 'Aguara', '420000010', 'Campo', '34088899', 'belkis@gmail.com', 12);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `garantesinquilinos`
--

INSERT INTO `garantesinquilinos` (`GaranteInquilinoID`, `Fecha`, `Nombre`, `Apellido`, `Direccion`, `DNI`, `DireccionPersonal`, `Telefono`, `Mail`, `InquilinoID`) VALUES
(15, '0000-00-00', 'garanteInquilino 1', '', '', '1234567812121212', NULL, NULL, NULL, NULL),
(16, '0000-00-00', 'garanteInquilino2', '', '', '1221', NULL, NULL, NULL, NULL),
(17, '0000-00-00', 'garanteInquilino 1', '', '', '12345678', NULL, NULL, NULL, NULL),
(19, '0000-00-00', 'garanteInquilino 1', '', '', '12345699123', NULL, NULL, NULL, NULL),
(20, '0000-00-00', 'garanteInquilino2', '', '', '12345601123', NULL, NULL, NULL, NULL),
(21, '0000-00-00', '111', '', '', '111', NULL, NULL, NULL, NULL),
(22, '0000-00-00', '222', '', '', '222', NULL, NULL, NULL, NULL),
(23, '0000-00-00', 'Tami', '', '', '42125499', NULL, NULL, NULL, NULL),
(24, '0000-00-00', '', '', '', '', NULL, NULL, NULL, NULL),
(25, '0000-00-00', 'Diego', '', '', '42000004', NULL, NULL, NULL, NULL),
(27, '0000-00-00', 'Diego', '', '', '42000007', NULL, NULL, NULL, NULL),
(29, '0000-00-00', 'L', '', '', '42000008', NULL, NULL, NULL, NULL),
(31, '0000-00-00', 'Vegeta', '', '', '42000012', NULL, NULL, NULL, NULL),
(34, '0000-00-00', 'Roman', 'Riquelme', '', '42000017', NULL, '123123', 'al@gmail.com', 38),
(36, '0000-00-00', 'Brisa', 'Leys', '', '42000021', NULL, '123123', 'al@gmail.com', 39),
(37, '0000-00-00', 'sandra', 'sasss', '', '42000031', NULL, '3128765676', 'sandra@gmail.com', 40),
(38, '0000-00-00', 'Primo', 'uno', '', '23000002', NULL, '7865676567', 'primo@gmail.com', 44),
(39, '0000-00-00', 'danilo', 'lemer', '', '23000005', NULL, '5655845698', 'danilo@gmail.com', 45),
(41, '0000-00-00', 'maria', 'lemer', '', '43000021', NULL, '6525654587', 'maria@gmail.com', 46),
(43, '0000-00-00', 'belkis', 'torres', '', '46000002', NULL, '45212545215', 'belkis@gmail.com', 47),
(44, '0000-00-00', 'Martina', 'Barrios', '', '43000022', NULL, '65232514789', 'martina@gmail.com', 48),
(45, '0000-00-00', 'Diame ', 'Ferrero', '', '43000023', NULL, '8974589658', 'diame@gmail.com', 48);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inquilinos`
--

CREATE TABLE `inquilinos` (
  `InquilinoID` int(11) NOT NULL,
  `Fecha` date DEFAULT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Apellido` varchar(50) NOT NULL,
  `DNI` varchar(20) NOT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Mail` varchar(100) DEFAULT NULL,
  `ClienteID` int(11) DEFAULT NULL,
  `PropiedadID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inquilinos`
--

INSERT INTO `inquilinos` (`InquilinoID`, `Fecha`, `Nombre`, `Apellido`, `DNI`, `Telefono`, `Mail`, `ClienteID`, `PropiedadID`) VALUES
(25, NULL, 'Nahuel', '', '42128499', '3408579184', 'nahuelabalos77@gmail.com', NULL, NULL),
(26, NULL, 'Nahuel', '', '42128499123', '3408579184', 'nahuelabalos77@gmail.com', NULL, NULL),
(27, NULL, '123', '', '123', '123', '123@gmail.com', NULL, NULL),
(30, NULL, 'Tamara', '', '42123499', '34532222', '123@gmail.com', NULL, NULL),
(32, NULL, 'Angel', 'Abalos', '42000003', '34532222', '123@gmail.com', NULL, NULL),
(33, NULL, 'Miku', 'Tatabane', '42000006', '34532222', '123@gmail.com', 0, 10),
(34, NULL, 'Kira', 'Urabe', '42000007', '123', '123@gmail.com', 0, 11),
(35, NULL, 'Goku', 'Kakaroto', '420000011', '34532222', '123@gmail.com', 12, 12),
(36, '0000-00-00', 'Marco', 'Reus', '420000013', '123131', '123@gmail.com', 12, 13),
(38, '0000-00-00', 'Radamel', 'Falcao', '420000016', '123', '123@gmail.com', 12, 14),
(39, '0000-00-00', 'Mario', 'Leis', '420000020', '34532222', '123@gmail.com', 12, 15),
(40, '0000-00-00', 'vero', 'ramirez', '42000030', '5654567898', 'vero@gmail.com', 10, 4),
(44, NULL, 'tomas', 'sal', '23000001', '7876565456', 'tomas@gmail.com', 1, 1),
(45, NULL, 'jonatan', 'lemer', '23000004', '56521452365', 'jona@gmail.com', 10, 2),
(46, NULL, 'danilo', 'lemer', '430000020', '5652325658', 'dani@gmail.com', 10, 5),
(47, '2025-07-01', 'reicardo', 'pereyra', '46000001', '65232565214', 'ricardo@gmail.com', 10, 3),
(48, '2025-07-01', 'Guillermina', 'Barrios', '430000021', '5623254152', 'guille@gmail.com', 12, 13),
(49, '2025-07-01', 'Ale', 'FF', '430000022', '6523698587', 'ale@gmail.com', 0, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones_leidas`
--

CREATE TABLE `notificaciones_leidas` (
  `id` int(11) NOT NULL,
  `ContratoID` int(11) DEFAULT NULL,
  `fecha_leido` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones_leidas`
--

INSERT INTO `notificaciones_leidas` (`id`, `ContratoID`, `fecha_leido`) VALUES
(1, 26, '2025-06-30 17:01:38'),
(2, 27, '2025-06-30 17:02:52');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `propiedades`
--

INSERT INTO `propiedades` (`PropiedadID`, `Fecha`, `Barrio`, `Ciudad`, `Direccion`, `Nro`, `Dominio`, `NroPartida`, `Estado`, `ClienteID`) VALUES
(1, '2023-10-01', 'Centro', 'Ciudad Ejemplo', 'Av. Siempre Viva 742', '1A', 'ABC123', '987654', 'En Venta', 1),
(2, '2025-03-12', 'Barrio1', 'San Cristobal', 'Direccion1', '0', 'D1', '1', 'Alquilada', 10),
(3, '2025-03-13', 'Ferrosss', '(Seleccionar)', 'GUEMES', '1883', '002', '003', 'Alquilada', 10),
(4, '2025-03-13', 'Barrio1', 'Ciudad1', '0001', '002', '003', '000001', 'Alquilada', 10),
(5, '2025-03-13', 'Barrio2', 'Ciudad3', 'Direccion2', '0003', '0004', '0002', 'En Venta', 10),
(6, '2025-06-19', 'libertad', 'SAN CRISTOBAL', 'Guemes', '1883', '003', '003', 'En Venta', NULL),
(7, '2025-06-25', 'nn', 'los pp', 'sd', '099', '09', '588', 'Alquilada', NULL),
(8, '2025-06-25', 'normal', 'los pepitos', 'sin direccion', '099', '09', '588', 'Alquilada', 6),
(9, '2025-06-25', 'Ferro Dho', 'San Cristobal', 'Guemes ', '1883', '009', '12', 'Alquilada', 11),
(10, '2025-06-25', 'Ferro Dho', 'San Cristobal', 'Guemes ', '1884', '010', '12', 'En Venta', 11),
(11, '2025-06-25', 'Centro', 'San Cristobal', 'Caceros', '1887', '010', '588', 'En Venta', 11),
(12, '2025-06-25', 'Dho', 'San Cristobal', 'Pueyrredon', '099', '009', '588', 'En Venta', 12),
(13, '2025-06-25', 'Centro', 'San guillermo', 'San MArtin', '1883', '09', '588', 'En Venta', 12),
(14, '2025-06-25', 'Centro', 'Villa Trinidad', 'Almiron', '1887', '09', '588', 'En Venta', 12),
(15, '2025-06-25', 'Dho', 'Villa Trinidad', 'AAA', '1884', '09', '588', 'Alquilada', 12);

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
-- Indices de la tabla `contratos`
--
ALTER TABLE `contratos`
  ADD PRIMARY KEY (`ContratoID`),
  ADD KEY `ClienteID` (`ClienteID`),
  ADD KEY `InquilinoID` (`InquilinoID`),
  ADD KEY `PropiedadID` (`PropiedadID`),
  ADD KEY `GaranteInquilinoID` (`GaranteInquilinoID`);

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
-- Indices de la tabla `notificaciones_leidas`
--
ALTER TABLE `notificaciones_leidas`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `CajaID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `ClienteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `contratos`
--
ALTER TABLE `contratos`
  MODIFY `ContratoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `garantes`
--
ALTER TABLE `garantes`
  MODIFY `GaranteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `garantesinquilinos`
--
ALTER TABLE `garantesinquilinos`
  MODIFY `GaranteInquilinoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `inquilinos`
--
ALTER TABLE `inquilinos`
  MODIFY `InquilinoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `notificaciones_leidas`
--
ALTER TABLE `notificaciones_leidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `propiedades`
--
ALTER TABLE `propiedades`
  MODIFY `PropiedadID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `caja`
--
ALTER TABLE `caja`
  ADD CONSTRAINT `caja_ibfk_1` FOREIGN KEY (`ClienteID`) REFERENCES `clientes` (`ClienteID`),
  ADD CONSTRAINT `caja_ibfk_2` FOREIGN KEY (`PropiedadID`) REFERENCES `propiedades` (`PropiedadID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
