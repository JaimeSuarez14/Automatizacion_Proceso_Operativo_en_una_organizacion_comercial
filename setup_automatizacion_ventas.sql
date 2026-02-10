-- ===================================================
-- SCRIPT SQL PARA CREAR TABLAS DE SISTEMA DE VENTAS
-- Base de Datos: cevichería
-- ===================================================

-- Tabla de Clientes
CREATE TABLE IF NOT EXISTS clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(15),
    direccion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (email)
);

-- Tabla de Métodos de Pago
--CREATE TABLE IF NOT EXISTS metodos_pago (
--    id_pago INT AUTO_INCREMENT PRIMARY KEY,
--    nombre VARCHAR(50) NOT NULL UNIQUE
--);

-- Tabla de Estados de Pedidos
CREATE TABLE IF NOT EXISTS estadopedido (
    id_estado INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(50) NOT NULL UNIQUE
);

-- Tabla de Platos (debe existir ya)
CREATE TABLE IF NOT EXISTS platos (
    id_plato INT AUTO_INCREMENT PRIMARY KEY,
    nombre_plato VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    imagen VARCHAR(100),
    activo TINYINT DEFAULT 1,
    DATE_CREATED TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_pago INT,
    id_estado INT DEFAULT 1,
    monto_total DECIMAL(10, 2) NOT NULL,
    notas TEXT,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE,
    FOREIGN KEY (id_pago) REFERENCES metodospago(id_pago),
    FOREIGN KEY (id_estado) REFERENCES estadopedido(id_estado),
    INDEX (id_cliente),
    INDEX (fecha_pedido),
    INDEX (id_estado)
);

-- Tabla de Detalles del Pedido
CREATE TABLE IF NOT EXISTS detallepedido (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_plato INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_plato) REFERENCES platos(id_plato),
    INDEX (id_pedido)
);

-- Tabla de Ventas (Registro Histórico)
CREATE TABLE IF NOT EXISTS ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT,
    id_cliente INT,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    monto_total DECIMAL(10, 2),
    estado VARCHAR(50),
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido) ON DELETE SET NULL,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE SET NULL,
    INDEX (fecha_venta)
);

-- ===================================================
-- INSERTAR DATOS INICIALES
-- ===================================================



-- ===================================================
-- CREAR VISTAS ÚTILES PARA REPORTES
-- ===================================================

-- Vista: Resumen de Pedidos
CREATE OR REPLACE VIEW vw_pedidos_detallado AS
SELECT 
    p.id_pedido,
    c.id_cliente,
    c.nombre,
    c.email,
    c.telefono,
    p.fecha_pedido,
    p.monto_total,
    mp.nombre as metodo_pago,
    ep.descripcion as estado,
    COUNT(dp.id_detalle) as cantidad_items
FROM pedidos p
JOIN clientes c ON p.id_cliente = c.id_cliente
LEFT JOIN metodospago mp ON p.id_pago = mp.id_pago
LEFT JOIN estadopedido ep ON p.id_estado = ep.id_estado
LEFT JOIN detallepedido dp ON p.id_pedido = dp.id_pedido
GROUP BY p.id_pedido;

-- Vista: Top Productos Más Vendidos
CREATE OR REPLACE VIEW vw_productos_masvendidos AS
SELECT 
    pl.id_plato,
    pl.nombre_plato,
    pl.precio,
    SUM(dp.cantidad) as cantidad_vendida,
    COUNT(DISTINCT dp.id_pedido) as numero_pedidos,
    ROUND(SUM(dp.subtotal), 2) as ingresos_totales
FROM detallepedido dp
JOIN platos pl ON dp.id_plato = pl.id_plato
GROUP BY pl.id_plato
ORDER BY cantidad_vendida DESC;

