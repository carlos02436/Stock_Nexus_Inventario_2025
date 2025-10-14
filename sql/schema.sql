-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-10-2025 a las 20:32:44
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `stock_nexus_inventario_2025`
--
CREATE DATABASE IF NOT EXISTS `stock_nexus_inventario_2025` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `stock_nexus_inventario_2025`;

-- --------------------------------------------------------

DELIMITER $$

--
-- Estructura de tabla para la tabla `balance_general`
--

CREATE TABLE IF NOT EXISTS `balance_general` (
  `id_balance` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_balance` date DEFAULT curdate(),
  `total_ingresos` decimal(14,2) DEFAULT 0.00,
  `total_egresos` decimal(14,2) DEFAULT 0.00,
  `utilidad` decimal(14,2) GENERATED ALWAYS AS (`total_ingresos` - `total_egresos`) STORED,
  PRIMARY KEY (`id_balance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci$$

--
-- Volcado de datos para la tabla `balance_general`
--

INSERT INTO `balance_general` (`id_balance`, `fecha_balance`, `total_ingresos`, `total_egresos`) VALUES
(1, '2025-01-01', 10000.00, 4000.00),
(2, '2025-01-05', 12000.00, 5000.00),
(3, '2025-01-10', 8000.00, 3000.00),
(4, '2025-01-15', 15000.00, 7000.00),
(5, '2025-01-20', 9000.00, 2000.00),
(6, '2025-01-25', 11000.00, 4500.00),
(7, '2025-01-30', 13000.00, 6000.00),
(8, '2025-02-01', 14000.00, 6500.00),
(9, '2025-02-05', 9500.00, 3500.00),
(10, '2025-02-10', 12500.00, 5500.00)$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE IF NOT EXISTS `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci$$

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`, `descripcion`, `estado`) VALUES
(1, 'Electrónica', 'Productos electrónicos y accesorios', 'Activo'),
(2, 'Muebles', 'Muebles para hogar y oficina', 'Activo'),
(3, 'Ropa', 'Prendas de vestir y accesorios', 'Activo'),
(4, 'Alimentos', 'Comestibles y bebidas', 'Activo'),
(5, 'Bebidas', 'Refrescos y licores', 'Activo'),
(6, 'Papelería', 'Artículos de oficina y escolar', 'Activo'),
(7, 'Juguetes', 'Juguetes para niños', 'Activo'),
(8, 'Deportes', 'Equipamiento deportivo', 'Activo'),
(9, 'Belleza', 'Productos de cuidado personal', 'Activo'),
(10, 'Hogar', 'Artículos para el hogar', 'Activo'),
(11, 'dulces', 'golosinas', 'Activo'),
(12, 'jugos', 'jugos naturales', 'Activo')$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE IF NOT EXISTS `clientes` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_cliente` varchar(100) NOT NULL,
  `identificacion` varchar(30) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `uk_clientes_identificacion` (`identificacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci$$

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre_cliente`, `identificacion`, `telefono`, `correo`, `direccion`, `ciudad`, `fecha_registro`) VALUES
(1, 'Juan Pérez', '123456789', '3001234567', 'juan@example.com', 'Calle 1 #10-20', 'Bogotá', '2025-10-07 14:26:30'),
(2, 'María Gómez', '987654321', '3012345678', 'maria@example.com', 'Carrera 2 #15-30', 'Medellín', '2025-10-07 14:26:30'),
(3, 'Carlos Ramírez', '111222333', '3023456789', 'carlos@example.com', 'Calle 3 #20-10', 'Cali', '2025-10-07 14:26:30'),
(4, 'Ana Torres', '444555666', '3034567890', 'ana@example.com', 'Carrera 4 #25-50', 'Barranquilla', '2025-10-07 14:26:30'),
(5, 'Luis Martínez', '777888999', '3045678901', 'luis@example.com', 'Calle 5 #30-40', 'Cartagena', '2025-10-07 14:26:30'),
(6, 'Sofía Díaz', '222333444', '3056789012', 'sofia@example.com', 'Carrera 6 #35-60', 'Bucaramanga', '2025-10-07 14:26:30'),
(7, 'Andrés López', '555666777', '3067890123', 'andres@example.com', 'Calle 7 #40-70', 'Pereira', '2025-10-07 14:26:30'),
(8, 'Laura Herrera', '888999000', '3078901234', 'laura@example.com', 'Carrera 8 #45-80', 'Manizales', '2025-10-07 14:26:30'),
(9, 'Pedro Sánchez', '333444555', '3089012345', 'pedro@example.com', 'Calle 9 #50-90', 'Ibagué', '2025-10-07 14:26:30'),
(10, 'Valentina Ríos', '666777888', '3090123456', 'valentina@example.com', 'Carrera 10 #55-100', 'Neiva', '2025-10-07 14:26:30')$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos_sistema`
--

