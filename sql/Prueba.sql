-- ============================================================
-- Base de datos: ecommerce
-- Versión del servidor: MariaDB 10.4.32
-- Generado: Script limpio - solo estructura, SPs y datos base
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================
-- PROCEDIMIENTOS ALMACENADOS
-- ============================================================

DELIMITER $$

-- ----- Categoría -----

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_categoria_all` ()
BEGIN
    SELECT * FROM Categoria ORDER BY nombre ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_categoria_create` (IN `p_nombre` VARCHAR(100))
BEGIN
    INSERT INTO Categoria (nombre) VALUES (TRIM(p_nombre));
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_categoria_delete` (IN `p_cod` INT)
BEGIN
    DELETE FROM Categoria WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_categoria_find` (IN `p_cod` INT)
BEGIN
    SELECT * FROM Categoria WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_categoria_update` (IN `p_cod` INT, IN `p_nombre` VARCHAR(100))
BEGIN
    UPDATE Categoria SET nombre = TRIM(p_nombre) WHERE cod = p_cod;
END$$

-- ----- Cliente -----

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cliente_all` ()
BEGIN
    SELECT * FROM Cliente ORDER BY nombres ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cliente_create` (
    IN `p_ci` VARCHAR(20),
    IN `p_nombres` VARCHAR(100),
    IN `p_apPaterno` VARCHAR(50),
    IN `p_apMaterno` VARCHAR(50),
    IN `p_correo` VARCHAR(100),
    IN `p_direccion` TEXT,
    IN `p_nroCelular` VARCHAR(20),
    IN `p_usuarioCuenta` VARCHAR(50)
)
BEGIN
    INSERT INTO Cliente (ci, nombres, apPaterno, apMaterno, correo, direccion, nroCelular, usuarioCuenta)
    VALUES (p_ci, TRIM(p_nombres), TRIM(p_apPaterno), TRIM(p_apMaterno), p_correo, p_direccion, p_nroCelular, p_usuarioCuenta);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cliente_delete` (IN `p_ci` VARCHAR(20))
BEGIN
    DELETE FROM Cliente WHERE ci = p_ci;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cliente_find` (IN `p_ci` VARCHAR(20))
BEGIN
    SELECT * FROM Cliente WHERE ci = p_ci;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cliente_update` (
    IN `p_ci` VARCHAR(20),
    IN `p_nombres` VARCHAR(100),
    IN `p_apPaterno` VARCHAR(50),
    IN `p_apMaterno` VARCHAR(50),
    IN `p_correo` VARCHAR(100),
    IN `p_direccion` TEXT,
    IN `p_nroCelular` VARCHAR(20)
)
BEGIN
    UPDATE Cliente SET
        nombres    = TRIM(p_nombres),
        apPaterno  = TRIM(p_apPaterno),
        apMaterno  = TRIM(p_apMaterno),
        correo     = p_correo,
        direccion  = p_direccion,
        nroCelular = p_nroCelular
    WHERE ci = p_ci;
END$$

-- ----- Cuenta -----

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cuenta_create` (
    IN `p_usuario` VARCHAR(50),
    IN `p_password` VARCHAR(255),
    IN `p_rol` VARCHAR(20)
)
BEGIN
    INSERT INTO Cuenta (usuario, password, rol) VALUES (p_usuario, p_password, p_rol);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cuenta_find` (IN `p_usuario` VARCHAR(50))
BEGIN
    SELECT * FROM Cuenta WHERE usuario = p_usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cuenta_update_password` (
    IN `p_usuario` VARCHAR(50),
    IN `p_password` VARCHAR(255)
)
BEGIN
    UPDATE Cuenta SET password = p_password WHERE usuario = p_usuario;
END$$

-- ----- Industria -----

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_industria_all` ()
BEGIN
    SELECT * FROM Industria ORDER BY nombre ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_industria_create` (IN `p_nombre` VARCHAR(100))
BEGIN
    INSERT INTO Industria (nombre) VALUES (TRIM(p_nombre));
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_industria_delete` (IN `p_cod` INT)
BEGIN
    DELETE FROM Industria WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_industria_find` (IN `p_cod` INT)
BEGIN
    SELECT * FROM Industria WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_industria_update` (IN `p_cod` INT, IN `p_nombre` VARCHAR(100))
BEGIN
    UPDATE Industria SET nombre = TRIM(p_nombre) WHERE cod = p_cod;
END$$

-- ----- Marca -----

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_marca_all` ()
BEGIN
    SELECT * FROM Marca ORDER BY nombre ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_marca_create` (IN `p_nombre` VARCHAR(100))
BEGIN
    INSERT INTO Marca (nombre) VALUES (TRIM(p_nombre));
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_marca_delete` (IN `p_cod` INT)
BEGIN
    DELETE FROM Marca WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_marca_find` (IN `p_cod` INT)
