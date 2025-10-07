-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-10-2025 a las 03:49:27
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
-- Base de datos: `stock_nexus_inventario_2025`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `balance_general`
--

CREATE TABLE `balance_general` (
  `id_balance` int(11) NOT NULL,
  `fecha_balance` date DEFAULT curdate(),
  `total_ingresos` decimal(14,2) DEFAULT 0.00,
  `total_egresos` decimal(14,2) DEFAULT 0.00,
  `utilidad` decimal(14,2) GENERATED ALWAYS AS (`total_ingresos` - `total_egresos`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `balance_general`
--

INSERT INTO `balance_general` (`id_balance`, `fecha_balance`, `total_ingresos`, `total_egresos`) VALUES
(1, '2025-01-01', 500000.00, 300000.00),
(2, '2025-02-01', 720000.00, 450000.00),
(3, '2025-03-01', 600000.00, 320000.00),
(4, '2025-04-01', 800000.00, 500000.00),
(5, '2025-05-01', 700000.00, 400000.00),
(6, '2025-06-01', 900000.00, 550000.00),
(7, '2025-07-01', 850000.00, 480000.00),
(8, '2025-08-01', 880000.00, 600000.00),
(9, '2025-09-01', 920000.00, 620000.00),
(10, '2025-10-01', 950000.00, 650000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`, `descripcion`, `estado`) VALUES
(1, 'Bebidas', 'Refrescos, jugos, cervezas y aguas embotelladas', 'Activo'),
(2, 'Alimentos', 'Comestibles y abarrotes en general', 'Activo'),
(3, 'Limpieza', 'Artículos de aseo y desinfección', 'Activo'),
(4, 'Electrodomésticos', 'Pequeños aparatos eléctricos', 'Activo'),
(5, 'Ferretería', 'Herramientas y materiales', 'Activo'),
(6, 'Papelería', 'Útiles de oficina y escolares', 'Activo'),
(7, 'Tecnología', 'Equipos y accesorios electrónicos', 'Activo'),
(8, 'Ropa', 'Prendas de vestir y calzado', 'Activo'),
(9, 'Repuestos', 'Componentes mecánicos y eléctricos', 'Activo'),
(10, 'Otros', 'Artículos varios y misceláneos', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `identificacion` varchar(30) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre_cliente`, `identificacion`, `telefono`, `correo`, `direccion`, `ciudad`, `fecha_registro`) VALUES
(1, 'Comercial S.A.', '900101001-1', '3201112233', 'ventas@comercial.com', 'Cra 10 #25-30', 'Bogotá', '2025-10-06 15:42:05'),
(2, 'Tienda El Sol', '800220032-2', '3123334455', 'contacto@elsol.com', 'Calle 12 #8-10', 'Medellín', '2025-10-06 15:42:05'),
(3, 'Ferretería La 33', '901330045-3', '3105556677', 'info@la33.com', 'Av 33 #45-67', 'Cali', '2025-10-06 15:42:05'),
(4, 'Restaurante El Buen Sabor', '902440056-4', '3112223344', 'gerencia@buen-sabor.com', 'Cl 40 #21-80', 'Barranquilla', '2025-10-06 15:42:05'),
(5, 'Panadería Don Pan', '901550067-5', '3006667788', 'ventas@donpan.com', 'Cl 3 #12-40', 'Cartagena', '2025-10-06 15:42:05'),
(6, 'Insumos Agropecuarios SAS', '900660078-6', '3208889999', 'info@agroinsumos.com', 'Cra 80 #14-22', 'Bucaramanga', '2025-10-06 15:42:05'),
(7, 'Café La Palma', '900770089-7', '3100001111', 'admin@lapalma.com', 'Cl 50 #23-14', 'Pereira', '2025-10-06 15:42:05'),
(8, 'Supermercado Central', '900880090-8', '3113332222', 'contacto@central.com', 'Av 5 #67-10', 'Santa Marta', '2025-10-06 15:42:05'),
(9, 'Distribuidora Del Norte', '900990101-9', '3007778888', 'ventas@norte.com', 'Cra 12 #45-15', 'Cúcuta', '2025-10-06 15:42:05'),
(10, 'Hotel Bahía', '901110112-0', '3202221111', 'reservas@bahia.com', 'Cl 25 #20-50', 'Valledupar', '2025-10-06 15:42:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id_compra` int(11) NOT NULL,
  `codigo_compra` varchar(50) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_compra` datetime DEFAULT current_timestamp(),
  `total_compra` decimal(14,2) DEFAULT 0.00,
  `estado` enum('Pendiente','Pagada','Anulada') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id_compra`, `codigo_compra`, `id_proveedor`, `id_usuario`, `fecha_compra`, `total_compra`, `estado`) VALUES
(1, 'C001', 1, 2, '2025-10-06 15:42:07', 350000.00, 'Pagada'),
(2, 'C002', 2, 2, '2025-10-06 15:42:07', 420000.00, 'Pagada'),
(3, 'C003', 3, 2, '2025-10-06 15:42:07', 290000.00, 'Pendiente'),
(4, 'C004', 4, 2, '2025-10-06 15:42:07', 315000.00, 'Pagada'),
(5, 'C005', 5, 2, '2025-10-06 15:42:07', 175000.00, 'Pendiente'),
(6, 'C006', 6, 2, '2025-10-06 15:42:07', 212000.00, 'Pagada'),
(7, 'C007', 7, 2, '2025-10-06 15:42:07', 280000.00, 'Pagada'),
(8, 'C008', 8, 2, '2025-10-06 15:42:07', 360000.00, 'Pagada'),
(9, 'C009', 9, 2, '2025-10-06 15:42:07', 190000.00, 'Pendiente'),
(10, 'C010', 10, 2, '2025-10-06 15:42:07', 450000.00, 'Pagada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compras`
--

CREATE TABLE `detalle_compras` (
  `id_detalle` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(14,2) GENERATED ALWAYS AS (`cantidad` * `precio_unitario`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_compras`
--

INSERT INTO `detalle_compras` (`id_detalle`, `id_compra`, `id_producto`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 1, 100.00, 800.00),
(2, 2, 2, 50.00, 2000.00),
(3, 3, 3, 40.00, 12000.00),
(4, 4, 4, 60.00, 7000.00),
(5, 5, 5, 80.00, 2500.00),
(6, 6, 6, 30.00, 4000.00),
(7, 7, 7, 100.00, 2500.00),
(8, 8, 8, 20.00, 10000.00),
(9, 9, 9, 60.00, 2500.00),
(10, 10, 10, 25.00, 3500.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id_detalle` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(14,2) GENERATED ALWAYS AS (`cantidad` * `precio_unitario`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id_detalle`, `id_venta`, `id_producto`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 1, 10.00, 1500.00),
(2, 2, 2, 20.00, 3500.00),
(3, 3, 3, 3.00, 18000.00),
(4, 4, 4, 8.00, 10500.00),
(5, 5, 5, 5.00, 4200.00),
(6, 6, 6, 4.00, 7500.00),
(7, 7, 7, 15.00, 6000.00),
(8, 8, 8, 5.00, 18000.00),
(9, 9, 9, 8.00, 4500.00),
(10, 10, 10, 10.00, 8000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos_sistema`
--

CREATE TABLE `modulos_sistema` (
  `id_modulo` int(11) NOT NULL,
  `nombre_modulo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `ruta` varchar(100) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `orden` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos_sistema`
--

INSERT INTO `modulos_sistema` (`id_modulo`, `nombre_modulo`, `descripcion`, `icono`, `ruta`, `estado`, `orden`) VALUES
(1, 'Dashboard', 'Panel principal del sistema', 'fas fa-tachometer-alt', 'dashboard', 'Activo', 1),
(2, 'Productos', 'Gestión de productos e inventario', 'fas fa-boxes', 'productos', 'Activo', 2),
(3, 'Categorías', 'Gestión de categorías de productos', 'fas fa-tags', 'categorias', 'Activo', 3),
(4, 'Proveedores', 'Gestión de proveedores', 'fas fa-truck', 'proveedores', 'Activo', 4),
(5, 'Compras', 'Registro y gestión de compras', 'fas fa-shopping-cart', 'compras', 'Activo', 5),
(6, 'Ventas', 'Registro y gestión de ventas', 'fas fa-cash-register', 'ventas', 'Activo', 6),
(7, 'Clientes', 'Gestión de clientes', 'fas fa-users', 'clientes', 'Activo', 7),
(8, 'Movimientos', 'Movimientos de bodega', 'fas fa-exchange-alt', 'movimientos', 'Activo', 8),
(9, 'Finanzas', 'Gestión financiera y pagos', 'fas fa-chart-line', 'finanzas', 'Activo', 9),
(10, 'Reportes', 'Generación de reportes', 'fas fa-chart-bar', 'reportes', 'Activo', 10),
(11, 'Usuarios', 'Gestión de usuarios del sistema', 'fas fa-user-cog', 'usuarios', 'Activo', 11),
(12, 'Configuración', 'Configuración del sistema', 'fas fa-cogs', 'configuracion', 'Activo', 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_bodega`
--

CREATE TABLE `movimientos_bodega` (
  `id_movimiento` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `tipo_movimiento` enum('Entrada','Salida','Ajuste') NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_movimiento` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos_bodega`
--

INSERT INTO `movimientos_bodega` (`id_movimiento`, `id_producto`, `tipo_movimiento`, `cantidad`, `descripcion`, `id_usuario`, `fecha_movimiento`) VALUES
(1, 1, 'Entrada', 150.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(2, 2, 'Entrada', 100.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(3, 3, 'Entrada', 80.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(4, 4, 'Entrada', 90.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(5, 5, 'Entrada', 120.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(6, 6, 'Entrada', 60.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(7, 7, 'Entrada', 200.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(8, 8, 'Entrada', 40.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(9, 9, 'Entrada', 90.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07'),
(10, 10, 'Entrada', 50.00, 'Ingreso inicial de inventario', 4, '2025-10-06 15:42:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `tipo_pago` enum('Ingreso','Egreso') NOT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `monto` decimal(14,2) NOT NULL,
  `fecha_pago` datetime DEFAULT current_timestamp(),
  `metodo_pago` enum('Efectivo','Transferencia','Tarjeta') DEFAULT 'Efectivo',
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `tipo_pago`, `referencia`, `descripcion`, `monto`, `fecha_pago`, `metodo_pago`, `id_usuario`) VALUES
(1, 'Ingreso', 'V001', 'Venta al cliente Comercial S.A.', 45000.00, '2025-10-06 15:42:08', 'Efectivo', 3),
(2, 'Ingreso', 'V002', 'Venta al cliente Tienda El Sol', 72000.00, '2025-10-06 15:42:08', 'Tarjeta', 3),
(3, 'Ingreso', 'V004', 'Venta al cliente Buen Sabor', 120000.00, '2025-10-06 15:42:08', 'Transferencia', 3),
(4, 'Egreso', 'C001', 'Compra a Proveedora Andina', 350000.00, '2025-10-06 15:42:08', 'Transferencia', 3),
(5, 'Egreso', 'C002', 'Compra a Distribuciones El Valle', 420000.00, '2025-10-06 15:42:08', 'Efectivo', 3),
(6, 'Ingreso', 'V005', 'Venta al cliente Don Pan', 30000.00, '2025-10-06 15:42:08', 'Efectivo', 3),
(7, 'Egreso', 'C006', 'Compra a Papelería Central', 212000.00, '2025-10-06 15:42:08', 'Tarjeta', 3),
(8, 'Ingreso', 'V007', 'Venta al cliente La Palma', 75000.00, '2025-10-06 15:42:08', 'Efectivo', 3),
(9, 'Egreso', 'C008', 'Compra a TecnoDistribuciones', 360000.00, '2025-10-06 15:42:08', 'Transferencia', 3),
(10, 'Ingreso', 'V009', 'Venta a Distribuidora del Norte', 49000.00, '2025-10-06 15:42:08', 'Efectivo', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_roles`
--

CREATE TABLE `permisos_roles` (
  `id_permiso` int(11) NOT NULL,
  `id_rol` varchar(50) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `puede_ver` tinyint(1) DEFAULT 1,
  `puede_crear` tinyint(1) DEFAULT 0,
  `puede_editar` tinyint(1) DEFAULT 0,
  `puede_eliminar` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(10, 'Administrador', 10, 1, 1, 1, 1),
(11, 'Administrador', 11, 1, 1, 1, 1),
(12, 'Administrador', 12, 1, 1, 1, 1),
(16, 'Vendedor', 1, 1, 0, 0, 0),
(17, 'Vendedor', 2, 1, 0, 0, 0),
(18, 'Vendedor', 6, 1, 1, 0, 0),
(19, 'Vendedor', 7, 1, 1, 1, 0),
(20, 'Vendedor', 10, 1, 0, 0, 0),
(21, 'Contador', 1, 1, 0, 0, 0),
(22, 'Contador', 5, 1, 0, 0, 0),
(23, 'Contador', 6, 1, 0, 0, 0),
(24, 'Contador', 9, 1, 1, 1, 0),
(25, 'Contador', 10, 1, 1, 0, 0),
(26, 'Bodeguero', 1, 1, 0, 0, 0),
(27, 'Bodeguero', 2, 1, 1, 1, 0),
(28, 'Bodeguero', 3, 1, 0, 0, 0),
(29, 'Bodeguero', 5, 1, 1, 0, 0),
(30, 'Bodeguero', 8, 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `codigo_producto` varchar(50) NOT NULL,
  `nombre_producto` varchar(150) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `stock_actual` decimal(10,2) DEFAULT 0.00,
  `stock_minimo` decimal(10,2) DEFAULT 0.00,
  `unidad_medida` varchar(30) DEFAULT 'Unidad',
  `precio_compra` decimal(12,2) DEFAULT 0.00,
  `precio_venta` decimal(12,2) DEFAULT 0.00,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `codigo_producto`, `nombre_producto`, `id_categoria`, `descripcion`, `stock_actual`, `stock_minimo`, `unidad_medida`, `precio_compra`, `precio_venta`, `estado`) VALUES
(1, 'P001', 'Agua 600ml', 1, 'Botella de agua pura', 150.00, 30.00, 'Unidad', 800.00, 1500.00, 'Activo'),
(2, 'P002', 'Gaseosa 1.5L', 1, 'Bebida gaseosa sabor cola', 100.00, 20.00, 'Unidad', 2000.00, 3500.00, 'Activo'),
(3, 'P003', 'Arroz 5Kg', 2, 'Arroz blanco premium', 80.00, 15.00, 'Bulto', 12000.00, 18000.00, 'Activo'),
(4, 'P004', 'Aceite 1L', 2, 'Aceite vegetal refinado', 90.00, 20.00, 'Unidad', 7000.00, 10500.00, 'Activo'),
(5, 'P005', 'Cloro 1L', 3, 'Desinfectante líquido', 120.00, 25.00, 'Unidad', 2500.00, 4200.00, 'Activo'),
(6, 'P006', 'Escoba plástica', 3, 'Escoba de cerdas gruesas', 60.00, 10.00, 'Unidad', 4000.00, 7500.00, 'Activo'),
(7, 'P007', 'Bombillo LED 9W', 4, 'Luz blanca bajo consumo', 200.00, 30.00, 'Unidad', 2500.00, 6000.00, 'Activo'),
(8, 'P008', 'Martillo 16oz', 5, 'Mango de fibra, cabeza de acero', 40.00, 10.00, 'Unidad', 10000.00, 18000.00, 'Activo'),
(9, 'P009', 'Cuaderno universitario', 6, '100 hojas cuadriculado', 90.00, 15.00, 'Unidad', 2500.00, 4500.00, 'Activo'),
(10, 'P010', 'Cable USB tipo C', 7, '1 metro de longitud', 50.00, 10.00, 'Unidad', 3500.00, 8000.00, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id_proveedor` int(11) NOT NULL,
  `nombre_proveedor` varchar(100) NOT NULL,
  `nit` varchar(30) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id_proveedor`, `nombre_proveedor`, `nit`, `telefono`, `correo`, `direccion`, `ciudad`, `fecha_registro`) VALUES
(1, 'Proveedora Andina', '900100001-1', '3201234567', 'contacto@andina.com', 'Cl 30 #15-22', 'Bogotá', '2025-10-06 15:42:06'),
(2, 'Distribuciones El Valle', '901200002-2', '3107654321', 'ventas@elvalle.com', 'Av 45 #12-30', 'Cali', '2025-10-06 15:42:06'),
(3, 'Suministros del Norte', '902300003-3', '3119876543', 'info@norte.com', 'Cra 60 #14-10', 'Barranquilla', '2025-10-06 15:42:06'),
(4, 'Importadora del Sur', '903400004-4', '3123456789', 'contacto@importsur.com', 'Cl 22 #18-20', 'Medellín', '2025-10-06 15:42:06'),
(5, 'Ferrosoluciones SAS', '904500005-5', '3002223344', 'ventas@ferrosol.com', 'Av 68 #45-90', 'Bogotá', '2025-10-06 15:42:06'),
(6, 'Papelería Central', '905600006-6', '3015556677', 'info@papecentral.com', 'Cra 9 #30-80', 'Pereira', '2025-10-06 15:42:06'),
(7, 'AgroProveedores', '906700007-7', '3204445566', 'contacto@agropro.com', 'Cl 11 #45-33', 'Montería', '2025-10-06 15:42:06'),
(8, 'TecnoDistribuciones', '907800008-8', '3107778899', 'ventas@tecno.com', 'Cl 35 #8-90', 'Cúcuta', '2025-10-06 15:42:06'),
(9, 'Textiles del Caribe', '908900009-9', '3116667788', 'contacto@caribe.com', 'Av 30 #10-22', 'Cartagena', '2025-10-06 15:42:06'),
(10, 'RefriParts Ltda', '909000010-0', '3129990000', 'info@refriparts.com', 'Cra 80 #11-44', 'Bucaramanga', '2025-10-06 15:42:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('Administrador','Vendedor','Contador','Bodeguero') DEFAULT 'Vendedor',
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_completo`, `correo`, `usuario`, `contrasena`, `rol`, `estado`, `fecha_creacion`) VALUES
(1, 'Carlos Parra', 'carlos@stocknexus.com', 'carlos_admin', '123456', 'Administrador', 'Activo', '2025-10-06 15:42:05'),
(2, 'Juan Pérez', 'juan@stocknexus.com', 'juan_vendedor', '123456', 'Vendedor', 'Activo', '2025-10-06 15:42:05'),
(3, 'Ana Gómez', 'ana@stocknexus.com', 'ana_contadora', '123456', 'Contador', 'Activo', '2025-10-06 15:42:05'),
(4, 'Luis Herrera', 'luis@stocknexus.com', 'luis_bodega', '123456', 'Bodeguero', 'Activo', '2025-10-06 15:42:05'),
(5, 'María Rojas', 'maria@stocknexus.com', 'maria_vendedora', '123456', 'Vendedor', 'Activo', '2025-10-06 15:42:05'),
(6, 'David López', 'david@stocknexus.com', 'david_admin', '123456', 'Administrador', 'Activo', '2025-10-06 15:42:05'),
(7, 'Sofía Castro', 'sofia@stocknexus.com', 'sofia_cont', '123456', 'Contador', 'Activo', '2025-10-06 15:42:05'),
(8, 'Pedro Ramírez', 'pedro@stocknexus.com', 'pedro_bodega', '123456', 'Bodeguero', 'Activo', '2025-10-06 15:42:05'),
(9, 'Laura Torres', 'laura@stocknexus.com', 'laura_vend', '123456', 'Vendedor', 'Activo', '2025-10-06 15:42:05'),
(10, 'Andrés Díaz', 'andres@stocknexus.com', 'andres_admin', '123456', 'Administrador', 'Activo', '2025-10-06 15:42:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `codigo_venta` varchar(50) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_venta` datetime DEFAULT current_timestamp(),
  `metodo_pago` enum('Efectivo','Transferencia','Tarjeta','Crédito') DEFAULT 'Efectivo',
  `total_venta` decimal(14,2) DEFAULT 0.00,
  `estado` enum('Pendiente','Pagada','Anulada') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `codigo_venta`, `id_cliente`, `id_usuario`, `fecha_venta`, `metodo_pago`, `total_venta`, `estado`) VALUES
(1, 'V001', 1, 2, '2025-10-06 15:42:08', 'Efectivo', 45000.00, 'Pagada'),
(2, 'V002', 2, 2, '2025-10-06 15:42:08', 'Tarjeta', 72000.00, 'Pagada'),
(3, 'V003', 3, 2, '2025-10-06 15:42:08', 'Efectivo', 55000.00, 'Pendiente'),
(4, 'V004', 4, 2, '2025-10-06 15:42:08', 'Transferencia', 120000.00, 'Pagada'),
(5, 'V005', 5, 2, '2025-10-06 15:42:08', 'Efectivo', 30000.00, 'Pagada'),
(6, 'V006', 6, 2, '2025-10-06 15:42:08', 'Crédito', 90000.00, 'Pendiente'),
(7, 'V007', 7, 2, '2025-10-06 15:42:08', 'Efectivo', 75000.00, 'Pagada'),
(8, 'V008', 8, 2, '2025-10-06 15:42:08', 'Transferencia', 68000.00, 'Pagada'),
(9, 'V009', 9, 2, '2025-10-06 15:42:08', 'Efectivo', 49000.00, 'Pagada'),
(10, 'V010', 10, 2, '2025-10-06 15:42:08', 'Crédito', 115000.00, 'Pendiente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `balance_general`
--
ALTER TABLE `balance_general`
  ADD PRIMARY KEY (`id_balance`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `identificacion` (`identificacion`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id_compra`),
  ADD UNIQUE KEY `codigo_compra` (`codigo_compra`),
  ADD KEY `id_proveedor` (`id_proveedor`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_compra` (`id_compra`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `modulos_sistema`
--
ALTER TABLE `modulos_sistema`
  ADD PRIMARY KEY (`id_modulo`);

--
-- Indices de la tabla `movimientos_bodega`
--
ALTER TABLE `movimientos_bodega`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `permisos_roles`
--
ALTER TABLE `permisos_roles`
  ADD PRIMARY KEY (`id_permiso`),
  ADD KEY `id_modulo` (`id_modulo`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `codigo_producto` (`codigo_producto`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id_proveedor`),
  ADD UNIQUE KEY `nit` (`nit`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD UNIQUE KEY `codigo_venta` (`codigo_venta`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `balance_general`
--
ALTER TABLE `balance_general`
  MODIFY `id_balance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `modulos_sistema`
--
ALTER TABLE `modulos_sistema`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `movimientos_bodega`
--
ALTER TABLE `movimientos_bodega`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `permisos_roles`
--
ALTER TABLE `permisos_roles`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`),
  ADD CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD CONSTRAINT `detalle_compras_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`),
  ADD CONSTRAINT `detalle_compras_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`),
  ADD CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `movimientos_bodega`
--
ALTER TABLE `movimientos_bodega`
  ADD CONSTRAINT `movimientos_bodega_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`),
  ADD CONSTRAINT `movimientos_bodega_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `permisos_roles`
--
ALTER TABLE `permisos_roles`
  ADD CONSTRAINT `permisos_roles_ibfk_1` FOREIGN KEY (`id_modulo`) REFERENCES `modulos_sistema` (`id_modulo`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