CREATE TABLE IF NOT EXISTS `modulos_sistema` (
  `id_modulo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_modulo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `ruta` varchar(100) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `orden` int(11) DEFAULT 0,
  PRIMARY KEY (`id_modulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci$$

--
-- Volcado de datos para la tabla `modulos_sistema`
--

INSERT INTO `modulos_sistema` (`id_modulo`, `nombre_modulo`, `descripcion`, `icono`, `ruta`, `estado`, `orden`) VALUES
(1, 'Usuarios', 'Gestión de usuarios del sistema', 'user', '/usuarios', 'Activo', 1),
(2, 'Clientes', 'Gestión de clientes', 'users', '/clientes', 'Activo', 2),
(3, 'Proveedores', 'Gestión de proveedores', 'truck', '/proveedores', 'Activo', 3),
(4, 'Productos', 'Gestión de productos', 'box', '/productos', 'Activo', 4),
(5, 'Compras', 'Registro de compras', 'shopping-cart', '/compras', 'Activo', 5),
(6, 'Ventas', 'Registro de ventas', 'cash-register', '/ventas', 'Activo', 6),
(7, 'Inventario', 'Control de inventario', 'warehouse', '/inventario', 'Activo', 7),
(8, 'Pagos', 'Control de pagos', 'money-bill', '/pagos', 'Activo', 8),
(9, 'Reportes', 'Reportes y estadísticas', 'chart-line', '/reportes', 'Activo', 9),
(10, 'Configuración', 'Ajustes del sistema', 'cog', '/configuracion', 'Activo', 10)$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE IF NOT EXISTS `proveedores` (
  `id_proveedor` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_proveedor` varchar(100) NOT NULL,
  `nit` varchar(30) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_proveedor`),
  UNIQUE KEY `uk_proveedores_nit` (`nit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci$$

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id_proveedor`, `nombre_proveedor`, `nit`, `telefono`, `correo`, `direccion`, `ciudad`, `fecha_registro`) VALUES
(1, 'Provelectro S.A.', '900111222', '3101234567', 'contacto@provelectro.com', 'Calle 100 #10-20', 'Bogotá', '2025-10-07 14:26:30'),
(2, 'Muebles y Más', '900333444', '3112345678', 'ventas@mueblesymas.com', 'Carrera 50 #25-30', 'Medellín', '2025-10-07 14:26:30'),
(3, 'Alimentos del Sur', '900555666', '3123456789', 'info@alimentosdelsur.com', 'Calle 80 #15-20', 'Cali', '2025-10-07 14:26:30'),
(4, 'Distribuciones Caribe', '900777888', '3134567890', 'ventas@caribe.com', 'Carrera 60 #40-50', 'Barranquilla', '2025-10-07 14:26:30'),
(5, 'Ropa Fashion', '900999000', '3145678901', 'contacto@ropafashion.com', 'Calle 70 #30-10', 'Cartagena', '2025-10-07 14:26:30'),
(6, 'Bebidas y Licores', '901111222', '3156789012', 'ventas@bebidas.com', 'Carrera 20 #15-40', 'Bucaramanga', '2025-10-07 14:26:30'),
(7, 'Papelería Total', '901333444', '3167890123', 'info@papeleriatotal.com', 'Calle 30 #40-20', 'Pereira', '2025-10-07 14:26:30'),
(8, 'Juguetería Infantil', '901555666', '3178901234', 'ventas@juguetes.com', 'Carrera 10 #10-30', 'Manizales', '2025-10-07 14:26:30'),
(9, 'Deportes Activos', '901777888', '3189012345', 'info@deportesactivos.com', 'Calle 15 #20-50', 'Ibagué', '2025-10-07 14:26:30'),
(10, 'Belleza y Estilo', '901999000', '3190123456', 'ventas@belleza.com', 'Carrera 25 #30-60', 'Neiva', '2025-10-07 14:26:30')$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('Administrador','Vendedor','Contador','Bodeguero') DEFAULT 'Vendedor',
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `token_recuperacion` varchar(64) DEFAULT NULL,
  `token_expiracion` datetime DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `uk_usuarios_correo` (`correo`),
  UNIQUE KEY `uk_usuarios_usuario` (`usuario`),
  KEY `idx_token_recuperacion` (`token_recuperacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci$$

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_completo`, `correo`, `usuario`, `contrasena`, `rol`, `estado`, `fecha_creacion`, `token_recuperacion`, `token_expiracion`) VALUES
(1, 'Admin General', 'cparra02436@gmail.com', 'admin', 'admin123', 'Administrador', 'Activo', '2025-10-07 14:26:30', '2886502f9d5a6066bc76718bb6532143fa9e58d7fe5dd70eb7424b5afce70198', '2025-10-11 15:05:46'),
(2, 'Vendedor 1', 'vendedor1@example.com', 'vend1', 'vend123', 'Vendedor', 'Activo', '2025-10-07 14:26:30', NULL, NULL),
(3, 'Vendedor 2', 'vendedor2@example.com', 'vend2', 'vend123', 'Vendedor', 'Activo', '2025-10-07 14:26:30', NULL, NULL),
(4, 'Contador 1', 'conta1@example.com', 'cont1', 'cont123', 'Contador', 'Activo', '2025-10-07 14:26:30', NULL, NULL),
(5, 'Contador 2', 'conta2@example.com', 'cont2', 'cont123', 'Contador', 'Activo', '2025-10-07 14:26:30', NULL, NULL),
(6, 'Bodeguero 1', 'bodega1@example.com', 'bod1', 'bod123', 'Bodeguero', 'Activo', '2025-10-07 14:26:30', NULL, NULL),
(7, 'Bodeguero 2', 'bodega2@example.com', 'bod2', 'bod123', 'Bodeguero', 'Activo', '2025-10-07 14:26:30', NULL, NULL),
(8, 'Vendedor 3', 'vendedor3@example.com', 'vend3', 'vend123', 'Vendedor', 'Activo', '2025-10-07 14:26:30', NULL, NULL),
(10, 'Bodeguero 3', 'bodega3@example.com', 'bod3', 'bod123', 'Bodeguero', 'Activo', '2025-10-07 14:26:30', NULL, NULL)$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE IF NOT EXISTS `productos` (
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
  KEY `idx_productos_id_categoria` (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci$$

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `codigo_producto`, `nombre_producto`, `id_categoria`, `descripcion`, `stock_actual`, `stock_minimo`, `unidad_medida`, `precio_compra`, `precio_venta`, `estado`) VALUES
(1, 'ELEC001', 'Cargador USB', 1, 'Cargador rápido para celulares', 14.00, 10.00, 'Unidad', 20000.00, 35000.00, 'Activo'),
(2, 'ELEC002', 'Auriculares Bluetooth', 1, 'Auriculares inalámbricos', 30.00, 5.00, 'Unidad', 50000.00, 80000.00, 'Activo'),
(3, 'MUEB001', 'Silla Oficina', 2, 'Silla ergonómica de oficina', 11.00, 2.00, 'Unidad', 120000.00, 200000.00, 'Activo'),
(4, 'MUEB002', 'Mesa Comedor', 2, 'Mesa de madera para comedor', 15.00, 1.00, 'Unidad', 250000.00, 400000.00, 'Activo'),
(5, 'ROPA001', 'Camiseta Hombre', 3, 'Camiseta de algodón talla M', 91.00, 20.00, 'Unidad', 15000.00, 30000.00, 'Activo'),
(6, 'ALIM001', 'Arroz 1kg', 4, 'Arroz blanco', 150.00, 50.00, 'Paquete', 3000.00, 5500.00, 'Activo'),
(7, 'BEB001', 'Gaseosa 2L', 5, 'Bebida carbonatada', 73.00, 20.00, 'Unidad', 4000.00, 7000.00, 'Activo'),
(8, 'PAPE001', 'Cuaderno A4', 6, 'Cuaderno rayado 100 hojas', 50.00, 30.00, 'Unidad', 2000.00, 4000.00, 'Activo'),
(9, 'JUG001', 'Pelota Futbol', 7, 'Pelota profesional', 14.00, 5.00, 'Unidad', 30000.00, 55000.00, 'Activo'),
(10, 'DEPO001', 'Raqueta Tenis', 8, 'Raqueta de tenis profesional', 22.00, 2.00, 'Unidad', 80000.00, 150000.00, 'Activo'),
(11, 'BEL001', 'Gel Capilar', 9, 'Gel para el cabello.', 13.00, 10.00, 'Unidad', 23000.00, 30000.00, 'Activo')$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_contables`
--

CREATE TABLE IF NOT EXISTS `cuentas_contables` (
  `id_cuenta` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_cuenta` varchar(10) NOT NULL,
  `nombre_cuenta` varchar(100) NOT NULL,
  `tipo_cuenta` enum('ACTIVO','PASIVO','PATRIMONIO','INGRESO','GASTO') NOT NULL,
  PRIMARY KEY (`id_cuenta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE IF NOT EXISTS `compras` (
  `id_compra` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_compra` varchar(50) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_compra` datetime DEFAULT current_timestamp(),
  `total_compra` decimal(14,2) DEFAULT 0.00,
  `estado` enum('Pendiente','Pagada','Anulada') DEFAULT 'Pendiente',
  PRIMARY KEY (`id_compra`),
  UNIQUE KEY `uk_compras_codigo` (`codigo_compra`),
  KEY `idx_compras_id_proveedor` (`id_proveedor`),
  KEY `idx_compras_id_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci$$

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id_compra`, `codigo_compra`, `id_proveedor`, `id_usuario`, `fecha_compra`, `total_compra`, `estado`) VALUES
(1, 'COMP001', 1, 6, '2025-10-07 14:26:30', 500.00, 'Pagada'),
(2, 'COMP002', 2, 6, '2025-10-07 14:26:30', 800.00, 'Pagada'),
(3, 'COMP003', 3, 7, '2025-10-07 14:26:30', 200.00, 'Pagada'),
(4, 'COMP004', 4, 7, '2025-10-07 14:26:30', 1000.00, 'Anulada'),
(5, 'COMP005', 5, 6, '2025-10-07 14:26:30', 300.00, 'Pagada'),
(6, 'COMP006', 6, 6, '2025-10-07 14:26:30', 150.00, 'Pagada'),
(7, 'COMP007', 7, 7, '2025-10-07 14:26:30', 120.00, 'Pagada'),
(8, 'COMP008', 8, 6, '2025-10-07 14:26:30', 250.00, 'Pagada'),
(9, 'COMP009', 9, 7, '2025-10-07 14:26:30', 400.00, 'Pagada'),
(10, 'COMP010', 10, 6, '2025-10-07 14:26:30', 700.00, 'Pagada'),
(11, 'COMP011', 4, 1, '2025-10-13 21:29:08', 3750000.00, 'Pagada'),
(12, 'COMP012', 9, 1, '2025-10-14 12:28:35', 1600000.00, 'Pagada')$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compras`
--

CREATE TABLE IF NOT EXISTS `detalle_compras` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_compra` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(14,2) GENERATED ALWAYS AS (`cantidad` * `precio_unitario`) STORED,
  PRIMARY KEY (`id_detalle`),
  KEY `idx_detcomp_id_compra` (`id_compra`),
  KEY `idx_detcomp_id_producto` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci$$

--
-- Volcado de datos para la tabla `detalle_compras`
--

INSERT INTO `detalle_compras` (`id_detalle`, `id_compra`, `id_producto`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 1, 10.00, 20.00),
(2, 2, 2, 5.00, 50.00),
(3, 3, 3, 2.00, 120.00),
(4, 4, 4, 1.00, 250.00),
(5, 5, 5, 20.00, 15.00),
(6, 6, 6, 50.00, 3.00),
(7, 7, 7, 10.00, 4.00),
(8, 8, 8, 30.00, 2.00),
(9, 9, 9, 5.00, 30.00),
(10, 10, 10, 2.00, 80.00),
(11, 11, 4, 15.00, 250000.00),
(12, 12, 10, 20.00, 80000.00)$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE IF NOT EXISTS `ventas` (
  `id_venta` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_venta` varchar(50) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_venta` datetime DEFAULT current_timestamp(),
  `metodo_pago` enum('Efectivo','Transferencia','Tarjeta','Crédito') DEFAULT 'Efectivo',
  `total_venta` decimal(14,2) DEFAULT 0.00,
  `estado` enum('Pendiente','Pagada','Anulada') DEFAULT 'Pendiente',
  PRIMARY KEY (`id_venta`),
  UNIQUE KEY `uk_ventas_codigo` (`codigo_venta`),
  KEY `idx_ventas_cliente` (`id_cliente`),
  KEY `idx_ventas_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci$$

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `codigo_venta`, `id_cliente`, `id_usuario`, `fecha_venta`, `metodo_pago`, `total_venta`, `estado`) VALUES
(1, 'VENTA001', 1, 2, '2025-10-07 14:26:30', 'Efectivo', 350.00, 'Pagada'),
(2, 'VENTA002', 2, 3, '2025-10-07 14:26:30', 'Transferencia', 450.00, 'Pagada'),
(3, 'VENTA003', 3, 2, '2025-10-07 14:26:30', 'Tarjeta', 200.00, 'Pagada'),
(4, 'VENTA004', 4, 3, '2025-10-07 14:26:30', 'Crédito', 500.00, 'Anulada'),
(5, 'VENTA005', 5, 2, '2025-10-07 14:26:30', 'Efectivo', 300.00, 'Pagada'),
(6, 'VENTA006', 6, 3, '2025-10-07 14:26:30', 'Transferencia', 150.00, 'Pagada'),
(7, 'VENTA007', 7, 2, '2025-10-07 14:26:30', 'Tarjeta', 120.00, 'Pagada'),
(8, 'VENTA008', 8, 3, '2025-10-07 14:26:30', 'Efectivo', 250.00, 'Pagada'),
(9, 'VENTA009', 9, 2, '2025-10-07 14:26:30', 'Transferencia', 400.00, 'Pagada'),
(10, 'VENTA010', 10, 3, '2025-10-07 14:26:30', 'Crédito', 700.00, 'Pagada'),
(11, 'VENTA011', 3, 1, '2025-10-07 18:05:06', 'Tarjeta', 450000.00, 'Pagada'),
(12, 'VENTA012', 3, 1, '2025-10-07 18:17:11', 'Tarjeta', 476000.00, 'Pagada'),
(13, 'VENTA013', 8, 1, '2025-10-07 18:26:15', 'Crédito', 525000.00, 'Pagada'),
(14, 'VENTA014', 5, 1, '2025-10-08 10:26:14', 'Transferencia', 2027500.00, 'Pagada'),
(15, 'VENTA015', 7, 1, '2025-10-09 13:57:11', 'Efectivo', 1550000.00, 'Pagada'),
(16, 'VENTA016', 3, 1, '2025-10-09 21:08:44', 'Transferencia', 1140000.00, 'Pagada'),
(68, 'VENTA017', 4, 1, '2025-10-13 18:54:07', 'Tarjeta', 199500.00, 'Pagada'),
(69, 'VENTA018', 5, 1, '2025-10-13 20:13:00', 'Tarjeta', 220400.00, 'Pagada'),
(70, 'VENTA019', 6, 1, '2025-10-13 20:42:42', 'Efectivo', 680000.00, 'Pagada'),
(71, 'VENTA020', 8, 1, '2025-10-13 20:44:42', 'Tarjeta', 57000.00, 'Pagada'),
(72, 'VENTA021', 8, 1, '2025-10-13 20:58:18', 'Transferencia', 60000.00, 'Pagada'),
(73, 'VENTA022', 1, 1, '2025-10-13 21:10:36', 'Efectivo', 30000.00, 'Pagada'),
(74, 'VENTA023', 1, 1, '2025-10-13 21:11:33', 'Efectivo', 110000.00, 'Pagada'),
(75, 'VENTA024', 8, 1, '2025-10-13 21:16:39', 'Efectivo', 150000.00, 'Pagada'),
(76, 'VENTA025', 8, 1, '2025-10-14 12:22:01', 'Tarjeta', 266750.00, 'Pagada'),
(77, 'VENTA026', 7, 1, '2025-10-14 12:26:34', 'Transferencia', 33950.00, 'Pagada')$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE IF NOT EXISTS `detalle_ventas` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(14,2) GENERATED ALWAYS AS (`cantidad` * `precio_unitario`) STORED,
  PRIMARY KEY (`id_detalle`),
  KEY `idx_detven_id_venta` (`id_venta`),
  KEY `idx_detven_id_producto` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci$$

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id_detalle`, `id_venta`, `id_producto`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 1, 5.00, 35.00),
(2, 2, 2, 2.00, 80.00),
(3, 3, 3, 1.00, 200.00),
(4, 4, 4, 1.00, 400.00),
(5, 5, 5, 10.00, 30.00),
(6, 6, 6, 20.00, 5.50),
(7, 7, 7, 5.00, 7.00),
(8, 8, 8, 15.00, 4.00),
(9, 9, 9, 3.00, 55.00),
(10, 10, 10, 1.00, 150.00),
(11, 11, 5, 6.00, 30000.00),
(12, 11, 10, 1.00, 150000.00),
(13, 12, 8, 3.00, 4000.00),
(14, 12, 10, 3.00, 150000.00),
(15, 12, 7, 2.00, 7000.00),
(16, 13, 1, 15.00, 35000.00),
(17, 14, 6, 5.00, 5500.00),
(18, 14, 4, 5.00, 400000.00),
(19, 15, 10, 5.00, 150000.00),
(20, 15, 4, 2.00, 400000.00),
(21, 16, 4, 3.00, 400000.00),
(22, 68, 1, 6.00, 35000.00),
(23, 69, 9, 4.00, 55000.00),
(24, 69, 8, 3.00, 4000.00),
(25, 70, 3, 4.00, 200000.00),
(26, 71, 5, 2.00, 30000.00),
(27, 72, 11, 2.00, 30000.00),
(28, 73, 5, 1.00, 30000.00),
(29, 74, 9, 2.00, 55000.00),
(30, 75, 10, 1.00, 150000.00),
(31, 76, 9, 5.00, 55000.00),
(32, 77, 7, 5.00, 7000.00)$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos_operativos`
--

CREATE TABLE IF NOT EXISTS `gastos_operativos` (
  `id_gasto` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `valor` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id_gasto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_bodega`
--

CREATE TABLE IF NOT EXISTS `movimientos_bodega` (
  `id_movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `tipo_movimiento` enum('Entrada','Salida','Ajuste') NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_movimiento` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_movimiento`),
  KEY `idx_movimientos_producto` (`id_producto`),
  KEY `idx_movimientos_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci$$

--
-- Volcado de datos para la tabla `movimientos_bodega`
--

INSERT INTO `movimientos_bodega` (`id_movimiento`, `id_producto`, `tipo_movimiento`, `cantidad`, `descripcion`, `id_usuario`, `fecha_movimiento`) VALUES
(1, 1, 'Entrada', 10.00, 'Compra inicial', 6, '2025-10-07 14:26:30'),
(2, 2, 'Entrada', 5.00, 'Compra inicial', 6, '2025-10-07 14:26:30'),
(3, 3, 'Entrada', 2.00, 'Compra inicial', 7, '2025-10-07 14:26:30'),
(4, 4, 'Entrada', 1.00, 'Compra inicial', 7, '2025-10-07 14:26:30'),
(5, 5, 'Entrada', 20.00, 'Compra inicial', 6, '2025-10-07 14:26:30'),
(6, 6, 'Entrada', 50.00, 'Compra inicial', 6, '2025-10-07 14:26:30'),
(7, 7, 'Entrada', 10.00, 'Compra inicial', 7, '2025-10-07 14:26:30'),
(8, 8, 'Entrada', 30.00, 'Compra inicial', 6, '2025-10-07 14:26:30'),
(9, 9, 'Entrada', 5.00, 'Compra inicial', 7, '2025-10-07 14:26:30'),
(10, 10, 'Entrada', 2.00, 'Compra inicial', 6, '2025-10-07 14:26:30'),
(11, 5, 'Salida', 6.00, 'Venta #VENTA011', 1, '2025-10-07 18:05:06'),
(12, 10, 'Salida', 1.00, 'Venta #VENTA011', 1, '2025-10-07 18:05:06'),
(13, 8, 'Salida', 3.00, 'Venta #VENTA012', 1, '2025-10-07 18:17:11'),
(14, 10, 'Salida', 3.00, 'Venta #VENTA012', 1, '2025-10-07 18:17:11'),
(15, 7, 'Salida', 2.00, 'Venta #VENTA012', 1, '2025-10-07 18:17:11'),
(16, 1, 'Salida', 15.00, 'Venta #VENTA013', 1, '2025-10-07 18:26:15'),
(17, 6, 'Salida', 5.00, 'Venta #VENTA014', 1, '2025-10-08 10:26:14'),
(18, 4, 'Salida', 5.00, 'Venta #VENTA014', 1, '2025-10-08 10:26:14'),
(19, 8, 'Ajuste', 0.07, 'salida de inventario, por sobre costo', 2, '2025-10-08 15:28:05'),
(20, 10, 'Salida', 5.00, 'Venta #VENTA015', 1, '2025-10-09 13:57:11'),
(21, 4, 'Salida', 2.00, 'Venta #VENTA015', 1, '2025-10-09 13:57:11'),
(22, 4, 'Salida', 3.00, 'Venta #VENTA016', 1, '2025-10-09 21:08:44'),
(23, 1, 'Salida', 6.00, 'Venta #VENTA017', 1, '2025-10-13 18:54:07'),
(24, 9, 'Salida', 4.00, 'Venta #VENTA018', 1, '2025-10-13 20:13:00'),
(25, 8, 'Salida', 3.00, 'Venta #VENTA018', 1, '2025-10-13 20:13:00'),
(26, 3, 'Salida', 4.00, 'Venta #VENTA019', 1, '2025-10-13 20:42:42'),
(27, 5, 'Salida', 2.00, 'Venta #VENTA020', 1, '2025-10-13 20:44:42'),
(28, 11, 'Salida', 2.00, 'Venta #VENTA021', 1, '2025-10-13 20:58:18'),
(29, 5, 'Salida', 1.00, 'Venta #VENTA022', 1, '2025-10-13 21:10:36'),
(30, 9, 'Salida', 2.00, 'Venta #VENTA023', 1, '2025-10-13 21:11:33'),
(31, 10, 'Salida', 1.00, 'Venta #VENTA024', 1, '2025-10-13 21:16:39'),
(32, 4, 'Entrada', 15.00, 'Compra #C20251014042836', 1, '2025-10-13 21:29:08'),
(33, 9, 'Salida', 5.00, 'Venta #VENTA025', 1, '2025-10-14 12:22:01'),
(34, 7, 'Salida', 5.00, 'Venta #VENTA026', 1, '2025-10-14 12:26:34'),
(35, 10, 'Entrada', 20.00, 'Compra #C20251014192818', 1, '2025-10-14 12:28:35')$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_contables`
--

CREATE TABLE IF NOT EXISTS `movimientos_contables` (
  `id_movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuenta` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo_movimiento` enum('DEBITO','CREDITO') NOT NULL,
  `valor` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id_movimiento`),
  KEY `id_cuenta` (`id_cuenta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE IF NOT EXISTS `pagos` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_pago` enum('Ingreso','Egreso') NOT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `monto` decimal(14,2) NOT NULL,
  `fecha_pago` datetime DEFAULT current_timestamp(),
  `metodo_pago` enum('Efectivo','Transferencia','Tarjeta') DEFAULT 'Efectivo',
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `idx_pagos_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci$$

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `tipo_pago`, `referencia`, `descripcion`, `monto`, `fecha_pago`, `metodo_pago`, `id_usuario`) VALUES
(1, 'Ingreso', 'PAG001', 'Pago de cliente Juan Pérez', 350.00, '2025-10-07 14:26:30', 'Efectivo', 2),
(2, 'Ingreso', 'PAG002', 'Pago de cliente María Gómez', 450.00, '2025-10-07 14:26:30', 'Transferencia', 3),
(3, 'Ingreso', 'PAG003', 'Pago de cliente Carlos Ramírez', 200.00, '2025-10-07 14:26:30', 'Tarjeta', 2),
(4, 'Ingreso', 'PAG004', 'Pago de cliente Ana Torres', 500.00, '2025-10-07 14:26:30', 'Tarjeta', 3),
(5, 'Ingreso', 'PAG005', 'Pago de cliente Luis Martínez', 300.00, '2025-10-07 14:26:30', 'Efectivo', 2),
(6, 'Ingreso', 'PAG006', 'Pago de cliente Sofía Díaz', 150.00, '2025-10-07 14:26:30', 'Transferencia', 3),
(7, 'Ingreso', 'PAG007', 'Pago de cliente Andrés López', 120.00, '2025-10-07 14:26:30', 'Tarjeta', 2),
(8, 'Ingreso', 'PAG008', 'Pago de cliente Laura Herrera', 250.00, '2025-10-07 14:26:30', 'Efectivo', 3),
(9, 'Ingreso', 'PAG009', 'Pago de cliente Pedro Sánchez', 400.00, '2025-10-07 14:26:30', 'Transferencia', 2),
(10, 'Ingreso', 'PAG010', 'Pago de cliente Valentina Ríos', 700.00, '2025-10-07 14:26:30', 'Efectivo', 3),
(11, 'Ingreso', '1254400455', 'Pago accesorios.', 450000.00, '2025-10-09 14:59:00', 'Efectivo', 4),
(12, 'Egreso', '458766899', 'Pago de mercancía.', 1000000.00, '2025-10-09 15:42:33', 'Efectivo', 4)$$

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_roles`
--

CREATE TABLE IF NOT EXISTS `permisos_roles` (
  `id_permiso` int(11) NOT NULL AUTO_INCREMENT,
  `id_rol` varchar(50) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `puede_ver` tinyint(1) DEFAULT 1,
  `puede_crear` tinyint(1) DEFAULT 0,
  `puede_editar` tinyint(1) DEFAULT 0,
  `puede_eliminar` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id_permiso`),
  KEY `idx_permisos_modulo` (`id_modulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci$$

--
-- Volcado de datos para la tabla `permisos_roles`
--

INSERT INTO `permisos_roles` (`id_permiso`, `id_rol`, `id_modulo`, `puede_ver`, `puede_crear`, `puede_editar`, `puede_eliminar`) VALUES
(1, 'Administrador', 1, 1, 1, 1, 1),
(2, 'Administrador', 2, 1, 1, 1, 1),
(3, 'Administrador', 3, 1, 1, 1, 1),
(4, 'Administrador', 4, 1, 1, 1, 1),
(5, 'Administrador', 5, 1, 1, 1, 1),
(6, 'Administrador', 6, 1, 1, 1, 1),
(7, 'Administrador', 7, 1, 1, 1, 1),
(8, 'Administrador', 8, 1, 1, 1, 1),
(9, 'Administrador', 9, 1, 1, 1, 1),
(10, 'Administrador', 10, 1, 1, 1, 1)$$

-- --------------------------------------------------------

--
-- Disparadores `compras`
--
DELIMITER $$
CREATE TRIGGER IF NOT EXISTS `after_insert_compra` AFTER INSERT ON `compras` FOR EACH ROW BEGIN
  -- Activo: Inventario aumenta
  INSERT INTO movimientos_contables (id_cuenta, fecha, descripcion, tipo_movimiento, valor)
  VALUES (
    (SELECT id_cuenta FROM cuentas_contables WHERE nombre_cuenta = 'Inventarios' LIMIT 1),
    NEW.fecha_compra,
    CONCAT('Compra #', NEW.id_compra),
    'DEBITO',
    NEW.total_compra
  );

  -- Pasivo: Proveedores o bancos
  INSERT INTO movimientos_contables (id_cuenta, fecha, descripcion, tipo_movimiento, valor)
  VALUES (
    (SELECT id_cuenta FROM cuentas_contables WHERE nombre_cuenta = 'Proveedores' LIMIT 1),
    NEW.fecha_compra,
    CONCAT('Cuenta por pagar compra #', NEW.id_compra),
    'CREDITO',
    NEW.total_compra
  );
END
$$

--
-- Disparadores `gastos_operativos`
--
CREATE TRIGGER IF NOT EXISTS `after_insert_gasto` AFTER INSERT ON `gastos_operativos` FOR EACH ROW BEGIN
  -- Gasto: salida
  INSERT INTO movimientos_contables (id_cuenta, fecha, descripcion, tipo_movimiento, valor)
  VALUES (
    (SELECT id_cuenta FROM cuentas_contables WHERE nombre_cuenta = NEW.categoria LIMIT 1),
    NEW.fecha,
    NEW.descripcion,
    'DEBITO',
    NEW.valor
  );

  -- Caja o bancos disminuyen
  INSERT INTO movimientos_contables (id_cuenta, fecha, descripcion, tipo_movimiento, valor)
  VALUES (
    (SELECT id_cuenta FROM cuentas_contables WHERE nombre_cuenta = 'Caja' LIMIT 1),
    NEW.fecha,
    CONCAT('Pago por gasto: ', NEW.descripcion),
    'CREDITO',
    NEW.valor
  );
END
$$

--
-- Disparadores `ventas`
--
CREATE TRIGGER IF NOT EXISTS `after_insert_venta` AFTER INSERT ON `ventas` FOR EACH ROW BEGIN
  -- Ingreso: venta
  INSERT INTO movimientos_contables (id_cuenta, fecha, descripcion, tipo_movimiento, valor)
  VALUES (
    (SELECT id_cuenta FROM cuentas_contables WHERE nombre_cuenta = 'Ventas' LIMIT 1),
    NEW.fecha_venta,
    CONCAT('Venta #', NEW.id_venta),
    'CREDITO',
    NEW.total_venta
  );

  -- Activo: caja o bancos
  INSERT INTO movimientos_contables (id_cuenta, fecha, descripcion, tipo_movimiento, valor)
  VALUES (
    (SELECT id_cuenta FROM cuentas_contables WHERE nombre_cuenta = 'Caja' LIMIT 1),
    NEW.fecha_venta,
    CONCAT('Ingreso por venta #', NEW.id_venta),
    'DEBITO',
    NEW.total_venta
  );
END
$$

DELIMITER ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `fk_compras_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`),
  ADD CONSTRAINT `fk_compras_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD CONSTRAINT `fk_detcompras_compra` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`),
  ADD CONSTRAINT `fk_detcompras_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `fk_detventas_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`),
  ADD CONSTRAINT `fk_detventas_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`);

--
-- Filtros para la tabla `movimientos_bodega`
--
ALTER TABLE `movimientos_bodega`
  ADD CONSTRAINT `fk_movimientos_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`),
  ADD CONSTRAINT `fk_movimientos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `movimientos_contables`
--
ALTER TABLE `movimientos_contables`
  ADD CONSTRAINT `movimientos_contables_ibfk_1` FOREIGN KEY (`id_cuenta`) REFERENCES `cuentas_contables` (`id_cuenta`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `fk_pagos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `permisos_roles`
--
ALTER TABLE `permisos_roles`
  ADD CONSTRAINT `fk_permisos_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulos_sistema` (`id_modulo`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `fk_ventas_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `fk_ventas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;