BEGIN
    SELECT * FROM Marca WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_marca_update` (IN `p_cod` INT, IN `p_nombre` VARCHAR(100))
BEGIN
    UPDATE Marca SET nombre = TRIM(p_nombre) WHERE cod = p_cod;
END$$

-- ----- Producto -----

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_productos_top_vendidos` (IN `p_limite` INT)
BEGIN
    SELECT
        p.cod,
        p.nombre,
        p.descripcion,
        p.precio,
        p.imagen,
        m.nombre   AS marca,
        cat.nombre AS categoria,
        i.nombre   AS industria,
        COALESCE(dps.stock, 0)                        AS stock,
        COALESCE(SUM(dnv.cant), 0)                    AS total_vendidos,
        COALESCE(SUM(dnv.cant * dnv.precioUnitario), 0) AS ingresos_totales
    FROM Producto p
    LEFT JOIN Marca m                  ON p.codMarca     = m.cod
    LEFT JOIN Categoria cat            ON p.codCategoria = cat.cod
    LEFT JOIN Industria i              ON p.codIndustria = i.cod
    LEFT JOIN DetalleProductoSucursal dps ON p.cod = dps.codProducto AND dps.codSucursal = 1
    LEFT JOIN DetalleNotaVenta dnv     ON p.cod = dnv.codProducto
    WHERE p.estado = 'activo'
    GROUP BY p.cod, p.nombre, p.descripcion, p.precio, p.imagen,
             m.nombre, cat.nombre, i.nombre, dps.stock
    ORDER BY total_vendidos DESC, ingresos_totales DESC
    LIMIT p_limite;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_add_stock` (
    IN `p_codProducto` INT,
    IN `p_stock` INT,
    IN `p_codSucursal` INT
)
BEGIN
    INSERT INTO DetalleProductoSucursal (codProducto, codSucursal, stock)
    VALUES (p_codProducto, p_codSucursal, p_stock)
    ON DUPLICATE KEY UPDATE stock = p_stock;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_all_active` ()
BEGIN
    SELECT p.*, m.nombre AS marca, cat.nombre AS categoria, i.nombre AS industria,
           COALESCE(dps.stock, 0) AS stock
    FROM Producto p
    LEFT JOIN Marca m                     ON p.codMarca     = m.cod
    LEFT JOIN Categoria cat               ON p.codCategoria = cat.cod
    LEFT JOIN Industria i                 ON p.codIndustria = i.cod
    LEFT JOIN DetalleProductoSucursal dps ON p.cod = dps.codProducto AND dps.codSucursal = 1
    WHERE p.estado = 'activo'
    ORDER BY p.cod DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_all_admin` ()
BEGIN
    SELECT p.*, m.nombre AS marca, cat.nombre AS categoria, i.nombre AS industria,
           COALESCE(dps.stock, 0) AS stock
    FROM Producto p
    LEFT JOIN Marca m                     ON p.codMarca     = m.cod
    LEFT JOIN Categoria cat               ON p.codCategoria = cat.cod
    LEFT JOIN Industria i                 ON p.codIndustria = i.cod
    LEFT JOIN DetalleProductoSucursal dps ON p.cod = dps.codProducto AND dps.codSucursal = 1
    ORDER BY p.cod DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_create` (
    IN `p_nombre` VARCHAR(100),
    IN `p_descripcion` TEXT,
    IN `p_precio` DECIMAL(10,2),
    IN `p_imagen` VARCHAR(255),
    IN `p_estado` VARCHAR(20),
    IN `p_codMarca` INT,
    IN `p_codIndustria` INT,
    IN `p_codCategoria` INT,
    IN `p_stock` INT
)
BEGIN
    DECLARE v_codProducto INT;

    INSERT INTO Producto (nombre, descripcion, precio, imagen, estado, codMarca, codIndustria, codCategoria)
    VALUES (TRIM(p_nombre), TRIM(p_descripcion), p_precio, p_imagen, p_estado,
            p_codMarca, p_codIndustria, p_codCategoria);

    SET v_codProducto = LAST_INSERT_ID();

    IF p_stock > 0 THEN
        INSERT INTO DetalleProductoSucursal (codProducto, codSucursal, stock)
        VALUES (v_codProducto, 1, p_stock);
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_delete` (IN `p_cod` INT)
BEGIN
    DELETE FROM Producto WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_find` (IN `p_cod` INT)
