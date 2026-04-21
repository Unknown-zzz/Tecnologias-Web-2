-- ========================================
-- STORED PROCEDURES - SISTEMA ECOMMERCE
-- ========================================

USE ecommerce;

-- ========== PROCEDIMIENTOS PARA CATEGORÍAS ==========

DELIMITER //

DROP PROCEDURE IF EXISTS sp_categoria_all//
CREATE PROCEDURE sp_categoria_all()
BEGIN
    SELECT * FROM Categoria ORDER BY nombre ASC;
END//

DROP PROCEDURE IF EXISTS sp_categoria_find//
CREATE PROCEDURE sp_categoria_find(IN p_cod INT)
BEGIN
    SELECT * FROM Categoria WHERE cod = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_categoria_create//
CREATE PROCEDURE sp_categoria_create(IN p_nombre VARCHAR(100))
BEGIN
    INSERT INTO Categoria (nombre) VALUES (TRIM(p_nombre));
END//

DROP PROCEDURE IF EXISTS sp_categoria_update//
CREATE PROCEDURE sp_categoria_update(IN p_cod INT, IN p_nombre VARCHAR(100))
BEGIN
    UPDATE Categoria SET nombre = TRIM(p_nombre) WHERE cod = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_categoria_delete//
CREATE PROCEDURE sp_categoria_delete(IN p_cod INT)
BEGIN
    DELETE FROM Categoria WHERE cod = p_cod;
END//

-- ========== PROCEDIMIENTOS PARA MARCAS ==========

DROP PROCEDURE IF EXISTS sp_marca_all//
CREATE PROCEDURE sp_marca_all()
BEGIN
    SELECT * FROM Marca ORDER BY nombre ASC;
END//

DROP PROCEDURE IF EXISTS sp_marca_find//
CREATE PROCEDURE sp_marca_find(IN p_cod INT)
BEGIN
    SELECT * FROM Marca WHERE cod = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_marca_create//
CREATE PROCEDURE sp_marca_create(IN p_nombre VARCHAR(100))
BEGIN
    INSERT INTO Marca (nombre) VALUES (TRIM(p_nombre));
END//

DROP PROCEDURE IF EXISTS sp_marca_update//
CREATE PROCEDURE sp_marca_update(IN p_cod INT, IN p_nombre VARCHAR(100))
BEGIN
    UPDATE Marca SET nombre = TRIM(p_nombre) WHERE cod = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_marca_delete//
CREATE PROCEDURE sp_marca_delete(IN p_cod INT)
BEGIN
    DELETE FROM Marca WHERE cod = p_cod;
END//

-- ========== PROCEDIMIENTOS PARA INDUSTRIAS ==========

DROP PROCEDURE IF EXISTS sp_industria_all//
CREATE PROCEDURE sp_industria_all()
BEGIN
    SELECT * FROM Industria ORDER BY nombre ASC;
END//

DROP PROCEDURE IF EXISTS sp_industria_find//
CREATE PROCEDURE sp_industria_find(IN p_cod INT)
BEGIN
    SELECT * FROM Industria WHERE cod = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_industria_create//
CREATE PROCEDURE sp_industria_create(IN p_nombre VARCHAR(100))
BEGIN
    INSERT INTO Industria (nombre) VALUES (TRIM(p_nombre));
END//

DROP PROCEDURE IF EXISTS sp_industria_update//
CREATE PROCEDURE sp_industria_update(IN p_cod INT, IN p_nombre VARCHAR(100))
BEGIN
    UPDATE Industria SET nombre = TRIM(p_nombre) WHERE cod = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_industria_delete//
CREATE PROCEDURE sp_industria_delete(IN p_cod INT)
BEGIN
    DELETE FROM Industria WHERE cod = p_cod;
END//

-- ========== PROCEDIMIENTOS PARA SUCURSALES ==========

DROP PROCEDURE IF EXISTS sp_sucursal_all//
CREATE PROCEDURE sp_sucursal_all()
BEGIN
    SELECT * FROM Sucursal ORDER BY nombre ASC;
END//

DROP PROCEDURE IF EXISTS sp_sucursal_find//
CREATE PROCEDURE sp_sucursal_find(IN p_cod INT)
BEGIN
    SELECT * FROM Sucursal WHERE cod = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_sucursal_create//
