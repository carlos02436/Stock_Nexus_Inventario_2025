-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS stock_nexus_inventario_2025
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE stock_nexus_inventario_2025;

-- --------------------------------------------------------
-- Tabla: balance_general
-- --------------------------------------------------------
CREATE TABLE `balance_general` (
  `id_balance` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_balance` date DEFAULT (CURDATE()),
  `total_ingresos` decimal(14,2) DEFAULT 0.00,
  `total_egresos` decimal(14,2) DEFAULT 0.00,
  `utilidad` decimal(14,2) AS (`total_ingresos` - `total_egresos`) STORED,
  PRIMARY KEY (`id_balance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla: categorias
-- --------------------------------------------------------
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla: clientes
-- --------------------------------------------------------
CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_cliente` varchar(100) NOT NULL,
  `identificacion` varchar(30) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `uk_clientes_identificacion` (`identificacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla: usuarios
-- --------------------------------------------------------
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('Administrador','Vendedor','Contador','Bodeguero') DEFAULT 'Vendedor',
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `uk_usuarios_correo` (`correo`),
  UNIQUE KEY `uk_usuarios_usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla: proveedores
-- --------------------------------------------------------
CREATE TABLE `proveedores` (
  `id_proveedor` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_proveedor` varchar(100) NOT NULL,
  `nit` varchar(30) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_proveedor`),
  UNIQUE KEY `uk_proveedores_nit` (`nit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla: productos
-- --------------------------------------------------------
CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_producto` varchar(50) NOT NULL,
  `nombre_producto` varchar(150) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `stock_actual` decimal(10,2) DEFAULT 0.00,
  `stock_minimo` decimal(10,2) DEFAULT 0.00,
  `unidad_medida` varchar(30) DEFAULT 'Unidad',
  `precio_compra` decimal(12,2) DEFAULT 0.00,
  `precio_venta` decimal(12,2) DEFAULT 0.00,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  PRIMARY KEY (`id_producto`),
  UNIQUE KEY `uk_productos_codigo` (`codigo_producto`),
  KEY `idx_productos_id_categoria` (`id_categoria`),
  CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Ahora las tablas que referencian a las anteriores
-- --------------------------------------------------------

-- Tabla: compras
CREATE TABLE `compras` (
  `id_compra` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_compra` varchar(50) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_compra` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_compra` decimal(14,2) DEFAULT 0.00,
  `estado` enum('Pendiente','Pagada','Anulada') DEFAULT 'Pendiente',
  PRIMARY KEY (`id_compra`),
  UNIQUE KEY `uk_compras_codigo` (`codigo_compra`),
  KEY `idx_compras_id_proveedor` (`id_proveedor`),
  KEY `idx_compras_id_usuario` (`id_usuario`),
  CONSTRAINT `fk_compras_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`),
  CONSTRAINT `fk_compras_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: detalle_compras
CREATE TABLE `detalle_compras` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_compra` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(14,2) AS (`cantidad` * `precio_unitario`) STORED,
  PRIMARY KEY (`id_detalle`),
  KEY `idx_detcomp_id_compra` (`id_compra`),
  KEY `idx_detcomp_id_producto` (`id_producto`),
  CONSTRAINT `fk_detcompras_compra` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`),
  CONSTRAINT `fk_detcompras_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: ventas
CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_venta` varchar(50) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_venta` datetime DEFAULT CURRENT_TIMESTAMP,
  `metodo_pago` enum('Efectivo','Transferencia','Tarjeta','Crédito') DEFAULT 'Efectivo',
  `total_venta` decimal(14,2) DEFAULT 0.00,
  `estado` enum('Pendiente','Pagada','Anulada') DEFAULT 'Pendiente',
  PRIMARY KEY (`id_venta`),
  UNIQUE KEY `uk_ventas_codigo` (`codigo_venta`),
  KEY `idx_ventas_cliente` (`id_cliente`),
  KEY `idx_ventas_usuario` (`id_usuario`),
  CONSTRAINT `fk_ventas_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `fk_ventas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: detalle_ventas
