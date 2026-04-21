-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-04-2026 a las 14:32:24
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ecommerce`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_categoria_all` ()   BEGIN
    SELECT * FROM Categoria ORDER BY nombre ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_categoria_create` (IN `p_nombre` VARCHAR(100))   BEGIN
    INSERT INTO Categoria (nombre) VALUES (TRIM(p_nombre));
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_categoria_delete` (IN `p_cod` INT)   BEGIN
    DELETE FROM Categoria WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_categoria_find` (IN `p_cod` INT)   BEGIN
    SELECT * FROM Categoria WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_categoria_update` (IN `p_cod` INT, IN `p_nombre` VARCHAR(100))   BEGIN
    UPDATE Categoria SET nombre = TRIM(p_nombre) WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cliente_all` ()   BEGIN
    SELECT * FROM Cliente ORDER BY nombres ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cliente_create` (IN `p_ci` VARCHAR(20), IN `p_nombres` VARCHAR(100), IN `p_apPaterno` VARCHAR(50), IN `p_apMaterno` VARCHAR(50), IN `p_correo` VARCHAR(100), IN `p_direccion` TEXT, IN `p_nroCelular` VARCHAR(20), IN `p_usuarioCuenta` VARCHAR(50))   BEGIN
    INSERT INTO Cliente (ci, nombres, apPaterno, apMaterno, correo, direccion, nroCelular, usuarioCuenta)
    VALUES (p_ci, TRIM(p_nombres), TRIM(p_apPaterno), TRIM(p_apMaterno), p_correo, p_direccion, p_nroCelular, p_usuarioCuenta);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cliente_delete` (IN `p_ci` VARCHAR(20))   BEGIN
    DELETE FROM Cliente WHERE ci = p_ci;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cliente_find` (IN `p_ci` VARCHAR(20))   BEGIN
    SELECT * FROM Cliente WHERE ci = p_ci;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cliente_update` (IN `p_ci` VARCHAR(20), IN `p_nombres` VARCHAR(100), IN `p_apPaterno` VARCHAR(50), IN `p_apMaterno` VARCHAR(50), IN `p_correo` VARCHAR(100), IN `p_direccion` TEXT, IN `p_nroCelular` VARCHAR(20))   BEGIN
    UPDATE Cliente SET
        nombres = TRIM(p_nombres),
        apPaterno = TRIM(p_apPaterno),
        apMaterno = TRIM(p_apMaterno),
        correo = p_correo,
        direccion = p_direccion,
        nroCelular = p_nroCelular
    WHERE ci = p_ci;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cuenta_create` (IN `p_usuario` VARCHAR(50), IN `p_password` VARCHAR(255), IN `p_rol` VARCHAR(20))   BEGIN
    INSERT INTO Cuenta (usuario, password, rol) VALUES (p_usuario, p_password, p_rol);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cuenta_find` (IN `p_usuario` VARCHAR(50))   BEGIN
    SELECT * FROM Cuenta WHERE usuario = p_usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cuenta_update_password` (IN `p_usuario` VARCHAR(50), IN `p_password` VARCHAR(255))   BEGIN
    UPDATE Cuenta SET password = p_password WHERE usuario = p_usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_industria_all` ()   BEGIN
    SELECT * FROM Industria ORDER BY nombre ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_industria_create` (IN `p_nombre` VARCHAR(100))   BEGIN
    INSERT INTO Industria (nombre) VALUES (TRIM(p_nombre));
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_industria_delete` (IN `p_cod` INT)   BEGIN
    DELETE FROM Industria WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_industria_find` (IN `p_cod` INT)   BEGIN
    SELECT * FROM Industria WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_industria_update` (IN `p_cod` INT, IN `p_nombre` VARCHAR(100))   BEGIN
    UPDATE Industria SET nombre = TRIM(p_nombre) WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_marca_all` ()   BEGIN
    SELECT * FROM Marca ORDER BY nombre ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_marca_create` (IN `p_nombre` VARCHAR(100))   BEGIN
    INSERT INTO Marca (nombre) VALUES (TRIM(p_nombre));
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_marca_delete` (IN `p_cod` INT)   BEGIN
    DELETE FROM Marca WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_marca_find` (IN `p_cod` INT)   BEGIN
    SELECT * FROM Marca WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_marca_update` (IN `p_cod` INT, IN `p_nombre` VARCHAR(100))   BEGIN
    UPDATE Marca SET nombre = TRIM(p_nombre) WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_add_stock` (IN `p_codProducto` INT, IN `p_stock` INT, IN `p_codSucursal` INT)   BEGIN
    INSERT INTO DetalleProductoSucursal (codProducto, codSucursal, stock)
    VALUES (p_codProducto, p_codSucursal, p_stock)
    ON DUPLICATE KEY UPDATE stock = p_stock;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_all_active` ()   BEGIN
    SELECT p.*, m.nombre AS marca, cat.nombre AS categoria, i.nombre AS industria, 
           COALESCE(dps.stock, 0) AS stock
    FROM Producto p
    LEFT JOIN Marca m ON p.codMarca = m.cod
    LEFT JOIN Categoria cat ON p.codCategoria = cat.cod
    LEFT JOIN Industria i ON p.codIndustria = i.cod
    LEFT JOIN DetalleProductoSucursal dps ON p.cod = dps.codProducto AND dps.codSucursal = 1
    WHERE p.estado = 'activo'
    ORDER BY p.cod DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_all_admin` ()   BEGIN
    SELECT p.*, m.nombre AS marca, cat.nombre AS categoria, i.nombre AS industria,
           COALESCE(dps.stock, 0) AS stock
    FROM Producto p
    LEFT JOIN Marca m ON p.codMarca = m.cod
    LEFT JOIN Categoria cat ON p.codCategoria = cat.cod
    LEFT JOIN Industria i ON p.codIndustria = i.cod
    LEFT JOIN DetalleProductoSucursal dps ON p.cod = dps.codProducto AND dps.codSucursal = 1
    ORDER BY p.cod DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_create` (IN `p_nombre` VARCHAR(100), IN `p_descripcion` TEXT, IN `p_precio` DECIMAL(10,2), IN `p_imagen` VARCHAR(255), IN `p_estado` VARCHAR(20), IN `p_codMarca` INT, IN `p_codIndustria` INT, IN `p_codCategoria` INT, IN `p_stock` INT)   BEGIN
    DECLARE v_codProducto INT;
    
    INSERT INTO Producto (nombre, descripcion, precio, imagen, estado, codMarca, codIndustria, codCategoria)
    VALUES (TRIM(p_nombre), TRIM(p_descripcion), p_precio, p_imagen, p_estado, p_codMarca, p_codIndustria, p_codCategoria);
    
    SET v_codProducto = LAST_INSERT_ID();
    
    IF p_stock > 0 THEN
        INSERT INTO DetalleProductoSucursal (codProducto, codSucursal, stock)
        VALUES (v_codProducto, 1, p_stock);
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_delete` (IN `p_cod` INT)   BEGIN
    DELETE FROM Producto WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_find` (IN `p_cod` INT)   BEGIN
    SELECT p.*, m.nombre AS marca, cat.nombre AS categoria, i.nombre AS industria
    FROM Producto p
    LEFT JOIN Marca m ON p.codMarca = m.cod
    LEFT JOIN Categoria cat ON p.codCategoria = cat.cod
    LEFT JOIN Industria i ON p.codIndustria = i.cod
    WHERE p.cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_update` (IN `p_cod` INT, IN `p_nombre` VARCHAR(100), IN `p_descripcion` TEXT, IN `p_precio` DECIMAL(10,2), IN `p_imagen` VARCHAR(255), IN `p_estado` VARCHAR(20), IN `p_codMarca` INT, IN `p_codIndustria` INT, IN `p_codCategoria` INT)   BEGIN
    UPDATE Producto SET
        nombre = TRIM(p_nombre),
        descripcion = TRIM(p_descripcion),
        precio = p_precio,
        imagen = p_imagen,
        estado = p_estado,
        codMarca = p_codMarca,
        codIndustria = p_codIndustria,
        codCategoria = p_codCategoria
    WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sucursal_all` ()   BEGIN
    SELECT * FROM Sucursal ORDER BY nombre ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sucursal_create` (IN `p_nombre` VARCHAR(100), IN `p_direccion` VARCHAR(255), IN `p_telefono` VARCHAR(20))   BEGIN
    INSERT INTO Sucursal (nombre, direccion, telefono) VALUES (TRIM(p_nombre), p_direccion, p_telefono);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sucursal_delete` (IN `p_cod` INT)   BEGIN
    DELETE FROM Sucursal WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sucursal_find` (IN `p_cod` INT)   BEGIN
    SELECT * FROM Sucursal WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sucursal_update` (IN `p_cod` INT, IN `p_nombre` VARCHAR(100), IN `p_direccion` VARCHAR(255), IN `p_telefono` VARCHAR(20))   BEGIN
    UPDATE Sucursal SET nombre = TRIM(p_nombre), direccion = p_direccion, telefono = p_telefono WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_all` ()   BEGIN
    SELECT nv.nro, nv.fecha, nv.ciCliente,
           c.nombres, c.apPaterno, c.apMaterno,
           SUM(dnv.cant * dnv.precioUnitario) as total
    FROM NotaVenta nv
    INNER JOIN Cliente c ON nv.ciCliente = c.ci
    INNER JOIN DetalleNotaVenta dnv ON nv.nro = dnv.nroNotaVenta
    GROUP BY nv.nro, nv.fecha, nv.ciCliente, c.nombres, c.apPaterno, c.apMaterno
    ORDER BY nv.fecha DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_create` (IN `p_ciCliente` VARCHAR(20), OUT `p_nro` INT)   BEGIN
    INSERT INTO NotaVenta (ciCliente) VALUES (p_ciCliente);
    SET p_nro = LAST_INSERT_ID();
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_details` (IN `p_nro` INT)   BEGIN
    SELECT dnv.codProducto, dnv.cant, dnv.precioUnitario,
           p.nombre as producto, p.imagen
    FROM DetalleNotaVenta dnv
    INNER JOIN Producto p ON dnv.codProducto = p.cod
    WHERE dnv.nroNotaVenta = p_nro;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_detail_add` (IN `p_nroNotaVenta` INT, IN `p_codProducto` INT, IN `p_cant` INT, IN `p_precioUnitario` DECIMAL(10,2))   BEGIN
    INSERT INTO DetalleNotaVenta (nroNotaVenta, codProducto, cant, precioUnitario)
    VALUES (p_nroNotaVenta, p_codProducto, p_cant, p_precioUnitario);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_find` (IN `p_nro` INT)   BEGIN
    SELECT nv.nro, nv.fecha, nv.ciCliente,
           c.nombres, c.apPaterno, c.apMaterno, c.correo, c.direccion, c.nroCelular,
           nv.rutaInforme
    FROM NotaVenta nv
    INNER JOIN Cliente c ON nv.ciCliente = c.ci
    WHERE nv.nro = p_nro;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_set_report_path` (IN `p_nro` INT, IN `p_ruta` VARCHAR(255))   BEGIN
    UPDATE NotaVenta SET rutaInforme = p_ruta WHERE nro = p_nro;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_total_count` ()   BEGIN
    SELECT COUNT(*) as total FROM NotaVenta;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_total_ingresos` ()   BEGIN
    SELECT COALESCE(SUM(dnv.cant * dnv.precioUnitario), 0) as total
    FROM DetalleNotaVenta dnv;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `cod` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`cod`, `nombre`) VALUES