-- Vista: Clientes Frecuentes
CREATE OR REPLACE VIEW vw_clientes_frecuentes AS
SELECT 
    c.id_cliente,
    c.nombre,
    c.email,
    COUNT(p.id_pedido) as numero_pedidos,
    ROUND(SUM(p.monto_total), 2) as gasto_total,
    ROUND(AVG(p.monto_total), 2) as gasto_promedio,
    MAX(p.fecha_pedido) as ultima_compra
FROM clientes c
LEFT JOIN pedidos p ON c.id_cliente = p.id_cliente
GROUP BY c.id_cliente
HAVING numero_pedidos > 0;

-- Vista: Ingresos por Mes
CREATE OR REPLACE VIEW vw_ingresos_mensual AS
SELECT 
    DATE_FORMAT(p.fecha_pedido, '%Y-%m') as mes,
    COUNT(*) as numero_pedidos,
    ROUND(SUM(p.monto_total), 2) as ingresos,
    ROUND(AVG(p.monto_total), 2) as promedio,
    MIN(p.fecha_pedido) as fecha_inicio,
    MAX(p.fecha_pedido) as fecha_fin
FROM pedidos p
GROUP BY DATE_FORMAT(p.fecha_pedido, '%Y-%m')
ORDER BY mes DESC;

-- ===================================================
-- CREAR PROCEDIMIENTOS ALMACENADOS ÚTILES
-- ===================================================

-- Procedimiento: Obtener Estadísticas Generales
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS sp_estadisticas_ventas()
BEGIN
    SELECT 
        COUNT(*) as total_pedidos,
        COALESCE(SUM(monto_total), 0) as ingresos_totales,
        ROUND(AVG(monto_total), 2) as promedio_venta,
        (SELECT COUNT(*) FROM clientes) as total_clientes,
        (SELECT COUNT(*) FROM pedidos WHERE id_estado = 1) as pendientes,
        (SELECT COUNT(*) FROM pedidos WHERE DATE(fecha_pedido) = CURDATE()) as pedidos_hoy,
        (SELECT COALESCE(SUM(monto_total), 0) FROM pedidos WHERE DATE(fecha_pedido) = CURDATE()) as ingresos_hoy
    FROM pedidos;
END$$
DELIMITER ;

-- Procedimiento: Registrar Nueva Venta
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS sp_registrar_venta(
    IN p_id_cliente INT,
    IN p_id_pago INT,
    IN p_monto_total DECIMAL(10,2),
    OUT p_id_pedido INT
)
BEGIN
    INSERT INTO pedidos (id_cliente, id_pago, id_estado, monto_total, fecha_pedido)
    VALUES (p_id_cliente, p_id_pago, 1, p_monto_total, NOW());
    
    SET p_id_pedido = LAST_INSERT_ID();
END$$
DELIMITER ;

-- Procedimiento: Obtener Detalle de Pedido
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS sp_detalle_pedido(IN p_id_pedido INT)
BEGIN
    SELECT 
        dp.id_detalle,
        pl.nombre_plato,
        dp.cantidad,
        dp.precio_unitario,
        dp.subtotal
    FROM detallepedido dp
    JOIN platos pl ON dp.id_plato = pl.id_plato
    WHERE dp.id_pedido = p_id_pedido;
END$$
DELIMITER ;

-- ===================================================
-- ÍNDICES PARA OPTIMIZAR CONSULTAS
-- ===================================================

ALTER TABLE pedidos ADD INDEX idx_cliente (id_cliente);
ALTER TABLE pedidos ADD INDEX idx_fecha (fecha_pedido);
ALTER TABLE pedidos ADD INDEX idx_estado (id_estado);
ALTER TABLE detallepedido ADD INDEX idx_pedido (id_pedido);
ALTER TABLE detallepedido ADD INDEX idx_plato (id_plato);
ALTER TABLE ventas ADD INDEX idx_fecha_venta (fecha_venta);
ALTER TABLE clientes ADD INDEX idx_email (email);

-- ===================================================
-- FIN DEL SCRIPT
-- ===================================================