CREATE TABLE `detalle_ventas` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(14,2) AS (`cantidad` * `precio_unitario`) STORED,
  PRIMARY KEY (`id_detalle`),
  KEY `idx_detven_id_venta` (`id_venta`),
  KEY `idx_detven_id_producto` (`id_producto`),
  CONSTRAINT `fk_detventas_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`),
  CONSTRAINT `fk_detventas_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: modulos_sistema
CREATE TABLE `modulos_sistema` (
  `id_modulo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_modulo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `ruta` varchar(100) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `orden` int(11) DEFAULT 0,
  PRIMARY KEY (`id_modulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: permisos_roles
CREATE TABLE `permisos_roles` (
  `id_permiso` int(11) NOT NULL AUTO_INCREMENT,
  `id_rol` varchar(50) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `puede_ver` tinyint(1) DEFAULT 1,
  `puede_crear` tinyint(1) DEFAULT 0,
  `puede_editar` tinyint(1) DEFAULT 0,
  `puede_eliminar` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id_permiso`),
  KEY `idx_permisos_modulo` (`id_modulo`),
  CONSTRAINT `fk_permisos_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulos_sistema` (`id_modulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: movimientos_bodega
CREATE TABLE `movimientos_bodega` (
  `id_movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `tipo_movimiento` enum('Entrada','Salida','Ajuste') NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_movimiento` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_movimiento`),
  KEY `idx_movimientos_producto` (`id_producto`),
  KEY `idx_movimientos_usuario` (`id_usuario`),
  CONSTRAINT `fk_movimientos_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`),
  CONSTRAINT `fk_movimientos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: pagos
CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_pago` enum('Ingreso','Egreso') NOT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `monto` decimal(14,2) NOT NULL,
  `fecha_pago` datetime DEFAULT CURRENT_TIMESTAMP,
  `metodo_pago` enum('Efectivo','Transferencia','Tarjeta') DEFAULT 'Efectivo',
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `idx_pagos_usuario` (`id_usuario`),
  CONSTRAINT `fk_pagos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Insertar datos de ejemplo (solo para las tablas que ya existían antes)
-- --------------------------------------------------------

INSERT INTO `balance_general` (`fecha_balance`, `total_ingresos`, `total_egresos`)
VALUES
('2025-01-01', 500000.00, 300000.00),
('2025-02-01', 720000.00, 450000.00),
('2025-03-01', 600000.00, 320000.00),
('2025-04-01', 800000.00, 500000.00),
('2025-05-01', 700000.00, 400000.00),
('2025-06-01', 900000.00, 550000.00),
('2025-07-01', 850000.00, 480000.00),
('2025-08-01', 880000.00, 600000.00),
('2025-09-01', 920000.00, 620000.00),
('2025-10-01', 950000.00, 650000.00);

INSERT INTO `categorias` (`nombre_categoria`, `descripcion`, `estado`)
VALUES
('Bebidas', 'Refrescos, jugos, cervezas y aguas embotelladas', 'Activo'),
('Alimentos', 'Comestibles y abarrotes en general', 'Activo'),
('Limpieza', 'Artículos de aseo y desinfección', 'Activo'),
('Electrodomésticos', 'Pequeños aparatos eléctricos', 'Activo'),
('Ferretería', 'Herramientas y materiales', 'Activo'),
('Papelería', 'Útiles de oficina y escolares', 'Activo'),
('Tecnología', 'Equipos y accesorios electrónicos', 'Activo'),
('Ropa', 'Prendas de vestir y calzado', 'Activo'),
('Repuestos', 'Componentes mecánicos y eléctricos', 'Activo'),
('Otros', 'Artículos varios y misceláneos', 'Activo');

INSERT INTO `clientes` (`nombre_cliente`, `identificacion`, `telefono`, `correo`, `direccion`, `ciudad`, `fecha_registro`)
VALUES
('Comercial S.A.', '900101001-1', '3201112233', 'ventas@comercial.com', 'Cra 10 #25-30', 'Bogotá', '2025-10-06 15:42:05'),
('Tienda El Sol', '800220032-2', '3123334455', 'contacto@elsol.com', 'Calle 12 #8-10', 'Medellín', '2025-10-06 15:42:05'),
('Ferretería La 33', '901330045-3', '3105556677', 'info@la33.com', 'Av 33 #45-67', 'Cali', '2025-10-06 15:42:05'),
('Restaurante El Buen Sabor', '902440056-4', '3112223344', 'gerencia@buen-sabor.com', 'Cl 40 #21-80', 'Barranquilla', '2025-10-06 15:42:05'),
('Panadería Don Pan', '901550067-5', '3006667788', 'ventas@donpan.com', 'Cl 3 #12-40', 'Cartagena', '2025-10-06 15:42:05'),
('Insumos Agropecuarios SAS', '900660078-6', '3208889999', 'info@agroinsumos.com', 'Cra 80 #14-22', 'Bucaramanga', '2025-10-06 15:42:05'),
('Café La Palma', '900770089-7', '3100001111', 'admin@lapalma.com', 'Cl 50 #23-14', 'Pereira', '2025-10-06 15:42:05'),
('Supermercado Central', '900880090-8', '3113332222', 'contacto@central.com', 'Av 5 #67-10', 'Santa Marta', '2025-10-06 15:42:05'),
('Distribuidora Del Norte', '900990101-9', '3007778888', 'ventas@norte.com', 'Cra 12 #45-15', 'Cúcuta', '2025-10-06 15:42:05'),
('Hotel Bahía', '901110112-0', '3202221111', 'reservas@bahia.com', 'Cl 25 #20-50', 'Valledupar', '2025-10-06 15:42:05');

INSERT INTO `usuarios` (`nombre_completo`, `correo`, `usuario`, `contrasena`, `rol`, `estado`, `fecha_creacion`)
VALUES
('Carlos Parra', 'carlos@stocknexus.com', 'carlos_admin', '123456', 'Administrador', 'Activo', '2025-10-06 15:42:05'),
('Juan Pérez', 'juan@stocknexus.com', 'juan_vendedor', '123456', 'Vendedor', 'Activo', '2025-10-06 15:42:05'),
('Ana Gómez', 'ana@stocknexus.com', 'ana_contadora', '123456', 'Contador', 'Activo', '2025-10-06 15:42:05'),
('Luis Herrera', 'luis@stocknexus.com', 'luis_bodega', '123456', 'Bodeguero', 'Activo', '2025-10-06 15:42:05'),
('María Rojas', 'maria@stocknexus.com', 'maria_vendedora', '123456', 'Vendedor', 'Activo', '2025-10-06 15:42:05'),
('David López', 'david@stocknexus.com', 'david_admin', '123456', 'Administrador', 'Activo', '2025-10-06 15:42:05'),
('Sofía Castro', 'sofia@stocknexus.com', 'sofia_cont', '123456', 'Contador', 'Activo', '2025-10-06 15:42:05'),
('Pedro Ramírez', 'pedro@stocknexus.com', 'pedro_bodega', '123456', 'Bodeguero', 'Activo', '2025-10-06 15:42:05'),
('Laura Torres', 'laura@stocknexus.com', 'laura_vend', '123456', 'Vendedor', 'Activo', '2025-10-06 15:42:05'),
('Andrés Díaz', 'andres@stocknexus.com', 'andres_admin', '123456', 'Administrador', 'Activo', '2025-10-06 15:42:05');

-- INSERT para compras
INSERT INTO `compras` (`codigo_compra`, `id_proveedor`, `id_usuario`, `fecha_compra`, `total_compra`, `estado`)
VALUES
('C001', 1, 2, '2025-10-06 15:42:07', 350000.00, 'Pagada'),
('C002', 2, 2, '2025-10-06 15:42:07', 420000.00, 'Pagada'),
('C003', 3, 2, '2025-10-06 15:42:07', 290000.00, 'Pendiente'),
('C004', 4, 2, '2025-10-06 15:42:07', 315000.00, 'Pagada'),
('C005', 5, 2, '2025-10-06 15:42:07', 175000.00, 'Pendiente'),
('C006', 6, 2, '2025-10-06 15:42:07', 212000.00, 'Pagada'),
('C007', 7, 2, '2025-10-06 15:42:07', 280000.00, 'Pagada'),
('C008', 8, 2, '2025-10-06 15:42:07', 360000.00, 'Pagada'),
('C009', 9, 2, '2025-10-06 15:42:07', 190000.00, 'Pendiente'),
('C010', 10, 2, '2025-10-06 15:42:07', 450000.00, 'Pagada');

-- INSERT para detalle_compras
INSERT INTO `detalle_compras` (`id_compra`, `id_producto`, `cantidad`, `precio_unitario`)
VALUES
(1, 1, 100.00, 800.00),
(2, 2, 50.00, 2000.00),
(3, 3, 40.00, 12000.00),
(4, 4, 60.00, 7000.00),
(5, 5, 80.00, 2500.00),
(6, 6, 30.00, 4000.00),
(7, 7, 100.00, 2500.00),
(8, 8, 20.00, 10000.00),
(9, 9, 60.00, 2500.00),
(10, 10, 25.00, 3500.00);

-- INSERT para ventas
INSERT INTO `ventas` (`codigo_venta`, `id_cliente`, `id_usuario`, `fecha_venta`, `metodo_pago`, `total_venta`, `estado`)
VALUES
('V001', 1, 2, '2025-10-06 15:42:08', 'Efectivo', 45000.00, 'Pagada'),
('V002', 2, 2, '2025-10-06 15:42:08', 'Tarjeta', 72000.00, 'Pagada'),
('V003', 3, 2, '2025-10-06 15:42:08', 'Efectivo', 55000.00, 'Pendiente'),
('V004', 4, 2, '2025-10-06 15:42:08', 'Transferencia', 120000.00, 'Pagada'),
('V005', 5, 2, '2025-10-06 15:42:08', 'Efectivo', 30000.00, 'Pagada'),
('V006', 6, 2, '2025-10-06 15:42:08', 'Crédito', 90000.00, 'Pendiente'),
('V007', 7, 2, '2025-10-06 15:42:08', 'Efectivo', 75000.00, 'Pagada'),
('V008', 8, 2, '2025-10-06 15:42:08', 'Transferencia', 68000.00, 'Pagada'),
('V009', 9, 2, '2025-10-06 15:42:08', 'Efectivo', 49000.00, 'Pagada'),
('V010', 10, 2, '2025-10-06 15:42:08', 'Crédito', 115000.00, 'Pendiente');

-- INSERT para detalle_ventas
INSERT INTO `detalle_ventas` (`id_venta`, `id_producto`, `cantidad`, `precio_unitario`)
VALUES
(1, 1, 10.00, 1500.00),
(2, 2, 20.00, 3500.00),
(3, 3, 3.00, 18000.00),
(4, 4, 8.00, 10500.00),
(5, 5, 5.00, 4200.00),
(6, 6, 4.00, 7500.00),
(7, 7, 15.00, 6000.00),
(8, 8, 5.00, 18000.00),
(9, 9, 8.00, 4500.00),
(10, 10, 10.00, 8000.00);

-- INSERT para movimientos_bodega
INSERT INTO `movimientos_bodega` (`id_producto`, `tipo_movimiento`, `cantidad`, `descripcion`, `id_usuario`, `fecha_movimiento`)
VALUES
(1, 'Entrada', 150.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(2, 'Entrada', 100.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(3, 'Entrada', 80.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(4, 'Entrada', 90.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(5, 'Entrada', 120.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(6, 'Entrada', 60.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(7, 'Entrada', 200.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(8, 'Entrada', 40.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(9, 'Entrada', 90.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(10, 'Entrada', 50.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07');

-- INSERT para pagos
INSERT INTO `pagos` (`tipo_pago`, `referencia`, `descripcion`, `monto`, `fecha_pago`, `metodo_pago`, `id_usuario`)
VALUES
('Ingreso', 'V001', 'Venta al cliente Comercial S.A.', 45000.00, '2025-10-06 15:42:08', 'Efectivo', 3),
('Ingreso', 'V002', 'Venta al cliente Tienda El Sol', 72000.00, '2025-10-06 15:42:08', 'Tarjeta', 3),
('Ingreso', 'V004', 'Venta al cliente Buen Sabor', 120000.00, '2025-10-06 15:42:08', 'Transferencia', 3),
('Egreso', 'C001', 'Compra a Proveedora Andina', 350000.00, '2025-10-06 15:42:08', 'Transferencia', 3),
('Egreso', 'C002', 'Compra a Distribuciones El Valle', 420000.00, '2025-10-06 15:42:08', 'Efectivo', 3),
('Ingreso', 'V005', 'Venta al cliente Don Pan', 30000.00, '2025-10-06 15:42:08', 'Efectivo', 3),
('Egreso', 'C006', 'Compra a Papelería Central', 212000.00, '2025-10-06 15:42:08', 'Tarjeta', 3),
('Ingreso', 'V007', 'Venta al cliente La Palma', 75000.00, '2025-10-06 15:42:08', 'Efectivo', 3),
('Egreso', 'C008', 'Compra a TecnoDistribuciones', 360000.00, '2025-10-06 15:42:08', 'Transferencia', 3),
('Ingreso', 'V009', 'Venta a Distribuidora del Norte', 49000.00, '2025-10-06 15:42:08', 'Efectivo', 3);

-- INSERT para modulos_sistema
INSERT INTO `modulos_sistema` (`nombre_modulo`, `descripcion`, `icono`, `ruta`, `estado`, `orden`)
VALUES
('Dashboard', 'Panel principal del sistema', 'fas fa-tachometer-alt', 'dashboard', 'Activo', 1),
('Productos', 'Gestión de productos e inventario', 'fas fa-boxes', 'productos', 'Activo', 2),
('Categorías', 'Gestión de categorías de productos', 'fas fa-tags', 'categorias', 'Activo', 3),
('Proveedores', 'Gestión de proveedores', 'fas fa-truck', 'proveedores', 'Activo', 4),
('Compras', 'Registro y gestión de compras', 'fas fa-shopping-cart', 'compras', 'Activo', 5),
('Ventas', 'Registro y gestión de ventas', 'fas fa-cash-register', 'ventas', 'Activo', 6),
('Clientes', 'Gestión de clientes', 'fas fa-users', 'clientes', 'Activo', 7),
('Movimientos', 'Movimientos de bodega', 'fas fa-exchange-alt', 'movimientos', 'Activo', 8),
('Finanzas', 'Gestión financiera y pagos', 'fas fa-chart-line', 'finanzas', 'Activo', 9),
('Reportes', 'Generación de reportes', 'fas fa-chart-bar', 'reportes', 'Activo', 10),
('Usuarios', 'Gestión de usuarios del sistema', 'fas fa-user-cog', 'usuarios', 'Activo', 11),
('Configuración', 'Configuración del sistema', 'fas fa-cogs', 'configuracion', 'Activo', 12);

-- INSERT para permisos_roles
INSERT INTO `permisos_roles` (`id_rol`, `id_modulo`, `puede_ver`, `puede_crear`, `puede_editar`, `puede_eliminar`)
VALUES
('Administrador', 1, 1, 1, 1, 1),
('Administrador', 2, 1, 1, 1, 1),
('Administrador', 3, 1, 1, 1, 1),
('Administrador', 4, 1, 1, 1, 1),
('Administrador', 5, 1, 1, 1, 1),
('Administrador', 6, 1, 1, 1, 1),
('Administrador', 7, 1, 1, 1, 1),
('Administrador', 8, 1, 1, 1, 1),
('Administrador', 9, 1, 1, 1, 1),
('Administrador', 10, 1, 1, 1, 1),
('Administrador', 11, 1, 1, 1, 1),
('Administrador', 12, 1, 1, 1, 1),
('Vendedor', 1, 1, 0, 0, 0),
('Vendedor', 2, 1, 0, 0, 0),
('Vendedor', 6, 1, 1, 0, 0),
('Vendedor', 7, 1, 1, 1, 0),
('Vendedor', 10,1, 1, 0, 0),
('Contador', 1, 1, 0, 0, 0),
('Contador', 5, 1, 0, 0, 0),
('Contador', 6, 1, 0, 0, 0),
('Contador', 9, 1, 1, 1, 0),
('Contador',10,1, 1, 0, 0),
('Bodeguero',1, 1, 0, 0, 0),
('Bodeguero',2, 1, 1, 1, 0),
('Bodeguero',3, 1, 0, 0, 0),
('Bodeguero',5, 1, 1, 0, 0),
('Bodeguero',8, 1, 1, 0, 0);

COMMIT;
-- --------------------------------------------------------