(4, 'Accesorios'),
(5, 'Calzado'),
(3, 'Smartphones'),
(2, 'Sudaderas'),
(1, 'Vape');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

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

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`ci`, `nombres`, `apPaterno`, `apMaterno`, `correo`, `direccion`, `nroCelular`, `usuarioCuenta`) VALUES
('12345678', 'Juan', 'P??rez', 'Garc??a', 'juan@email.com', 'Calle Ficticia 123', '987654321', 'cliente'),
('87654321', 'Jona', 'User', 'Test', 'jona@email.com', 'Calle Test 456', '123456789', 'Jona'),
('9585486', 'Jonathan', 'Campos', 'Mansilla', 'j29s09s03@gmail.com', 'Mi casa', '69160031', 'Jonathan');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta`
--

CREATE TABLE `cuenta` (
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('cliente','admin') DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuenta`
--

INSERT INTO `cuenta` (`usuario`, `password`, `rol`) VALUES
('admin', '$2y$10$JUA3u5jHv4tejyJ/E2RaIeghU07tuQ2pGsJgbuZ7ur4llnTaAYVG6', 'admin'),
('cliente', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'),
('Jona', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'),
('Jonathan', '$2y$10$JUA3u5jHv4tejyJ/E2RaIeghU07tuQ2pGsJgbuZ7ur4llnTaAYVG6', 'cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallenotaventa`
--

CREATE TABLE `detallenotaventa` (
  `nroNotaVenta` int(11) NOT NULL,
  `codProducto` int(11) NOT NULL,
  `cant` int(11) NOT NULL,
  `precioUnitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detallenotaventa`
--

INSERT INTO `detallenotaventa` (`nroNotaVenta`, `codProducto`, `cant`, `precioUnitario`) VALUES
(3, 4, 1, 299.90),
(4, 1, 1, 249.90),
(4, 2, 1, 189.00),
(4, 3, 2, 999.00),
(4, 4, 5, 299.90),
(5, 3, 1, 999.00),
(5, 4, 1, 299.90),
(6, 3, 1, 999.00),
(6, 4, 1, 299.90),
(7, 3, 1, 999.00),
(7, 4, 1, 299.90);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalleproductosucursal`
--

CREATE TABLE `detalleproductosucursal` (
  `codProducto` int(11) NOT NULL,
  `codSucursal` int(11) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalleproductosucursal`
--

INSERT INTO `detalleproductosucursal` (`codProducto`, `codSucursal`, `stock`) VALUES
(1, 1, 14),
(2, 1, 7),
(3, 1, 0),
(4, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `industria`
--

CREATE TABLE `industria` (
  `cod` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `industria`
--

INSERT INTO `industria` (`cod`, `nombre`) VALUES
(4, 'Deportes'),
(3, 'Electr??nica'),
(2, 'Ropa y Accesorios'),
(1, 'Tecnolog??a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca`
--

CREATE TABLE `marca` (
  `cod` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `marca`
--

INSERT INTO `marca` (`cod`, `nombre`) VALUES
(3, 'Adidas'),
(5, 'Apple'),
(2, 'Nike'),
(4, 'Samsung'),
(1, 'Vaporesso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notaventa`
--

CREATE TABLE `notaventa` (
  `nro` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `ciCliente` varchar(20) DEFAULT NULL,
  `rutaInforme` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notaventa`
--

INSERT INTO `notaventa` (`nro`, `fecha`, `ciCliente`, `rutaInforme`) VALUES
(3, '2026-04-16 17:47:17', '87654321', NULL),
(4, '2026-04-16 17:48:18', '87654321', NULL),
(5, '2026-04-21 12:23:25', '9585486', NULL),
(6, '2026-04-21 12:24:11', '9585486', NULL),
(7, '2026-04-21 12:28:38', '9585486', 'storage/receipts/venta_7_1776774518.pdf');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

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

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`cod`, `nombre`, `descripcion`, `precio`, `imagen`, `estado`, `codMarca`, `codIndustria`, `codCategoria`) VALUES
(1, 'Sudadera Nike', 'Sudadera Nike de algod??n premium', 249.90, 'sudadera_nike.png', 'activo', 2, 2, 2),
(2, 'Vaporesso XROS 3', 'Vape Vaporesso XROS 3 con bater??a de larga duraci??n', 189.00, 'vaporesso_xros3.png', 'activo', 1, 1, 1),
(3, 'iPhone 15', 'Smartphone Apple iPhone 15', 999.00, 'iphone15.png', 'activo', 5, 3, 3),
(4, 'Air Jordan 1', 'Zapatillas Air Jordan 1 retro', 299.90, 'air_jordan1.png', 'activo', 2, 4, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `qr_pagos`
--

CREATE TABLE `qr_pagos` (
  `id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `ciCliente` varchar(20) NOT NULL,
  `carrito` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`carrito`)),
  `estado` enum('pendiente','confirmado','completado','expirado') NOT NULL DEFAULT 'pendiente',
  `nroVenta` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `qr_pagos`
--

INSERT INTO `qr_pagos` (`id`, `token`, `ciCliente`, `carrito`, `estado`, `nroVenta`, `created_at`, `expires_at`) VALUES
(1, '044c5549832ee097774ceed3fddb993ecfc41dbf6a8c8ee368a772176dd36ead', '9585486', '[{\"id\":4,\"cantidad\":1},{\"id\":3,\"cantidad\":1}]', 'confirmado', NULL, '2026-04-21 12:22:30', '2026-04-21 12:27:30'),
(2, '23db442e5b5310fdcea54232b8396d8eb40e4fa41e963605a4461c435c199bd0', '9585486', '[{\"id\":4,\"cantidad\":1},{\"id\":3,\"cantidad\":1}]', 'confirmado', NULL, '2026-04-21 12:24:06', '2026-04-21 12:29:06'),
(3, '46fb92751bfb3ca2b27cf7fd15ea2bfd421d49f227aa3e242a5d71cf8b7b1eb7', '9585486', '[{\"id\":4,\"cantidad\":1},{\"id\":3,\"cantidad\":1}]', 'completado', 7, '2026-04-21 12:28:32', '2026-04-21 12:33:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal`
--

CREATE TABLE `sucursal` (
  `cod` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sucursal`
--

INSERT INTO `sucursal` (`cod`, `nombre`, `direccion`, `telefono`) VALUES
(1, 'Sucursal Central', 'Centro de la ciudad', '123456789');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`cod`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`ci`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `usuarioCuenta` (`usuarioCuenta`);

--
-- Indices de la tabla `cuenta`
--
ALTER TABLE `cuenta`
  ADD PRIMARY KEY (`usuario`);

--
-- Indices de la tabla `detallenotaventa`
--
ALTER TABLE `detallenotaventa`
  ADD PRIMARY KEY (`nroNotaVenta`,`codProducto`),
  ADD KEY `codProducto` (`codProducto`);

--
-- Indices de la tabla `detalleproductosucursal`
--
ALTER TABLE `detalleproductosucursal`
  ADD PRIMARY KEY (`codProducto`,`codSucursal`),
  ADD KEY `codSucursal` (`codSucursal`);

--
-- Indices de la tabla `industria`
--
ALTER TABLE `industria`
  ADD PRIMARY KEY (`cod`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`cod`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `notaventa`
--
ALTER TABLE `notaventa`
  ADD PRIMARY KEY (`nro`),
  ADD KEY `ciCliente` (`ciCliente`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`cod`),
  ADD KEY `codMarca` (`codMarca`),
  ADD KEY `codIndustria` (`codIndustria`),
  ADD KEY `codCategoria` (`codCategoria`);

--
-- Indices de la tabla `qr_pagos`
--
ALTER TABLE `qr_pagos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_token` (`token`),
  ADD KEY `fk_qrpagos_cliente` (`ciCliente`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`cod`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `industria`
--
ALTER TABLE `industria`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `marca`
--
ALTER TABLE `marca`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `notaventa`
--
ALTER TABLE `notaventa`
  MODIFY `nro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `qr_pagos`
--
ALTER TABLE `qr_pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`usuarioCuenta`) REFERENCES `cuenta` (`usuario`);

--
-- Filtros para la tabla `detallenotaventa`
--
ALTER TABLE `detallenotaventa`
  ADD CONSTRAINT `detallenotaventa_ibfk_1` FOREIGN KEY (`nroNotaVenta`) REFERENCES `notaventa` (`nro`),
  ADD CONSTRAINT `detallenotaventa_ibfk_2` FOREIGN KEY (`codProducto`) REFERENCES `producto` (`cod`);

--
-- Filtros para la tabla `detalleproductosucursal`
--
ALTER TABLE `detalleproductosucursal`
  ADD CONSTRAINT `detalleproductosucursal_ibfk_1` FOREIGN KEY (`codProducto`) REFERENCES `producto` (`cod`),
  ADD CONSTRAINT `detalleproductosucursal_ibfk_2` FOREIGN KEY (`codSucursal`) REFERENCES `sucursal` (`cod`);

--
-- Filtros para la tabla `notaventa`
--
ALTER TABLE `notaventa`
  ADD CONSTRAINT `notaventa_ibfk_1` FOREIGN KEY (`ciCliente`) REFERENCES `cliente` (`ci`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`codMarca`) REFERENCES `marca` (`cod`),
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`codIndustria`) REFERENCES `industria` (`cod`),
  ADD CONSTRAINT `producto_ibfk_3` FOREIGN KEY (`codCategoria`) REFERENCES `categoria` (`cod`);

--
-- Filtros para la tabla `qr_pagos`
--
ALTER TABLE `qr_pagos`
  ADD CONSTRAINT `fk_qrpagos_cliente` FOREIGN KEY (`ciCliente`) REFERENCES `cliente` (`ci`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
