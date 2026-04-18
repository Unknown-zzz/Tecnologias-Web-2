-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-04-2026 a las 00:30:47
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Crear y seleccionar la base de datos
-- --------------------------------------------------------

DROP DATABASE IF EXISTS `ecommerce`;
CREATE DATABASE `ecommerce` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ecommerce`;

-- --------------------------------------------------------
-- Tabla `categoria`
-- --------------------------------------------------------

CREATE TABLE `categoria` (
  `cod` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categoria` (`cod`, `nombre`) VALUES
(4, 'Accesorios'),
(5, 'Calzado'),
(3, 'Smartphones'),
(2, 'Sudaderas'),
(1, 'Vape');

-- --------------------------------------------------------
-- Tabla `cuenta`
-- --------------------------------------------------------

CREATE TABLE `cuenta` (
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('cliente','admin') DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cuenta` (`usuario`, `password`, `rol`) VALUES
('admin', '$2y$10$JUA3u5jHv4tejyJ/E2RaIeghU07tuQ2pGsJgbuZ7ur4llnTaAYVG6', 'admin'),
('cliente', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'),
('Jona', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'),
('Jonathan', '$2y$10$JUA3u5jHv4tejyJ/E2RaIeghU07tuQ2pGsJgbuZ7ur4llnTaAYVG6', 'cliente');

-- --------------------------------------------------------
-- Tabla `cliente`
-- --------------------------------------------------------

CREATE TABLE `cliente` (
  `ci` varchar(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apPaterno` varchar(50) NOT NULL,
  `apMaterno` varchar(50) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `nroCelular` varchar(20) DEFAULT NULL,
  `usuarioCuenta` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cliente` (`ci`, `nombres`, `apPaterno`, `apMaterno`, `correo`, `direccion`, `nroCelular`, `usuarioCuenta`) VALUES
('12345678', 'Juan', 'Pérez', 'García', 'juan@email.com', 'Calle Ficticia 123', '987654321', 'cliente'),
('87654321', 'Jona', 'User', 'Test', 'jona@email.com', 'Calle Test 456', '123456789', 'Jona'),
('9585486', 'Jonathan', 'Campos', 'Mansilla', 'j29s09s03@gmail.com', 'Mi casa', '69160031', 'Jonathan');

-- --------------------------------------------------------
-- Tabla `industria`
-- --------------------------------------------------------

CREATE TABLE `industria` (
  `cod` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `industria` (`cod`, `nombre`) VALUES
(4, 'Deportes'),
(3, 'Electrónica'),
(2, 'Ropa y Accesorios'),
(1, 'Tecnología');

-- --------------------------------------------------------
-- Tabla `marca`
-- --------------------------------------------------------

CREATE TABLE `marca` (
  `cod` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `marca` (`cod`, `nombre`) VALUES
(3, 'Adidas'),
(5, 'Apple'),
(2, 'Nike'),
(4, 'Samsung'),
(1, 'Vaporesso');

-- --------------------------------------------------------
-- Tabla `sucursal`
-- --------------------------------------------------------

CREATE TABLE `sucursal` (
  `cod` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `sucursal` (`cod`, `nombre`, `direccion`, `telefono`) VALUES
(1, 'Sucursal Central', 'Centro de la ciudad', '123456789');

-- --------------------------------------------------------
-- Tabla `producto`
-- --------------------------------------------------------

CREATE TABLE `producto` (
  `cod` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `codMarca` int(11) DEFAULT NULL,
  `codIndustria` int(11) DEFAULT NULL,
  `codCategoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `producto` (`cod`, `nombre`, `descripcion`, `precio`, `imagen`, `estado`, `codMarca`, `codIndustria`, `codCategoria`) VALUES
(1, 'Sudadera Nike', 'Sudadera Nike de algodón premium', 749.90, 'sudadera_nike.png', 'activo', 2, 2, 2),
(2, 'Vaporesso XROS 3', 'Vape Vaporesso XROS 3 con batería de larga duración', 1299.00, 'vaporesso_xros3.png', 'activo', 1, 1, 1),
(3, 'iPhone 15', 'Smartphone Apple iPhone 15', 7499.00, 'iphone15.png', 'activo', 5, 3, 3),
(4, 'Air Jordan 1', 'Zapatillas Air Jordan 1 retro', 2399.90, 'air_jordan1.png', 'activo', 2, 4, 5);

-- --------------------------------------------------------
-- Tabla `notaventa`
-- --------------------------------------------------------

CREATE TABLE `notaventa` (
  `nro` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `ciCliente` varchar(20) DEFAULT NULL,
  `rutaInforme` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `notaventa` (`nro`, `fecha`, `ciCliente`, `rutaInforme`) VALUES
(3, '2026-04-16 17:47:17', '87654321', NULL),
(4, '2026-04-16 17:48:18', '87654321', NULL);

-- --------------------------------------------------------
-- Tabla `detallenotaventa`
-- --------------------------------------------------------

CREATE TABLE `detallenotaventa` (
  `nroNotaVenta` int(11) NOT NULL,
  `codProducto` int(11) NOT NULL,
  `cant` int(11) NOT NULL,
  `precioUnitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `detallenotaventa` (`nroNotaVenta`, `codProducto`, `cant`, `precioUnitario`) VALUES
(3, 4, 1, 2399.90),
(4, 1, 1, 749.90),
(4, 2, 1, 1299.00),
(4, 3, 2, 7499.00),
(4, 4, 5, 2399.90);

-- --------------------------------------------------------
-- Tabla `detalleproductosucursal`
-- --------------------------------------------------------

CREATE TABLE `detalleproductosucursal` (
  `codProducto` int(11) NOT NULL,
  `codSucursal` int(11) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `detalleproductosucursal` (`codProducto`, `codSucursal`, `stock`) VALUES
(1, 1, 14),
(2, 1, 7),
(3, 1, 3),
(4, 1, 4);

-- --------------------------------------------------------
-- Índices y claves primarias
-- --------------------------------------------------------

ALTER TABLE `categoria`
  ADD PRIMARY KEY (`cod`),
  ADD UNIQUE KEY `nombre` (`nombre`);

ALTER TABLE `cuenta`
  ADD PRIMARY KEY (`usuario`);

ALTER TABLE `cliente`
  ADD PRIMARY KEY (`ci`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `usuarioCuenta` (`usuarioCuenta`);

ALTER TABLE `industria`
  ADD PRIMARY KEY (`cod`),
  ADD UNIQUE KEY `nombre` (`nombre`);

ALTER TABLE `marca`
  ADD PRIMARY KEY (`cod`),
  ADD UNIQUE KEY `nombre` (`nombre`);

ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`cod`);

ALTER TABLE `producto`
  ADD PRIMARY KEY (`cod`),
  ADD KEY `codMarca` (`codMarca`),
  ADD KEY `codIndustria` (`codIndustria`),
  ADD KEY `codCategoria` (`codCategoria`);

ALTER TABLE `notaventa`
  ADD PRIMARY KEY (`nro`),
  ADD KEY `ciCliente` (`ciCliente`);

ALTER TABLE `detallenotaventa`
  ADD PRIMARY KEY (`nroNotaVenta`,`codProducto`),
  ADD KEY `codProducto` (`codProducto`);

ALTER TABLE `detalleproductosucursal`
  ADD PRIMARY KEY (`codProducto`,`codSucursal`),
  ADD KEY `codSucursal` (`codSucursal`);

-- --------------------------------------------------------
-- AUTO_INCREMENT
-- --------------------------------------------------------

ALTER TABLE `categoria`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `industria`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `marca`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `notaventa`
  MODIFY `nro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `producto`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `sucursal`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

-- --------------------------------------------------------
-- Claves foráneas
-- --------------------------------------------------------

ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`usuarioCuenta`) REFERENCES `cuenta` (`usuario`);

ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`codMarca`) REFERENCES `marca` (`cod`),
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`codIndustria`) REFERENCES `industria` (`cod`),
  ADD CONSTRAINT `producto_ibfk_3` FOREIGN KEY (`codCategoria`) REFERENCES `categoria` (`cod`);

ALTER TABLE `notaventa`
  ADD CONSTRAINT `notaventa_ibfk_1` FOREIGN KEY (`ciCliente`) REFERENCES `cliente` (`ci`);

ALTER TABLE `detallenotaventa`
  ADD CONSTRAINT `detallenotaventa_ibfk_1` FOREIGN KEY (`nroNotaVenta`) REFERENCES `notaventa` (`nro`),
  ADD CONSTRAINT `detallenotaventa_ibfk_2` FOREIGN KEY (`codProducto`) REFERENCES `producto` (`cod`);

ALTER TABLE `detalleproductosucursal`
  ADD CONSTRAINT `detalleproductosucursal_ibfk_1` FOREIGN KEY (`codProducto`) REFERENCES `producto` (`cod`),
  ADD CONSTRAINT `detalleproductosucursal_ibfk_2` FOREIGN KEY (`codSucursal`) REFERENCES `sucursal` (`cod`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