CREATE PROCEDURE sp_sucursal_create(IN p_nombre VARCHAR(100), IN p_direccion VARCHAR(255), IN p_telefono VARCHAR(20))
BEGIN
    INSERT INTO Sucursal (nombre, direccion, telefono) VALUES (TRIM(p_nombre), p_direccion, p_telefono);
END//

DROP PROCEDURE IF EXISTS sp_sucursal_update//
CREATE PROCEDURE sp_sucursal_update(IN p_cod INT, IN p_nombre VARCHAR(100), IN p_direccion VARCHAR(255), IN p_telefono VARCHAR(20))
BEGIN
    UPDATE Sucursal SET nombre = TRIM(p_nombre), direccion = p_direccion, telefono = p_telefono WHERE cod = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_sucursal_delete//
CREATE PROCEDURE sp_sucursal_delete(IN p_cod INT)
BEGIN
    DELETE FROM Sucursal WHERE cod = p_cod;
END//

-- ========== PROCEDIMIENTOS PARA PRODUCTOS ==========

DROP PROCEDURE IF EXISTS sp_producto_all_active//
CREATE PROCEDURE sp_producto_all_active()
BEGIN
    SELECT p.*, m.nombre AS marca, cat.nombre AS categoria, i.nombre AS industria, 
           COALESCE(dps.stock, 0) AS stock
    FROM Producto p
    LEFT JOIN Marca m ON p.codMarca = m.cod
    LEFT JOIN Categoria cat ON p.codCategoria = cat.cod
    LEFT JOIN Industria i ON p.codIndustria = i.cod
    LEFT JOIN DetalleProductoSucursal dps ON p.cod = dps.codProducto AND dps.codSucursal = 1
    WHERE p.estado = 'activo'
    ORDER BY p.cod DESC;
END//

DROP PROCEDURE IF EXISTS sp_producto_all_admin//
CREATE PROCEDURE sp_producto_all_admin()
BEGIN
    SELECT p.*, m.nombre AS marca, cat.nombre AS categoria, i.nombre AS industria,
           COALESCE(dps.stock, 0) AS stock
    FROM Producto p
    LEFT JOIN Marca m ON p.codMarca = m.cod
    LEFT JOIN Categoria cat ON p.codCategoria = cat.cod
    LEFT JOIN Industria i ON p.codIndustria = i.cod
    LEFT JOIN DetalleProductoSucursal dps ON p.cod = dps.codProducto AND dps.codSucursal = 1
    ORDER BY p.cod DESC;
END//

DROP PROCEDURE IF EXISTS sp_producto_find//
CREATE PROCEDURE sp_producto_find(IN p_cod INT)
BEGIN
    SELECT p.*, m.nombre AS marca, cat.nombre AS categoria, i.nombre AS industria
    FROM Producto p
    LEFT JOIN Marca m ON p.codMarca = m.cod
    LEFT JOIN Categoria cat ON p.codCategoria = cat.cod
    LEFT JOIN Industria i ON p.codIndustria = i.cod
    WHERE p.cod = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_producto_create//
CREATE PROCEDURE sp_producto_create(
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_precio DECIMAL(10,2),
    IN p_imagen VARCHAR(255),
    IN p_estado VARCHAR(20),
    IN p_codMarca INT,
    IN p_codIndustria INT,
    IN p_codCategoria INT,
    IN p_stock INT
)
BEGIN
    DECLARE v_codProducto INT;
    
    INSERT INTO Producto (nombre, descripcion, precio, imagen, estado, codMarca, codIndustria, codCategoria)
    VALUES (TRIM(p_nombre), TRIM(p_descripcion), p_precio, p_imagen, p_estado, p_codMarca, p_codIndustria, p_codCategoria);
    
    SET v_codProducto = LAST_INSERT_ID();
    
    IF p_stock > 0 THEN
        INSERT INTO DetalleProductoSucursal (codProducto, codSucursal, stock)
        VALUES (v_codProducto, 1, p_stock);
    END IF;
END//

DROP PROCEDURE IF EXISTS sp_producto_update//
CREATE PROCEDURE sp_producto_update(
    IN p_cod INT,
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_precio DECIMAL(10,2),
    IN p_imagen VARCHAR(255),
    IN p_estado VARCHAR(20),
    IN p_codMarca INT,
    IN p_codIndustria INT,
    IN p_codCategoria INT
)
BEGIN
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
END//

DROP PROCEDURE IF EXISTS sp_producto_delete//
CREATE PROCEDURE sp_producto_delete(IN p_cod INT)
BEGIN
    DELETE FROM Producto WHERE cod = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_producto_add_stock//