BEGIN
    SELECT p.*, m.nombre AS marca, cat.nombre AS categoria, i.nombre AS industria
    FROM Producto p
    LEFT JOIN Marca m       ON p.codMarca     = m.cod
    LEFT JOIN Categoria cat ON p.codCategoria = cat.cod
    LEFT JOIN Industria i   ON p.codIndustria = i.cod
    WHERE p.cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_producto_update` (
    IN `p_cod` INT,
    IN `p_nombre` VARCHAR(100),
    IN `p_descripcion` TEXT,
    IN `p_precio` DECIMAL(10,2),
    IN `p_imagen` VARCHAR(255),
    IN `p_estado` VARCHAR(20),
    IN `p_codMarca` INT,
    IN `p_codIndustria` INT,
    IN `p_codCategoria` INT
)
BEGIN
    UPDATE Producto SET
        nombre       = TRIM(p_nombre),
        descripcion  = TRIM(p_descripcion),
        precio       = p_precio,
        imagen       = p_imagen,
        estado       = p_estado,
        codMarca     = p_codMarca,
        codIndustria = p_codIndustria,
        codCategoria = p_codCategoria
    WHERE cod = p_cod;
END$$

-- ----- Sucursal -----

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sucursal_all` ()
BEGIN
    SELECT * FROM Sucursal ORDER BY nombre ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sucursal_create` (
    IN `p_nombre` VARCHAR(100),
    IN `p_direccion` VARCHAR(255),
    IN `p_telefono` VARCHAR(20)
)
BEGIN
    INSERT INTO Sucursal (nombre, direccion, telefono) VALUES (TRIM(p_nombre), p_direccion, p_telefono);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sucursal_delete` (IN `p_cod` INT)
BEGIN
    DELETE FROM Sucursal WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sucursal_find` (IN `p_cod` INT)
BEGIN
    SELECT * FROM Sucursal WHERE cod = p_cod;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sucursal_update` (
    IN `p_cod` INT,
    IN `p_nombre` VARCHAR(100),
    IN `p_direccion` VARCHAR(255),
    IN `p_telefono` VARCHAR(20)
)
BEGIN
    UPDATE Sucursal SET nombre = TRIM(p_nombre), direccion = p_direccion, telefono = p_telefono WHERE cod = p_cod;
END$$

-- ----- Venta -----

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_all` ()
BEGIN
    SELECT nv.nro, nv.fecha, nv.ciCliente,
           c.nombres, c.apPaterno, c.apMaterno,
           SUM(dnv.cant * dnv.precioUnitario) AS total
    FROM NotaVenta nv
    INNER JOIN Cliente c         ON nv.ciCliente    = c.ci
    INNER JOIN DetalleNotaVenta dnv ON nv.nro       = dnv.nroNotaVenta
    GROUP BY nv.nro, nv.fecha, nv.ciCliente, c.nombres, c.apPaterno, c.apMaterno
    ORDER BY nv.fecha DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_create` (
    IN `p_ciCliente` VARCHAR(20),
    OUT `p_nro` INT
)
BEGIN
    INSERT INTO NotaVenta (ciCliente) VALUES (p_ciCliente);
    SET p_nro = LAST_INSERT_ID();
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_details` (IN `p_nro` INT)
BEGIN
    SELECT dnv.codProducto, dnv.cant, dnv.precioUnitario,
           p.nombre AS producto, p.imagen
    FROM DetalleNotaVenta dnv
    INNER JOIN Producto p ON dnv.codProducto = p.cod
    WHERE dnv.nroNotaVenta = p_nro;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_detail_add` (
    IN `p_nroNotaVenta` INT,
    IN `p_codProducto` INT,
    IN `p_cant` INT,
    IN `p_precioUnitario` DECIMAL(10,2)
)
BEGIN
    INSERT INTO DetalleNotaVenta (nroNotaVenta, codProducto, cant, precioUnitario)
    VALUES (p_nroNotaVenta, p_codProducto, p_cant, p_precioUnitario);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_find` (IN `p_nro` INT)
BEGIN
    SELECT nv.nro, nv.fecha, nv.ciCliente,
           c.nombres, c.apPaterno, c.apMaterno, c.correo, c.direccion, c.nroCelular,
           nv.rutaInforme
    FROM NotaVenta nv
    INNER JOIN Cliente c ON nv.ciCliente = c.ci
    WHERE nv.nro = p_nro;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_set_report_path` (
    IN `p_nro` INT,
    IN `p_ruta` VARCHAR(255)
)
BEGIN
    UPDATE NotaVenta SET rutaInforme = p_ruta WHERE nro = p_nro;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_total_count` ()
BEGIN
    SELECT COUNT(*) AS total FROM NotaVenta;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_venta_total_ingresos` ()
BEGIN
    SELECT COALESCE(SUM(dnv.cant * dnv.precioUnitario), 0) AS total
    FROM DetalleNotaVenta dnv;
END$$

DELIMITER ;

-- ============================================================
-- ESTRUCTURA DE TABLAS
-- ============================================================

