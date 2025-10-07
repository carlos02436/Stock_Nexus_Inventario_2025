-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS stock_nexus_inventario_2025
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE stock_nexus_inventario_2025;

-- Tabla: balance_general
CREATE TABLE `balance_general` (
  `id_balance` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_balance` date DEFAULT (CURDATE()),
  `total_ingresos` decimal(14,2) DEFAULT 0.00,
  `total_egresos` decimal(14,2) DEFAULT 0.00,
  `utilidad` decimal(14,2) AS (`total_ingresos` - `total_egresos`) STORED,
  PRIMARY KEY (`id_balance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: categorias
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: clientes
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

-- Tabla: usuarios
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

-- Tabla: proveedores
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

-- Tabla: productos
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


-- Registros de ejemplo para los modulos

INSERT INTO balance_general (fecha_balance, total_ingresos, total_egresos) VALUES
('2025-01-01', 10000.00, 4000.00),
('2025-01-05', 12000.00, 5000.00),
('2025-01-10', 8000.00, 3000.00),
('2025-01-15', 15000.00, 7000.00),
('2025-01-20', 9000.00, 2000.00),
('2025-01-25', 11000.00, 4500.00),
('2025-01-30', 13000.00, 6000.00),
('2025-02-01', 14000.00, 6500.00),
('2025-02-05', 9500.00, 3500.00),
('2025-02-10', 12500.00, 5500.00);

INSERT INTO categorias (nombre_categoria, descripcion) VALUES
('Electrónica', 'Productos electrónicos y accesorios'),
('Muebles', 'Muebles para hogar y oficina'),
('Ropa', 'Prendas de vestir y accesorios'),
('Alimentos', 'Comestibles y bebidas'),
('Bebidas', 'Refrescos y licores'),
('Papelería', 'Artículos de oficina y escolar'),
('Juguetes', 'Juguetes para niños'),
('Deportes', 'Equipamiento deportivo'),
('Belleza', 'Productos de cuidado personal'),
('Hogar', 'Artículos para el hogar');

INSERT INTO clientes (nombre_cliente, identificacion, telefono, correo, direccion, ciudad) VALUES
('Juan Pérez', '123456789', '3001234567', 'juan@example.com', 'Calle 1 #10-20', 'Bogotá'),
('María Gómez', '987654321', '3012345678', 'maria@example.com', 'Carrera 2 #15-30', 'Medellín'),
('Carlos Ramírez', '111222333', '3023456789', 'carlos@example.com', 'Calle 3 #20-10', 'Cali'),
('Ana Torres', '444555666', '3034567890', 'ana@example.com', 'Carrera 4 #25-50', 'Barranquilla'),
('Luis Martínez', '777888999', '3045678901', 'luis@example.com', 'Calle 5 #30-40', 'Cartagena'),
('Sofía Díaz', '222333444', '3056789012', 'sofia@example.com', 'Carrera 6 #35-60', 'Bucaramanga'),
('Andrés López', '555666777', '3067890123', 'andres@example.com', 'Calle 7 #40-70', 'Pereira'),
('Laura Herrera', '888999000', '3078901234', 'laura@example.com', 'Carrera 8 #45-80', 'Manizales'),
('Pedro Sánchez', '333444555', '3089012345', 'pedro@example.com', 'Calle 9 #50-90', 'Ibagué'),
('Valentina Ríos', '666777888', '3090123456', 'valentina@example.com', 'Carrera 10 #55-100', 'Neiva');

INSERT INTO usuarios (nombre_completo, correo, usuario, contrasena, rol) VALUES
('Admin General', 'admin@example.com', 'admin', 'admin123', 'Administrador'),
('Vendedor 1', 'vendedor1@example.com', 'vend1', 'vend123', 'Vendedor'),
('Vendedor 2', 'vendedor2@example.com', 'vend2', 'vend123', 'Vendedor'),
('Contador 1', 'conta1@example.com', 'cont1', 'cont123', 'Contador'),
('Contador 2', 'conta2@example.com', 'cont2', 'cont123', 'Contador'),
('Bodeguero 1', 'bodega1@example.com', 'bod1', 'bod123', 'Bodeguero'),
('Bodeguero 2', 'bodega2@example.com', 'bod2', 'bod123', 'Bodeguero'),
('Vendedor 3', 'vendedor3@example.com', 'vend3', 'vend123', 'Vendedor'),
('Admin Secundario', 'admin2@example.com', 'admin2', 'admin123', 'Administrador'),
('Bodeguero 3', 'bodega3@example.com', 'bod3', 'bod123', 'Bodeguero');

INSERT INTO proveedores (nombre_proveedor, nit, telefono, correo, direccion, ciudad) VALUES
('Provelectro S.A.', '900111222', '3101234567', 'contacto@provelectro.com', 'Calle 100 #10-20', 'Bogotá'),
('Muebles y Más', '900333444', '3112345678', 'ventas@mueblesymas.com', 'Carrera 50 #25-30', 'Medellín'),
('Alimentos del Sur', '900555666', '3123456789', 'info@alimentosdelsur.com', 'Calle 80 #15-20', 'Cali'),
('Distribuciones Caribe', '900777888', '3134567890', 'ventas@caribe.com', 'Carrera 60 #40-50', 'Barranquilla'),
('Ropa Fashion', '900999000', '3145678901', 'contacto@ropafashion.com', 'Calle 70 #30-10', 'Cartagena'),
('Bebidas y Licores', '901111222', '3156789012', 'ventas@bebidas.com', 'Carrera 20 #15-40', 'Bucaramanga'),
('Papelería Total', '901333444', '3167890123', 'info@papeleriatotal.com', 'Calle 30 #40-20', 'Pereira'),
('Juguetería Infantil', '901555666', '3178901234', 'ventas@juguetes.com', 'Carrera 10 #10-30', 'Manizales'),
('Deportes Activos', '901777888', '3189012345', 'info@deportesactivos.com', 'Calle 15 #20-50', 'Ibagué'),
('Belleza y Estilo', '901999000', '3190123456', 'ventas@belleza.com', 'Carrera 25 #30-60', 'Neiva');

INSERT INTO productos (codigo_producto, nombre_producto, id_categoria, descripcion, stock_actual, stock_minimo, unidad_medida, precio_compra, precio_venta) VALUES
('ELEC001', 'Cargador USB', 1, 'Cargador rápido para celulares', 50, 10, 'Unidad', 20.00, 35.00),
('ELEC002', 'Auriculares Bluetooth', 1, 'Auriculares inalámbricos', 30, 5, 'Unidad', 50.00, 80.00),
('MUEB001', 'Silla Oficina', 2, 'Silla ergonómica de oficina', 15, 2, 'Unidad', 120.00, 200.00),
('MUEB002', 'Mesa Comedor', 2, 'Mesa de madera para comedor', 10, 1, 'Unidad', 250.00, 400.00),
('ROPA001', 'Camiseta Hombre', 3, 'Camiseta de algodón talla M', 100, 20, 'Unidad', 15.00, 30.00),
('ALIM001', 'Arroz 1kg', 4, 'Arroz blanco', 200, 50, 'Paquete', 3.00, 5.50),
('BEB001', 'Gaseosa 2L', 5, 'Bebida carbonatada', 80, 20, 'Unidad', 4.00, 7.00),
('PAPE001', 'Cuaderno A4', 6, 'Cuaderno rayado 100 hojas', 150, 30, 'Unidad', 2.00, 4.00),
('JUG001', 'Pelota Futbol', 7, 'Pelota profesional', 25, 5, 'Unidad', 30.00, 55.00),
('DEPO001', 'Raqueta Tenis', 8, 'Raqueta de tenis profesional', 12, 2, 'Unidad', 80.00, 150.00);

INSERT INTO compras (codigo_compra, id_proveedor, id_usuario, total_compra, estado) VALUES
('COMP001', 1, 6, 500.00, 'Pagada'),
('COMP002', 2, 6, 800.00, 'Pendiente'),
('COMP003', 3, 7, 200.00, 'Pagada'),
('COMP004', 4, 7, 1000.00, 'Anulada'),
('COMP005', 5, 6, 300.00, 'Pagada'),
('COMP006', 6, 6, 150.00, 'Pendiente'),
('COMP007', 7, 7, 120.00, 'Pagada'),
('COMP008', 8, 6, 250.00, 'Pagada'),
('COMP009', 9, 7, 400.00, 'Pendiente'),
('COMP010', 10, 6, 700.00, 'Pagada');

INSERT INTO detalle_compras (id_compra, id_producto, cantidad, precio_unitario) VALUES
(1, 1, 10, 20.00),
(2, 2, 5, 50.00),
(3, 3, 2, 120.00),
(4, 4, 1, 250.00),
(5, 5, 20, 15.00),
(6, 6, 50, 3.00),
(7, 7, 10, 4.00),
(8, 8, 30, 2.00),
(9, 9, 5, 30.00),
(10, 10, 2, 80.00);

INSERT INTO ventas (codigo_venta, id_cliente, id_usuario, metodo_pago, total_venta, estado) VALUES
('VENTA001', 1, 2, 'Efectivo', 350.00, 'Pagada'),
('VENTA002', 2, 3, 'Transferencia', 450.00, 'Pendiente'),
('VENTA003', 3, 2, 'Tarjeta', 200.00, 'Pagada'),
('VENTA004', 4, 3, 'Crédito', 500.00, 'Anulada'),
('VENTA005', 5, 2, 'Efectivo', 300.00, 'Pagada'),
('VENTA006', 6, 3, 'Transferencia', 150.00, 'Pendiente'),
('VENTA007', 7, 2, 'Tarjeta', 120.00, 'Pagada'),
('VENTA008', 8, 3, 'Efectivo', 250.00, 'Pagada'),
('VENTA009', 9, 2, 'Transferencia', 400.00, 'Pendiente'),
('VENTA010', 10, 3, 'Crédito', 700.00, 'Pagada');

INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES
(1, 1, 5, 35.00),
(2, 2, 2, 80.00),
(3, 3, 1, 200.00),
(4, 4, 1, 400.00),
(5, 5, 10, 30.00),
(6, 6, 20, 5.50),
(7, 7, 5, 7.00),
(8, 8, 15, 4.00),
(9, 9, 3, 55.00),
(10, 10, 1, 150.00);

INSERT INTO modulos_sistema (nombre_modulo, descripcion, icono, ruta, orden) VALUES
('Usuarios', 'Gestión de usuarios del sistema', 'user', '/usuarios', 1),
('Clientes', 'Gestión de clientes', 'users', '/clientes', 2),
('Proveedores', 'Gestión de proveedores', 'truck', '/proveedores', 3),
('Productos', 'Gestión de productos', 'box', '/productos', 4),
('Compras', 'Registro de compras', 'shopping-cart', '/compras', 5),
('Ventas', 'Registro de ventas', 'cash-register', '/ventas', 6),
('Inventario', 'Control de inventario', 'warehouse', '/inventario', 7),
('Pagos', 'Control de pagos', 'money-bill', '/pagos', 8),
('Reportes', 'Reportes y estadísticas', 'chart-line', '/reportes', 9),
('Configuración', 'Ajustes del sistema', 'cog', '/configuracion', 10);

INSERT INTO permisos_roles (id_rol, id_modulo, puede_ver, puede_crear, puede_editar, puede_eliminar) VALUES
('Administrador', 1, 1, 1, 1, 1),
('Administrador', 2, 1, 1, 1, 1),
('Administrador', 3, 1, 1, 1, 1),
('Administrador', 4, 1, 1, 1, 1),
('Administrador', 5, 1, 1, 1, 1),
('Administrador', 6, 1, 1, 1, 1),
('Administrador', 7, 1, 1, 1, 1),
('Administrador', 8, 1, 1, 1, 1),
('Administrador', 9, 1, 1, 1, 1),
('Administrador', 10, 1, 1, 1, 1);

INSERT INTO movimientos_bodega (id_producto, tipo_movimiento, cantidad, descripcion, id_usuario) VALUES
(1, 'Entrada', 10, 'Compra inicial', 6),
(2, 'Entrada', 5, 'Compra inicial', 6),
(3, 'Entrada', 2, 'Compra inicial', 7),
(4, 'Entrada', 1, 'Compra inicial', 7),
(5, 'Entrada', 20, 'Compra inicial', 6),
(6, 'Entrada', 50, 'Compra inicial', 6),
(7, 'Entrada', 10, 'Compra inicial', 7),
(8, 'Entrada', 30, 'Compra inicial', 6),
(9, 'Entrada', 5, 'Compra inicial', 7),
(10, 'Entrada', 2, 'Compra inicial', 6);

INSERT INTO pagos (tipo_pago, referencia, descripcion, monto, metodo_pago, id_usuario) VALUES
('Ingreso', 'PAG001', 'Pago de cliente Juan Pérez', 350.00, 'Efectivo', 2),
('Ingreso', 'PAG002', 'Pago de cliente María Gómez', 450.00, 'Transferencia', 3),
('Ingreso', 'PAG003', 'Pago de cliente Carlos Ramírez', 200.00, 'Tarjeta', 2),
('Ingreso', 'PAG004', 'Pago de cliente Ana Torres', 500.00, 'Crédito', 3),
('Ingreso', 'PAG005', 'Pago de cliente Luis Martínez', 300.00, 'Efectivo', 2),
('Ingreso', 'PAG006', 'Pago de cliente Sofía Díaz', 150.00, 'Transferencia', 3),
('Ingreso', 'PAG007', 'Pago de cliente Andrés López', 120.00, 'Tarjeta', 2),
('Ingreso', 'PAG008', 'Pago de cliente Laura Herrera', 250.00, 'Efectivo', 3),
('Ingreso', 'PAG009', 'Pago de cliente Pedro Sánchez', 400.00, 'Transferencia', 2),
('Ingreso', 'PAG010', 'Pago de cliente Valentina Ríos', 700.00, 'Crédito', 3);