CREATE PROCEDURE sp_producto_add_stock(IN p_codProducto INT, IN p_stock INT, IN p_codSucursal INT)
BEGIN
    INSERT INTO DetalleProductoSucursal (codProducto, codSucursal, stock)
    VALUES (p_codProducto, p_codSucursal, p_stock)
    ON DUPLICATE KEY UPDATE stock = p_stock;
END//

-- ========== PROCEDIMIENTOS PARA CLIENTES ==========

DROP PROCEDURE IF EXISTS sp_cliente_all//
CREATE PROCEDURE sp_cliente_all()
BEGIN
    SELECT * FROM Cliente ORDER BY nombres ASC;
END//

DROP PROCEDURE IF EXISTS sp_cliente_find//
CREATE PROCEDURE sp_cliente_find(IN p_ci VARCHAR(20))
BEGIN
    SELECT * FROM Cliente WHERE ci = p_ci;
END//

DROP PROCEDURE IF EXISTS sp_cliente_create//
CREATE PROCEDURE sp_cliente_create(
    IN p_ci VARCHAR(20),
    IN p_nombres VARCHAR(100),
    IN p_apPaterno VARCHAR(50),
    IN p_apMaterno VARCHAR(50),
    IN p_correo VARCHAR(100),
    IN p_direccion TEXT,
    IN p_nroCelular VARCHAR(20),
    IN p_usuarioCuenta VARCHAR(50)
)
BEGIN
    INSERT INTO Cliente (ci, nombres, apPaterno, apMaterno, correo, direccion, nroCelular, usuarioCuenta)
    VALUES (p_ci, TRIM(p_nombres), TRIM(p_apPaterno), TRIM(p_apMaterno), p_correo, p_direccion, p_nroCelular, p_usuarioCuenta);
END//

DROP PROCEDURE IF EXISTS sp_cliente_update//
CREATE PROCEDURE sp_cliente_update(
    IN p_ci VARCHAR(20),
    IN p_nombres VARCHAR(100),
    IN p_apPaterno VARCHAR(50),
    IN p_apMaterno VARCHAR(50),
    IN p_correo VARCHAR(100),
    IN p_direccion TEXT,
    IN p_nroCelular VARCHAR(20)
)
BEGIN
    UPDATE Cliente SET
        nombres = TRIM(p_nombres),
        apPaterno = TRIM(p_apPaterno),
        apMaterno = TRIM(p_apMaterno),
        correo = p_correo,
        direccion = p_direccion,
        nroCelular = p_nroCelular
    WHERE ci = p_ci;
END//

DROP PROCEDURE IF EXISTS sp_cliente_delete//
CREATE PROCEDURE sp_cliente_delete(IN p_ci VARCHAR(20))
BEGIN
    DELETE FROM Cliente WHERE ci = p_ci;
END//

-- ========== PROCEDIMIENTOS PARA CUENTAS ==========

DROP PROCEDURE IF EXISTS sp_cuenta_find//
CREATE PROCEDURE sp_cuenta_find(IN p_usuario VARCHAR(50))
BEGIN
    SELECT * FROM Cuenta WHERE usuario = p_usuario;
END//

DROP PROCEDURE IF EXISTS sp_cuenta_create//
CREATE PROCEDURE sp_cuenta_create(IN p_usuario VARCHAR(50), IN p_password VARCHAR(255), IN p_rol VARCHAR(20))
BEGIN
    INSERT INTO Cuenta (usuario, password, rol) VALUES (p_usuario, p_password, p_rol);
END//

DROP PROCEDURE IF EXISTS sp_cuenta_update_password//
CREATE PROCEDURE sp_cuenta_update_password(IN p_usuario VARCHAR(50), IN p_password VARCHAR(255))
BEGIN
    UPDATE Cuenta SET password = p_password WHERE usuario = p_usuario;
END//

-- ========== PROCEDIMIENTOS PARA VENTAS ==========

DROP PROCEDURE IF EXISTS sp_venta_create//
CREATE PROCEDURE sp_venta_create(IN p_ciCliente VARCHAR(20), OUT p_nro INT)
BEGIN
    INSERT INTO NotaVenta (ciCliente) VALUES (p_ciCliente);
    SET p_nro = LAST_INSERT_ID();
END//