-- --------------------------------------------------------
-- Tabla: Categoria
-- --------------------------------------------------------
CREATE TABLE `categoria` (
  `cod`    int(11)      NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`cod`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla: Cuenta
-- --------------------------------------------------------
CREATE TABLE `cuenta` (
  `usuario`  varchar(50)  NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol`      enum('cliente','admin') DEFAULT 'cliente',
  PRIMARY KEY (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla: Cliente
-- --------------------------------------------------------
CREATE TABLE `cliente` (
  `ci`            varchar(20)  NOT NULL,
  `nombres`       varchar(100) NOT NULL,
  `apPaterno`     varchar(50)  NOT NULL,
  `apMaterno`     varchar(50)  DEFAULT NULL,
  `correo`        varchar(100) DEFAULT NULL,
  `direccion`     text         DEFAULT NULL,
  `nroCelular`    varchar(20)  DEFAULT NULL,
  `usuarioCuenta` varchar(50)  DEFAULT NULL,
  PRIMARY KEY (`ci`),
  UNIQUE KEY `correo` (`correo`),
  KEY `usuarioCuenta` (`usuarioCuenta`),
  CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`usuarioCuenta`) REFERENCES `cuenta` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla: Industria
-- --------------------------------------------------------
CREATE TABLE `industria` (
  `cod`    int(11)      NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`cod`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla: Marca
-- --------------------------------------------------------
CREATE TABLE `marca` (
  `cod`    int(11)      NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`cod`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla: Sucursal
-- --------------------------------------------------------
CREATE TABLE `sucursal` (
  `cod`       int(11)      NOT NULL AUTO_INCREMENT,
  `nombre`    varchar(100) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono`  varchar(20)  DEFAULT NULL,
  PRIMARY KEY (`cod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla: Producto
-- --------------------------------------------------------
CREATE TABLE `producto` (
  `cod`          int(11)         NOT NULL AUTO_INCREMENT,
  `nombre`       varchar(100)    NOT NULL,
  `descripcion`  text            DEFAULT NULL,
  `precio`       decimal(10,2)   NOT NULL,
  `imagen`       varchar(255)    DEFAULT NULL,
  `estado`       enum('activo','inactivo') DEFAULT 'activo',
  `codMarca`     int(11)         DEFAULT NULL,
  `codIndustria` int(11)         DEFAULT NULL,
  `codCategoria` int(11)         DEFAULT NULL,
  PRIMARY KEY (`cod`),
  KEY `codMarca`     (`codMarca`),
  KEY `codIndustria` (`codIndustria`),
  KEY `codCategoria` (`codCategoria`),
  CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`codMarca`)     REFERENCES `marca`     (`cod`),
  CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`codIndustria`) REFERENCES `industria` (`cod`),
  CONSTRAINT `producto_ibfk_3` FOREIGN KEY (`codCategoria`) REFERENCES `categoria` (`cod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla: DetalleProductoSucursal
-- --------------------------------------------------------
CREATE TABLE `detalleproductosucursal` (
  `codProducto` int(11) NOT NULL,
  `codSucursal` int(11) NOT NULL,
  `stock`       int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`codProducto`,`codSucursal`),
  KEY `codSucursal` (`codSucursal`),
  CONSTRAINT `detalleproductosucursal_ibfk_1` FOREIGN KEY (`codProducto`) REFERENCES `producto`  (`cod`),
  CONSTRAINT `detalleproductosucursal_ibfk_2` FOREIGN KEY (`codSucursal`) REFERENCES `sucursal`  (`cod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla: NotaVenta
-- --------------------------------------------------------
CREATE TABLE `notaventa` (
  `nro`         int(11)      NOT NULL AUTO_INCREMENT,
  `fecha`       timestamp    NOT NULL DEFAULT current_timestamp(),
  `ciCliente`   varchar(20)  DEFAULT NULL,
  `rutaInforme` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nro`),
  KEY `ciCliente` (`ciCliente`),
  CONSTRAINT `notaventa_ibfk_1` FOREIGN KEY (`ciCliente`) REFERENCES `cliente` (`ci`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla: DetalleNotaVenta
-- --------------------------------------------------------
CREATE TABLE `detallenotaventa` (
  `nroNotaVenta`  int(11)       NOT NULL,
  `codProducto`   int(11)       NOT NULL,
  `cant`          int(11)       NOT NULL,
  `precioUnitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`nroNotaVenta`,`codProducto`),
  KEY `codProducto` (`codProducto`),
  CONSTRAINT `detallenotaventa_ibfk_1` FOREIGN KEY (`nroNotaVenta`) REFERENCES `notaventa` (`nro`),
  CONSTRAINT `detallenotaventa_ibfk_2` FOREIGN KEY (`codProducto`)  REFERENCES `producto`  (`cod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla: QR_Pagos
-- --------------------------------------------------------
CREATE TABLE `qr_pagos` (
  `id`         int(11)      NOT NULL AUTO_INCREMENT,
  `token`      varchar(64)  NOT NULL,
  `ciCliente`  varchar(20)  NOT NULL,
  `carrito`    longtext     CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
                            CHECK (json_valid(`carrito`)),
  `estado`     enum('pendiente','confirmado','completado','expirado') NOT NULL DEFAULT 'pendiente',
  `nroVenta`   int(11)      DEFAULT NULL,
  `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_token` (`token`),
  KEY `fk_qrpagos_cliente` (`ciCliente`),
  KEY `idx_estado` (`estado`),
  CONSTRAINT `fk_qrpagos_cliente` FOREIGN KEY (`ciCliente`)
      REFERENCES `cliente` (`ci`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- DATOS BASE
-- ============================================================

-- --------------------------------------------------------
-- Cuenta admin
-- Contraseña hasheada: admin123  (bcrypt $2y$10$...)
-- --------------------------------------------------------
INSERT INTO `cuenta` (`usuario`, `password`, `rol`) VALUES
('admin', '$2y$10$JUA3u5jHv4tejyJ/E2RaIeghU07tuQ2pGsJgbuZ7ur4llnTaAYVG6', 'admin');

-- --------------------------------------------------------
-- Sucursal por defecto (requerida por los SPs y el stock)
-- --------------------------------------------------------
INSERT INTO `sucursal` (`nombre`, `direccion`, `telefono`) VALUES
('Sucursal Central', 'Centro de la ciudad', '123456789');

-- --------------------------------------------------------
-- Industrias para electrodomésticos
-- --------------------------------------------------------
INSERT INTO `industria` (`nombre`) VALUES
('Electrodomesticos'),
('Linea Blanca'),
('Climatizacion'),
('Audio y Video');

-- --------------------------------------------------------
-- Categorías para electrodomésticos
-- --------------------------------------------------------
INSERT INTO `categoria` (`nombre`) VALUES
('Refrigeracion'),
('Lavado'),
('Coccion'),
('Climatizacion'),
('Audio y Television'),
('Cuidado Personal'),
('Aspiradoras'),
('Pequenos Electrodomesticos');

-- --------------------------------------------------------
-- Marcas reales de electrodomésticos
-- --------------------------------------------------------
INSERT INTO `marca` (`nombre`) VALUES
('Samsung'),
('LG'),
('Whirlpool'),
('Mabe'),
('Bosch'),
('Panasonic'),
('Sony'),
('Electrolux');

-- --------------------------------------------------------
-- 20 Productos de electrodomésticos con datos reales
--
-- Referencia de IDs tras los inserts anteriores:
--   Industrias: 1=Electrodomesticos, 2=Linea Blanca, 3=Climatizacion, 4=Audio y Video
--   Categorias: 1=Refrigeracion, 2=Lavado, 3=Coccion, 4=Climatizacion,
--               5=Audio y Television, 6=Cuidado Personal, 7=Aspiradoras, 8=Pequenos Electrodomesticos
--   Marcas:     1=Samsung, 2=LG, 3=Whirlpool, 4=Mabe, 5=Bosch, 6=Panasonic, 7=Sony, 8=Electrolux
-- --------------------------------------------------------
INSERT INTO `producto` (`nombre`, `descripcion`, `precio`, `imagen`, `estado`, `codMarca`, `codIndustria`, `codCategoria`) VALUES
-- Refrigeración
('Samsung RT38K5010S8 Refrigeradora No Frost 380L',
 'Refrigeradora de 2 puertas No Frost 380 litros, compresor Digital Inverter, clase energetica A+, dispensador de agua, color plateado.',
 3299.00, NULL, 'activo', 1, 2, 1),

('LG GB45SPT Refrigeradora Top Freezer 418L',
 'Refrigeradora LG Top Freezer 418 litros con tecnologia Linear Compressor, No Frost total, Smart Diagnosis, ahorro de energia A++.',
 3799.00, NULL, 'activo', 2, 2, 1),

('Mabe RME360FZMRX0 Refrigeradora French Door 360L',
 'Refrigeradora French Door 360 litros, No Frost, dispensador de agua y hielo, control de temperatura electronico, color inox.',
 4599.00, NULL, 'activo', 4, 2, 1),

-- Lavado
('Samsung WW90T4040CE Lavadora 9kg Inverter',
 'Lavadora carga frontal 9 kg, motor Digital Inverter, ciclo Burbuja Activa, 14 programas de lavado, clase A+++, 1400 rpm.',
 3199.00, NULL, 'activo', 1, 2, 2),

('LG F4WV3009S6W Lavadora 9kg AI Direct Drive',
 'Lavadora carga frontal 9 kg con motor AI Direct Drive sin correa, autolimpieza del tambor, WiFi ThinQ, clase energetica A, 1400 rpm.',
 3499.00, NULL, 'activo', 2, 2, 2),

('Whirlpool FWLF71082W Lavasecadora 7kg/5kg',
 'Lavasecadora carga frontal 7 kg de lavado / 5 kg de secado, tecnologia 6th Sense, vapor anti-arrugas, 1000 rpm.',
 4199.00, NULL, 'activo', 3, 2, 2),

-- Cocción
('Mabe EME7660CBOX0 Cocina 6 Quemadores',
 'Cocina a gas 6 quemadores de acero inoxidable, horno con grill y luz interior, encendido electronico en todos los quemadores, capacidad horno 85 litros.',
 2599.00, NULL, 'activo', 4, 2, 3),

('Bosch HBA534BB0 Horno Electrico Empotrable 71L',
 'Horno electrico empotrable 71 litros, 8 funciones de coccion, limpieza pirolitica, panel tactil, temperatura hasta 275 grados, clase A.',
 3899.00, NULL, 'activo', 5, 2, 3),

('Panasonic NN-ST34HMZPE Microondas 25L Inverter',
 'Microondas 25 litros con tecnologia Inverter para coccion uniforme, 800 W de potencia, grill, 11 niveles de potencia, panel digital.',
 899.00, NULL, 'activo', 6, 1, 3),

-- Climatización
('LG S18EQ Aire Acondicionado Split Inverter 18000 BTU',
 'Aire acondicionado Split Inverter 18000 BTU frio/calor, compresor Dual Inverter, clase A++, modo Auto Cleaning, control WiFi desde smartphone.',
 4299.00, NULL, 'activo', 2, 3, 4),

('Samsung AR18TXHQASINEU Split Inverter WindFree 18000 BTU',
 'Aire acondicionado Samsung WindFree 18000 BTU, tecnologia sin viento directo, compresor Digital Inverter, filtro antipolvo, WiFi integrado.',
 4799.00, NULL, 'activo', 1, 3, 4),

('Electrolux EXI18F2HMVT Split Inverter 18000 BTU',
 'Aire acondicionado Split Inverter 18000 BTU, tecnologia Hi-Wall, filtro Greenguard antipolvo y antibacterial, control remoto con WiFi, clase A.',
 3899.00, NULL, 'activo', 8, 3, 4),

-- Audio y Televisión
('Sony KD-55X80K Smart TV 4K 55 pulgadas',
 'Smart TV Sony 55" 4K Ultra HD con procesador X1, Google TV, Dolby Vision, HDR, Triluminos Pro, Android TV 11, panel de 60 Hz.',
 4199.00, NULL, 'activo', 7, 4, 5),

('Samsung QN55Q70C QLED Smart TV 55 pulgadas',
 'Smart TV Samsung QLED 55" 4K con procesador Quantum 4K, Quantum HDR, Motion Xcelerator, Tizen OS, OTS Lite, panel 120 Hz.',
 5499.00, NULL, 'activo', 1, 4, 5),

('LG OLED55C3PSA Smart TV OLED 4K 55 pulgadas',
 'Smart TV LG OLED 55" 4K con panel OLED evo, procesador alfa9 Gen6 AI, Dolby Vision IQ, Dolby Atmos, webOS 23, 120 Hz, cuatro puertos HDMI 2.1.',
 7499.00, NULL, 'activo', 2, 4, 5),

('Sony HT-S400 Barra de Sonido 2.1 330W',
 'Barra de sonido con subwoofer inalambrico 330 W RMS, Dolby Audio, S-Force Pro Front Surround, Bluetooth 5.0, entrada optica y HDMI ARC.',
 1299.00, NULL, 'activo', 7, 4, 5),

-- Pequeños electrodomésticos
('Bosch TKA6A044 Cafetera de Goteo 1200W 10 Tazas',
 'Cafetera de goteo 1200 W, capacidad 1,25 litros (10 tazas), funcion anti-goteo, jarra de vidrio con asa ergonomica, placa de calentamiento.',
 499.00, NULL, 'activo', 5, 1, 8),

('Panasonic NN-E28JMMBPQ Microondas Solo 23L 800W',
 'Microondas compacto 23 litros 800 W, 5 niveles de potencia, temporizador hasta 30 minutos, descongelacion por peso, color blanco.',
 649.00, NULL, 'activo', 6, 1, 8),

-- Cuidado personal
('Philips GC4867 Plancha de Vapor PerfectCare 2400W',
 'Plancha de vapor PerfectCare 2400 W, suela OptimalTEMP sin temperatura que ajustar, vapor continuo 45 g/min, golpe de vapor 200 g, deposito 350 ml.',
 799.00, NULL, 'activo', 6, 1, 6),

-- Aspiradoras
('LG A9K-ULTRA2X Aspiradora Sin Cable Power Cord-Zero',
 'Aspiradora inalambrica 200 W, succion de 210 W, autonomia hasta 80 minutos, filtro HEPA 13, motor de 5 velocidades, kit completo de accesorios.',
 2499.00, NULL, 'activo', 2, 1, 7);

-- --------------------------------------------------------
-- Stock inicial en Sucursal Central (cod=1) para los 20 productos
-- Los IDs de producto arrancan en 1 ya que la tabla queda limpia
-- --------------------------------------------------------
INSERT INTO `detalleproductosucursal` (`codProducto`, `codSucursal`, `stock`) VALUES
(1,  1, 30),
(2,  1, 25),
(3,  1, 20),
(4,  1, 35),
(5,  1, 30),
(6,  1, 15),
(7,  1, 40),
(8,  1, 12),
(9,  1, 50),
(10, 1, 25),
(11, 1, 20),
(12, 1, 18),
(13, 1, 22),
(14, 1, 15),
(15, 1, 10),
(16, 1, 28),
(17, 1, 60),
(18, 1, 55),
(19, 1, 45),
(20, 1, 18);

-- ============================================================
-- USUARIOS Y CLIENTES (5 cuentas reales de ejemplo)
-- Todas las contraseñas son "password123" hasheadas con bcrypt
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- ============================================================

INSERT INTO `cuenta` (`usuario`, `password`, `rol`) VALUES
('mgarcia',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'),
('lmamani',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'),
('cflores',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'),
('rquispe',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'),
('avargas',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente');

INSERT INTO `cliente` (`ci`, `nombres`, `apPaterno`, `apMaterno`, `correo`, `direccion`, `nroCelular`, `usuarioCuenta`) VALUES
('7823401',  'Maria Elena',  'Garcia',   'Quispe',   'mgarcia@gmail.com',    'Av. Camacho 1452, La Paz',           '72341089',  'mgarcia'),
('6195034',  'Luis Alberto', 'Mamani',   'Condori',  'lmamani@hotmail.com',  'Calle Murillo 345, El Alto',         '67891234',  'lmamani'),
('8034512',  'Carmen Rosa',  'Flores',   'Torrez',   'cflores@gmail.com',    'Av. Buenos Aires 890, La Paz',       '71256789',  'cflores'),
('5978623',  'Roberto',      'Quispe',   'Alcon',    'rquispe@yahoo.com',    'Calle Sagarnaga 223, La Paz',        '76543210',  'rquispe'),
('9102847',  'Ana Lucia',    'Vargas',   'Mendoza',  'avargas@gmail.com',    'Av. Arce 2140, Zona Sopocachi',      '69874512',  'avargas');

-- ============================================================
-- NOTAS DE VENTA (8 ventas en diferentes fechas)
-- Simulan actividad normal de las últimas semanas
-- ============================================================

INSERT INTO `notaventa` (`nro`, `fecha`, `ciCliente`, `rutaInforme`) VALUES
(1,  '2026-03-05 09:15:22', '7823401', 'storage/receipts/venta_1_1741168522.pdf'),
(2,  '2026-03-12 11:42:10', '6195034', 'storage/receipts/venta_2_1741773730.pdf'),
(3,  '2026-03-18 15:30:05', '8034512', 'storage/receipts/venta_3_1742312205.pdf'),
(4,  '2026-03-25 10:08:47', '5978623', 'storage/receipts/venta_4_1742896127.pdf'),
(5,  '2026-04-01 16:55:33', '7823401', 'storage/receipts/venta_5_1743522933.pdf'),
(6,  '2026-04-07 09:22:18', '9102847', 'storage/receipts/venta_6_1744017738.pdf'),
(7,  '2026-04-14 14:10:50', '6195034', 'storage/receipts/venta_7_1744639850.pdf'),
(8,  '2026-04-19 17:45:03', '8034512', 'storage/receipts/venta_8_1745085903.pdf');

-- ============================================================
-- DETALLE DE NOTAS DE VENTA
-- Precios reflejan los valores de los productos al momento de la venta
-- ============================================================

-- Venta 1 | Maria Garcia | 05-mar | Compra TV + Barra de sonido
INSERT INTO `detallenotaventa` (`nroNotaVenta`, `codProducto`, `cant`, `precioUnitario`) VALUES
(1, 13, 1, 4199.00),   -- Sony KD-55X80K Smart TV 55"
(1, 16, 1, 1299.00);   -- Sony HT-S400 Barra de Sonido

-- Venta 2 | Luis Mamani | 12-mar | Lavadora + Microondas
INSERT INTO `detallenotaventa` (`nroNotaVenta`, `codProducto`, `cant`, `precioUnitario`) VALUES
(2, 4,  1, 3199.00),   -- Samsung WW90T4040CE Lavadora 9kg
(2, 9,  1,  899.00);   -- Panasonic NN-ST34HMZPE Microondas 25L

-- Venta 3 | Carmen Flores | 18-mar | Refrigeradora + Cocina
INSERT INTO `detallenotaventa` (`nroNotaVenta`, `codProducto`, `cant`, `precioUnitario`) VALUES
(3, 1,  1, 3299.00),   -- Samsung RT38K5010S8 Refrigeradora 380L
(3, 7,  1, 2599.00);   -- Mabe EME7660CBOX0 Cocina 6 Quemadores

-- Venta 4 | Roberto Quispe | 25-mar | Aire Acondicionado
INSERT INTO `detallenotaventa` (`nroNotaVenta`, `codProducto`, `cant`, `precioUnitario`) VALUES
(4, 10, 1, 4299.00),   -- LG S18EQ Aire Acondicionado 18000 BTU
(4, 17, 1,  499.00);   -- Bosch TKA6A044 Cafetera 10 Tazas

-- Venta 5 | Maria Garcia | 01-abr | Segunda compra: Lavadora
INSERT INTO `detallenotaventa` (`nroNotaVenta`, `codProducto`, `cant`, `precioUnitario`) VALUES
(5, 5,  1, 3499.00),   -- LG F4WV3009S6W Lavadora 9kg AI
(5, 19, 1,  799.00);   -- Philips GC4867 Plancha de Vapor

-- Venta 6 | Ana Vargas | 07-abr | TV OLED + Aspiradora
INSERT INTO `detallenotaventa` (`nroNotaVenta`, `codProducto`, `cant`, `precioUnitario`) VALUES
(6, 15, 1, 7499.00),   -- LG OLED55C3PSA Smart TV OLED 55"
(6, 20, 1, 2499.00);   -- LG A9K-ULTRA2X Aspiradora Sin Cable

-- Venta 7 | Luis Mamani | 14-abr | Horno + Cafetera
INSERT INTO `detallenotaventa` (`nroNotaVenta`, `codProducto`, `cant`, `precioUnitario`) VALUES
(7, 8,  1, 3899.00),   -- Bosch HBA534BB0 Horno Electrico Empotrable
(7, 17, 2,  499.00);   -- Bosch TKA6A044 Cafetera (x2)

-- Venta 8 | Carmen Flores | 19-abr | Aire Acondicionado Samsung + Microondas
INSERT INTO `detallenotaventa` (`nroNotaVenta`, `codProducto`, `cant`, `precioUnitario`) VALUES
(8, 11, 1, 4799.00),   -- Samsung AR18TXHQASINEU Split Inverter WindFree
(8, 18, 1,  649.00);   -- Panasonic NN-E28JMMBPQ Microondas Solo 23L

-- ============================================================
-- QR_PAGOS correspondientes a las ventas (todos completados)
-- ============================================================

INSERT INTO `qr_pagos` (`id`, `token`, `ciCliente`, `carrito`, `estado`, `nroVenta`, `created_at`, `expires_at`) VALUES
(1, 'a1f3c2e8d9b047a6f51c3e2d8b9047a6f51c3e2d8b9047a6f51c3e2d8b9047a', '7823401',
 '[{"id":13,"cantidad":1},{"id":16,"cantidad":1}]',
 'completado', 1, '2026-03-05 09:10:00', '2026-03-05 09:15:00'),

(2, 'b2e4d3f9e0c158b7e62d4f3e9c0158b7e62d4f3e9c0158b7e62d4f3e9c0158b', '6195034',
 '[{"id":4,"cantidad":1},{"id":9,"cantidad":1}]',
 'completado', 2, '2026-03-12 11:38:00', '2026-03-12 11:43:00'),

(3, 'c3f5e4a0f1d269c8f73e5a4f0d1269c8f73e5a4f0d1269c8f73e5a4f0d1269c', '8034512',
 '[{"id":1,"cantidad":1},{"id":7,"cantidad":1}]',
 'completado', 3, '2026-03-18 15:25:00', '2026-03-18 15:30:00'),

(4, 'd4a6f5b1a2e370d9a84f6b5a1e2370d9a84f6b5a1e2370d9a84f6b5a1e2370d', '5978623',
 '[{"id":10,"cantidad":1},{"id":17,"cantidad":1}]',
 'completado', 4, '2026-03-25 10:03:00', '2026-03-25 10:08:00'),

(5, 'e5b7a6c2b3f481e0b95a7c6b2f3481e0b95a7c6b2f3481e0b95a7c6b2f3481e', '7823401',
 '[{"id":5,"cantidad":1},{"id":19,"cantidad":1}]',
 'completado', 5, '2026-04-01 16:50:00', '2026-04-01 16:55:00'),

(6, 'f6c8b7d3c4a592f1c06b8d7c3a4592f1c06b8d7c3a4592f1c06b8d7c3a4592f', '9102847',
 '[{"id":15,"cantidad":1},{"id":20,"cantidad":1}]',
 'completado', 6, '2026-04-07 09:17:00', '2026-04-07 09:22:00'),

(7, 'a7d9c8e4d5b603a2d17c9e8d4b5603a2d17c9e8d4b5603a2d17c9e8d4b5603a', '6195034',
 '[{"id":8,"cantidad":1},{"id":17,"cantidad":2}]',
 'completado', 7, '2026-04-14 14:05:00', '2026-04-14 14:10:00'),

(8, 'b8e0d9f5e6c714b3e28d0f9e5c6714b3e28d0f9e5c6714b3e28d0f9e5c6714b', '8034512',
 '[{"id":11,"cantidad":1},{"id":18,"cantidad":1}]',
 'completado', 8, '2026-04-19 17:40:00', '2026-04-19 17:45:00');

-- ============================================================
-- Ajuste AUTO_INCREMENT para evitar conflictos al insertar nuevos registros
-- ============================================================
ALTER TABLE `notaventa`  AUTO_INCREMENT = 9;
ALTER TABLE `qr_pagos`   AUTO_INCREMENT = 9;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;