DROP PROCEDURE IF EXISTS sp_venta_detail_add//
CREATE PROCEDURE sp_venta_detail_add(
    IN p_nroNotaVenta INT,
    IN p_codProducto INT,
    IN p_cant INT,
    IN p_precioUnitario DECIMAL(10,2)
)
BEGIN
    INSERT INTO DetalleNotaVenta (nroNotaVenta, codProducto, cant, precioUnitario)
    VALUES (p_nroNotaVenta, p_codProducto, p_cant, p_precioUnitario);
END//

DROP PROCEDURE IF EXISTS sp_venta_all//
CREATE PROCEDURE sp_venta_all()
BEGIN
    SELECT nv.nro, nv.fecha, nv.ciCliente,
           c.nombres, c.apPaterno, c.apMaterno,
           SUM(dnv.cant * dnv.precioUnitario) as total
    FROM NotaVenta nv
    INNER JOIN Cliente c ON nv.ciCliente = c.ci
    INNER JOIN DetalleNotaVenta dnv ON nv.nro = dnv.nroNotaVenta
    GROUP BY nv.nro, nv.fecha, nv.ciCliente, c.nombres, c.apPaterno, c.apMaterno
    ORDER BY nv.fecha DESC;
END//

DROP PROCEDURE IF EXISTS sp_venta_find//
CREATE PROCEDURE sp_venta_find(IN p_nro INT)
BEGIN
    SELECT nv.nro, nv.fecha, nv.ciCliente,
           c.nombres, c.apPaterno, c.apMaterno, c.correo, c.direccion, c.nroCelular,
           nv.rutaInforme
    FROM NotaVenta nv
    INNER JOIN Cliente c ON nv.ciCliente = c.ci
    WHERE nv.nro = p_nro;
END//

DROP PROCEDURE IF EXISTS sp_venta_set_report_path//
CREATE PROCEDURE sp_venta_set_report_path(
    IN p_nro INT,
    IN p_ruta VARCHAR(255)
)
BEGIN
    UPDATE NotaVenta SET rutaInforme = p_ruta WHERE nro = p_nro;
END//

DROP PROCEDURE IF EXISTS sp_venta_details//
CREATE PROCEDURE sp_venta_details(IN p_nro INT)
BEGIN
    SELECT dnv.codProducto, dnv.cant, dnv.precioUnitario,
           p.nombre as producto, p.imagen
    FROM DetalleNotaVenta dnv
    INNER JOIN Producto p ON dnv.codProducto = p.cod
    WHERE dnv.nroNotaVenta = p_nro;
END//

DROP PROCEDURE IF EXISTS sp_venta_total_count//
CREATE PROCEDURE sp_venta_total_count()
BEGIN
    SELECT COUNT(*) as total FROM NotaVenta;
END//

DROP PROCEDURE IF EXISTS sp_venta_total_ingresos//
CREATE PROCEDURE sp_venta_total_ingresos()
BEGIN
    SELECT COALESCE(SUM(dnv.cant * dnv.precioUnitario), 0) as total
    FROM DetalleNotaVenta dnv;
END//

-- ========== PROCEDIMIENTOS PARA PRODUCTOS MÁS VENDIDOS ==========

DROP PROCEDURE IF EXISTS sp_productos_top_vendidos//
CREATE PROCEDURE sp_productos_top_vendidos(IN p_limite INT)
BEGIN
    SELECT 
        p.cod,
        p.nombre,
        p.descripcion,
        p.precio,
        p.imagen,
        m.nombre AS marca,
        cat.nombre AS categoria,
        i.nombre AS industria,
        COALESCE(dps.stock, 0) AS stock,
        COALESCE(SUM(dnv.cant), 0) AS total_vendidos,
        COALESCE(SUM(dnv.cant * dnv.precioUnitario), 0) AS ingresos_totales
    FROM Producto p
    LEFT JOIN Marca m ON p.codMarca = m.cod
    LEFT JOIN Categoria cat ON p.codCategoria = cat.cod
    LEFT JOIN Industria i ON p.codIndustria = i.cod
    LEFT JOIN DetalleProductoSucursal dps ON p.cod = dps.codProducto AND dps.codSucursal = 1
    LEFT JOIN DetalleNotaVenta dnv ON p.cod = dnv.codProducto
    WHERE p.estado = 'activo'
    GROUP BY p.cod, p.nombre, p.descripcion, p.precio, p.imagen, m.nombre, cat.nombre, i.nombre, dps.stock
    ORDER BY total_vendidos DESC, ingresos_totales DESC
    LIMIT p_limite;
END//

DELIMITER ;
