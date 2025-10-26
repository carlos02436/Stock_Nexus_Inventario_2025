-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS stock_nexus_inventario_2025
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE stock_nexus_inventario_2025;

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `balance_general`
--

CREATE TABLE `balance_general` (
  `id_balance` int(11) NOT NULL,
  `fecha_balance` date NOT NULL,
  `total_ingresos` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_egresos` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(10, '2025-02-10', 12500.00, 5500.00);

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
(12, 'jugos', 'jugos naturales', 'Activo');

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
(1, 'Juan Pérez', '123456789', '3001234567', 'juan@example.com', 'Calle 1 #10-20', 'Bogotá', '2025-10-07 14:26:30'),
(2, 'María Gómez', '987654321', '3012345678', 'maria@example.com', 'Carrera 2 #15-30', 'Medellín', '2025-10-07 14:26:30'),
(3, 'Carlos Ramírez', '111222333', '3023456789', 'carlos@example.com', 'Calle 3 #20-10', 'Cali', '2025-10-07 14:26:30'),
(4, 'Ana Torres', '444555666', '3034567890', 'ana@example.com', 'Carrera 4 #25-50', 'Barranquilla', '2025-10-07 14:26:30'),
(5, 'Luis Martínez', '777888999', '3045678901', 'luis@example.com', 'Calle 5 #30-40', 'Cartagena', '2025-10-07 14:26:30'),
(6, 'Sofía Díaz', '222333444', '3056789012', 'sofia@example.com', 'Carrera 6 #35-60', 'Bucaramanga', '2025-10-07 14:26:30'),
(7, 'Andrés López', '555666777', '3067890123', 'andres@example.com', 'Calle 7 #40-70', 'Pereira', '2025-10-07 14:26:30'),
(8, 'Laura Herrera', '888999000', '3078901234', 'laura@example.com', 'Carrera 8 #45-80', 'Manizales', '2025-10-07 14:26:30'),
(9, 'Pedro Sánchez', '333444555', '3089012345', 'pedro@example.com', 'Calle 9 #50-90', 'Ibagué', '2025-10-07 14:26:30'),
(10, 'Valentina Ríos', '666777888', '3090123456', 'valentina@example.com', 'Carrera 10 #55-100', 'Neiva', '2025-10-07 14:26:30');

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
  `estado` enum('Pendiente','Pagada','Anulada') DEFAULT 'Pendiente',
  `descuento_aplicado` decimal(10,2) DEFAULT 0.00,
  `porcentaje_descuento` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id_compra`, `codigo_compra`, `id_proveedor`, `id_usuario`, `fecha_compra`, `total_compra`, `estado`, `descuento_aplicado`, `porcentaje_descuento`) VALUES
(1, 'COMP001', 1, 6, '2025-10-07 14:26:30', 500.00, 'Pagada', 0.00, 0.00),
(2, 'COMP002', 2, 6, '2025-10-07 14:26:30', 800.00, 'Pagada', 0.00, 0.00),
(3, 'COMP003', 3, 7, '2025-10-07 14:26:30', 200.00, 'Pagada', 0.00, 0.00),
(4, 'COMP004', 4, 7, '2025-10-07 14:26:30', 1000.00, 'Pagada', 0.00, 0.00),
(5, 'COMP005', 5, 6, '2025-10-07 14:26:30', 300.00, 'Pagada', 0.00, 0.00),
(6, 'COMP006', 6, 6, '2025-10-07 14:26:30', 150.00, 'Pagada', 0.00, 0.00),
(7, 'COMP007', 7, 7, '2025-10-07 14:26:30', 120.00, 'Pagada', 0.00, 0.00),
(8, 'COMP008', 8, 6, '2025-10-07 14:26:30', 250.00, 'Pagada', 0.00, 0.00),
(9, 'COMP009', 9, 7, '2025-10-07 14:26:30', 400.00, 'Pagada', 0.00, 0.00),
(10, 'COMP010', 10, 6, '2025-10-07 14:26:30', 700.00, 'Pagada', 0.00, 0.00),
(11, 'COMP011', 4, 1, '2025-10-13 21:29:08', 3750000.00, 'Pagada', 0.00, 0.00),
(12, 'COMP012', 9, 1, '2025-10-14 12:28:35', 1600000.00, 'Pagada', 0.00, 0.00),
(13, 'COMP013', 9, 1, '2025-10-26 17:13:38', 245000.00, 'Pagada', 0.00, 2.00);

--
-- Disparadores `compras`
--
DELIMITER $$
CREATE TRIGGER `before_insert_compras` BEFORE INSERT ON `compras` FOR EACH ROW BEGIN
    IF NEW.codigo_compra IS NULL OR NEW.codigo_compra = '' THEN
        SET NEW.codigo_compra = CONCAT('COMP', LPAD(NEW.id_compra, 3, '0'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_contables`
--

CREATE TABLE `cuentas_contables` (
  `id_cuenta` int(11) NOT NULL,
  `codigo_cuenta` varchar(100) NOT NULL,
  `nombre_cuenta` varchar(255) NOT NULL,
  `tipo_cuenta` enum('Activo','Pasivo','Patrimonio','Ingreso','Costo','Gasto','Orden') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('Activa','Inactiva') DEFAULT 'Activa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuentas_contables`
--

INSERT INTO `cuentas_contables` (`id_cuenta`, `codigo_cuenta`, `nombre_cuenta`, `tipo_cuenta`, `descripcion`, `estado`, `created_at`) VALUES
(1, '1105', 'Caja', 'Activo', 'Efectivo en caja general', 'Activa', '2025-10-14 23:20:01'),
(2, '110505', 'Caja General', 'Activo', 'Efectivo en caja principal', 'Activa', '2025-10-14 23:20:01'),
(3, '1110', 'Bancos', 'Activo', 'Depósitos en entidades financieras', 'Activa', '2025-10-14 23:20:01'),
(4, '111005', 'Cuenta Corriente', 'Activo', 'Cuenta corriente bancaria', 'Activa', '2025-10-14 23:20:01'),
(5, '111010', 'Cuenta de Ahorros', 'Activo', 'Cuenta de ahorros', 'Activa', '2025-10-14 23:20:01'),
(6, '1115', 'Inversiones', 'Activo', 'Inversiones temporales', 'Activa', '2025-10-14 23:20:01'),
(7, '1305', 'Clientes', 'Activo', 'Cuentas por cobrar a clientes', 'Activa', '2025-10-14 23:20:01'),
(8, '130505', 'Clientes Nacionales', 'Activo', 'Clientes del país', 'Activa', '2025-10-14 23:20:01'),
(9, '130510', 'Clientes Internacionales', 'Activo', 'Clientes del exterior', 'Activa', '2025-10-14 23:20:01'),
(10, '1345', 'Anticipos', 'Activo', 'Anticipos a proveedores', 'Activa', '2025-10-14 23:20:01'),
(11, '14', 'Inventarios', 'Activo', 'Mercancías y productos', 'Activa', '2025-10-14 23:20:01'),
(12, '1405', 'Materias Primas', 'Activo', 'Materiales para producción', 'Activa', '2025-10-14 23:20:01'),
(13, '1410', 'Productos en Proceso', 'Activo', 'Productos en fabricación', 'Activa', '2025-10-14 23:20:01'),
(14, '1415', 'Productos Terminados', 'Activo', 'Productos listos para venta', 'Activa', '2025-10-14 23:20:01'),
(15, '1420', 'Mercancías No Fabricadas', 'Activo', 'Mercancías para la venta', 'Activa', '2025-10-14 23:20:01'),
(16, '1430', 'Bienes en Consignación', 'Activo', 'Mercancías en consignación', 'Activa', '2025-10-14 23:20:01'),
(17, '1435', 'Inventario en Tránsito', 'Activo', 'Mercancías en transporte', 'Activa', '2025-10-14 23:20:01'),
(18, '1440', 'Suministros', 'Activo', 'Materiales y suministros', 'Activa', '2025-10-14 23:20:01'),
(19, '1445', 'Envases y Empaques', 'Activo', 'Materiales de empaque', 'Activa', '2025-10-14 23:20:01'),
(20, '1450', 'Productos Agrícolas', 'Activo', 'Productos del sector agrícola', 'Activa', '2025-10-14 23:20:01'),
(21, '1455', 'Animales en Desarrollo', 'Activo', 'Animales en crecimiento', 'Activa', '2025-10-14 23:20:01'),
(22, '15', 'Propiedades, Planta y Equipo', 'Activo', 'Activos fijos', 'Activa', '2025-10-14 23:20:01'),
(23, '1510', 'Terrenos', 'Activo', 'Terrenos de propiedad de la empresa', 'Activa', '2025-10-14 23:20:01'),
(24, '1515', 'Edificaciones', 'Activo', 'Edificios y construcciones', 'Activa', '2025-10-14 23:20:01'),
(25, '1520', 'Maquinaria y Equipo', 'Activo', 'Maquinaria y equipo de producción', 'Activa', '2025-10-14 23:20:01'),
(26, '1525', 'Equipo de Oficina', 'Activo', 'Muebles y enseres de oficina', 'Activa', '2025-10-14 23:20:01'),
(27, '1530', 'Vehículos', 'Activo', 'Vehículos de transporte', 'Activa', '2025-10-14 23:20:01'),
(28, '1535', 'Equipo de Computación', 'Activo', 'Equipos de computación y comunicación', 'Activa', '2025-10-14 23:20:01'),
(29, '1540', 'Depreciación Acumulada', 'Activo', 'Depreciación acumulada de activos fijos', 'Activa', '2025-10-14 23:20:01'),
(30, '21', 'Obligaciones Financieras', 'Pasivo', 'Préstamos y obligaciones', 'Activa', '2025-10-14 23:20:01'),
(31, '2105', 'Bancos Nacionales', 'Pasivo', 'Préstamos de bancos nacionales', 'Activa', '2025-10-14 23:20:01'),
(32, '22', 'Proveedores', 'Pasivo', 'Cuentas por pagar a proveedores', 'Activa', '2025-10-14 23:20:01'),
(33, '2205', 'Proveedores Nacionales', 'Pasivo', 'Proveedores del país', 'Activa', '2025-10-14 23:20:01'),
(34, '2210', 'Proveedores Internacionales', 'Pasivo', 'Proveedores del exterior', 'Activa', '2025-10-14 23:20:01'),
(35, '23', 'Cuentas por Pagar', 'Pasivo', 'Otras cuentas por pagar', 'Activa', '2025-10-14 23:20:01'),
(36, '2335', 'Acreedores Varios', 'Pasivo', 'Otros acreedores diversos', 'Activa', '2025-10-14 23:20:01'),
(37, '24', 'Impuestos por Pagar', 'Pasivo', 'Obligaciones tributarias', 'Activa', '2025-10-14 23:20:01'),
(38, '2408', 'IVA por Pagar', 'Pasivo', 'IVA causado por pagar', 'Activa', '2025-10-14 23:20:01'),
(39, '2412', 'Retención en la Fuente', 'Pasivo', 'Retención en la fuente por pagar', 'Activa', '2025-10-14 23:20:01'),
(40, '25', 'Obligaciones Laborales', 'Pasivo', 'Prestaciones sociales', 'Activa', '2025-10-14 23:20:01'),
(41, '2505', 'Salarios por Pagar', 'Pasivo', 'Salarios pendientes de pago', 'Activa', '2025-10-14 23:20:01'),
(42, '2510', 'Prestaciones Sociales', 'Pasivo', 'Prestaciones sociales por pagar', 'Activa', '2025-10-14 23:20:01'),
(43, '26', 'Diferidos', 'Pasivo', 'Ingresos diferidos', 'Activa', '2025-10-14 23:20:01'),
(44, '2605', 'Ingresos Diferidos', 'Pasivo', 'Ingresos recibidos por anticipado', 'Activa', '2025-10-14 23:20:01'),
(45, '31', 'Capital Social', 'Patrimonio', 'Aportes de los socios', 'Activa', '2025-10-14 23:20:01'),
(46, '3105', 'Capital Autorizado', 'Patrimonio', 'Capital social autorizado', 'Activa', '2025-10-14 23:20:01'),
(47, '3110', 'Capital por Suscribir', 'Patrimonio', 'Capital pendiente de pago', 'Activa', '2025-10-14 23:20:01'),
(48, '3115', 'Capital Pagado', 'Patrimonio', 'Capital efectivamente pagado', 'Activa', '2025-10-14 23:20:01'),
(49, '3120', 'Aportes Sociales', 'Patrimonio', 'Aportes de los accionistas', 'Activa', '2025-10-14 23:20:01'),
(50, '33', 'Reservas', 'Patrimonio', 'Reservas de la empresa', 'Activa', '2025-10-14 23:20:01'),
(51, '3305', 'Reserva Legal', 'Patrimonio', 'Reserva legal obligatoria', 'Activa', '2025-10-14 23:20:01'),
(52, '3310', 'Reservas Ocasionales', 'Patrimonio', 'Reservas voluntarias', 'Activa', '2025-10-14 23:20:01'),
(53, '3315', 'Reserva para Reposición', 'Patrimonio', 'Reserva para activos', 'Activa', '2025-10-14 23:20:01'),
(54, '34', 'Resultados del Ejercicio', 'Patrimonio', 'Utilidades o pérdidas', 'Activa', '2025-10-14 23:20:01'),
(55, '3405', 'Utilidad del Ejercicio', 'Patrimonio', 'Ganancias del período', 'Activa', '2025-10-14 23:20:01'),
(56, '3410', 'Pérdida del Ejercicio', 'Patrimonio', 'Pérdidas del período', 'Activa', '2025-10-14 23:20:01'),
(57, '36', 'Resultados de Ejercicios Anteriores', 'Patrimonio', 'Resultados acumulados', 'Activa', '2025-10-14 23:20:01'),
(58, '41', 'Ingresos Operacionales', 'Ingreso', 'Ingresos de actividades principales', 'Activa', '2025-10-14 23:20:01'),
(59, '4135', 'Ventas', 'Ingreso', 'Ingresos por venta de productos', 'Activa', '2025-10-14 23:20:01'),
(60, '413505', 'Ventas Productos Terminados', 'Ingreso', 'Ventas de productos fabricados', 'Activa', '2025-10-14 23:20:01'),
(61, '413510', 'Ventas Mercancías No Fabricadas', 'Ingreso', 'Ventas de mercancías compradas', 'Activa', '2025-10-14 23:20:01'),
(62, '413515', 'Ventas Servicios', 'Ingreso', 'Ventas de servicios prestados', 'Activa', '2025-10-14 23:20:01'),
(63, '4175', 'Devoluciones en Ventas', 'Ingreso', 'Devoluciones y rebajas sobre ventas', 'Activa', '2025-10-14 23:20:01'),
(64, '42', 'Ingresos No Operacionales', 'Ingreso', 'Otros ingresos', 'Activa', '2025-10-14 23:20:01'),
(65, '4210', 'Financieros', 'Ingreso', 'Ingresos por inversiones', 'Activa', '2025-10-14 23:20:01'),
(66, '4215', 'Otros Ingresos', 'Ingreso', 'Otros ingresos diversos', 'Activa', '2025-10-14 23:20:01'),
(67, '61', 'Costos de Ventas', 'Costo', 'Costos asociados a las ventas', 'Activa', '2025-10-14 23:20:01'),
(68, '6135', 'Costo de Ventas', 'Costo', 'Costo de mercancías vendidas', 'Activa', '2025-10-14 23:20:01'),
(69, '613505', 'Costo Ventas Productos Terminados', 'Costo', 'Costo de productos vendidos', 'Activa', '2025-10-14 23:20:01'),
(70, '613510', 'Costo Ventas Mercancías No Fabricadas', 'Costo', 'Costo de mercancías vendidas', 'Activa', '2025-10-14 23:20:01'),
(71, '613515', 'Costo Ventas Servicios', 'Costo', 'Costo de servicios prestados', 'Activa', '2025-10-14 23:20:01'),
(72, '6175', 'Devoluciones en Costos', 'Costo', 'Devoluciones en costos de ventas', 'Activa', '2025-10-14 23:20:01'),
(73, '62', 'Compras', 'Costo', 'Compras de mercancías', 'Activa', '2025-10-14 23:20:01'),
(74, '6205', 'Compras', 'Costo', 'Compras de inventarios', 'Activa', '2025-10-14 23:20:01'),
(75, '620505', 'Compras Mercancías', 'Costo', 'Compras para la venta', 'Activa', '2025-10-14 23:20:01'),
(76, '620510', 'Compras Materias Primas', 'Costo', 'Compras para producción', 'Activa', '2025-10-14 23:20:01'),
(77, '6255', 'Devoluciones en Compras', 'Costo', 'Devoluciones a proveedores', 'Activa', '2025-10-14 23:20:01'),
(78, '51', 'Gastos de Operación', 'Gasto', 'Gastos de administración y ventas', 'Activa', '2025-10-14 23:20:01'),
(79, '5105', 'Gastos de Personal', 'Gasto', 'Salarios y prestaciones', 'Activa', '2025-10-14 23:20:01'),
(80, '510505', 'Salarios', 'Gasto', 'Remuneraciones al personal', 'Activa', '2025-10-14 23:20:01'),
(81, '510510', 'Prestaciones Sociales', 'Gasto', 'Prestaciones legales', 'Activa', '2025-10-14 23:20:01'),
(82, '5110', 'Honorarios', 'Gasto', 'Honorarios a terceros', 'Activa', '2025-10-14 23:20:01'),
(83, '5115', 'Impuestos', 'Gasto', 'Impuestos y tasas', 'Activa', '2025-10-14 23:20:01'),
(84, '5120', 'Arrendamientos', 'Gasto', 'Alquileres y arrendamientos', 'Activa', '2025-10-14 23:20:01'),
(85, '5125', 'Servicios Públicos', 'Gasto', 'Agua, luz, teléfono', 'Activa', '2025-10-14 23:20:01'),
(86, '5130', 'Mantenimiento', 'Gasto', 'Mantenimiento de equipos', 'Activa', '2025-10-14 23:20:01'),
(87, '5135', 'Transporte', 'Gasto', 'Fletes y transportes', 'Activa', '2025-10-14 23:20:01'),
(88, '5140', 'Seguros', 'Gasto', 'Primas de seguros', 'Activa', '2025-10-14 23:20:01'),
(89, '5145', 'Comisiones', 'Gasto', 'Comisiones sobre ventas', 'Activa', '2025-10-14 23:20:01'),
(90, '5150', 'Publicidad', 'Gasto', 'Gastos de publicidad', 'Activa', '2025-10-14 23:20:01'),
(91, '5155', 'Gastos de Viaje', 'Gasto', 'Viáticos y viajes', 'Activa', '2025-10-14 23:20:01'),
(92, '5160', 'Papelería y Útiles', 'Gasto', 'Materiales de oficina', 'Activa', '2025-10-14 23:20:01'),
(93, '5165', 'Depreciación', 'Gasto', 'Depreciación de activos', 'Activa', '2025-10-14 23:20:01'),
(94, '5195', 'Otros Gastos', 'Gasto', 'Otros gastos operacionales', 'Activa', '2025-10-14 23:20:01'),
(95, '52', 'Gastos Financieros', 'Gasto', 'Gastos por intereses', 'Activa', '2025-10-14 23:20:01'),
(96, '53', 'Gastos Extraordinarios', 'Gasto', 'Gastos no operacionales', 'Activa', '2025-10-14 23:20:01'),
(97, '81', 'Cuentas de Orden Deudoras', 'Orden', 'Contingencias deudoras', 'Activa', '2025-10-14 23:20:01'),
(98, '82', 'Cuentas de Orden Acreedoras', 'Orden', 'Contingencias acreedoras', 'Activa', '2025-10-14 23:20:01');

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
(12, 12, 10, 20.00, 80000.00),
(13, 13, 2, 5.00, 0.00);

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
(32, 77, 7, 5.00, 7000.00),
(33, 79, 10, 4.00, 150000.00),
(34, 80, 8, 3.00, 4000.00),
(35, 80, 5, 1.00, 30000.00),
(36, 80, 6, 1.00, 5500.00),
(37, 81, 1, 1.00, 35000.00),
(38, 82, 6, 5.00, 5500.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos_operativos`
--

CREATE TABLE `gastos_operativos` (
  `id_gasto` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `valor` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'Usuarios', 'Gestión de usuarios del sistema', 'user', '/usuarios', 'Activo', 1),
(2, 'Clientes', 'Gestión de clientes', 'users', '/clientes', 'Activo', 2),
(3, 'Proveedores', 'Gestión de proveedores', 'truck', '/proveedores', 'Activo', 3),
(4, 'Productos', 'Gestión de productos', 'box', '/productos', 'Activo', 4),
(5, 'Compras', 'Registro de compras', 'shopping-cart', '/compras', 'Activo', 5),
(6, 'Ventas', 'Registro de ventas', 'cash-register', '/ventas', 'Activo', 6),
(7, 'Inventario', 'Control de inventario', 'warehouse', '/inventario', 'Activo', 7),
(8, 'Pagos', 'Control de pagos', 'money-bill', '/pagos', 'Activo', 8),
(9, 'Reportes', 'Reportes y estadísticas', 'chart-line', '/reportes', 'Activo', 9),
(10, 'Configuración', 'Ajustes del sistema', 'cog', '/configuracion', 'Activo', 10);

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
(35, 10, 'Entrada', 20.00, 'Compra #C20251014192818', 1, '2025-10-14 12:28:35'),
(36, 10, 'Salida', 4.00, 'Venta #VENTA028', 1, '2025-10-14 22:01:33'),
(37, 10, 'Entrada', 4.00, 'Anulación de venta ID #79', 1, '2025-10-14 22:10:58'),
(38, 8, 'Salida', 3.00, 'Venta #VENTA029', 1, '2025-10-14 22:11:52'),
(39, 5, 'Salida', 1.00, 'Venta #VENTA029', 1, '2025-10-14 22:11:52'),
(40, 6, 'Salida', 1.00, 'Venta #VENTA029', 1, '2025-10-14 22:11:52'),
(41, 8, 'Entrada', 3.00, 'Anulación de venta ID #80', 1, '2025-10-14 22:30:48'),
(42, 5, 'Entrada', 1.00, 'Anulación de venta ID #80', 1, '2025-10-14 22:30:48'),
(43, 6, 'Entrada', 1.00, 'Anulación de venta ID #80', 1, '2025-10-14 22:30:48'),
(44, 1, 'Entrada', 6.00, 'Anulación de venta ID #68', 1, '2025-10-14 22:31:03'),
(45, 7, 'Entrada', 5.00, 'Anulación de venta ID #77', 1, '2025-10-14 22:31:21'),
(46, 9, 'Entrada', 5.00, 'Anulación de venta ID #76', 1, '2025-10-14 22:31:25'),
(47, 10, 'Entrada', 1.00, 'Anulación de venta ID #75', 1, '2025-10-14 22:32:33'),
(48, 4, 'Entrada', 3.00, 'Anulación de venta ID #16', 1, '2025-10-14 22:32:37'),
(49, 9, 'Entrada', 2.00, 'Anulación de venta ID #74', 1, '2025-10-14 22:49:50'),
(50, 5, 'Entrada', 1.00, 'Anulación de venta ID #73', 1, '2025-10-14 22:50:01'),
(51, 11, 'Entrada', 2.00, 'Anulación de venta ID #72', 1, '2025-10-14 23:01:50'),
(52, 1, 'Entrada', 5.00, 'Anulación de venta ID #1', 1, '2025-10-14 23:05:07'),
(53, 8, 'Entrada', 3.00, 'Reversión por anulación de venta ID 80', NULL, '2025-10-14 23:18:35'),
(54, 5, 'Entrada', 1.00, 'Reversión por anulación de venta ID 80', NULL, '2025-10-14 23:18:35'),
(55, 6, 'Entrada', 1.00, 'Reversión por anulación de venta ID 80', NULL, '2025-10-14 23:18:35'),
(56, 10, 'Entrada', 4.00, 'Reversión por anulación de venta ID 79', NULL, '2025-10-14 23:18:59'),
(57, 1, 'Salida', 1.00, 'Venta #VENTA030', 1, '2025-10-14 23:20:06'),
(58, 1, 'Entrada', 1.00, 'Reversión por anulación de venta ID 81', NULL, '2025-10-14 23:40:06'),
(59, 1, 'Entrada', 1.00, 'Reversión por anulación de venta ID 81', NULL, '2025-10-15 16:10:42'),
(60, 1, 'Entrada', 1.00, 'Reversión por anulación de venta ID 81', NULL, '2025-10-15 16:23:37'),
(61, 6, 'Salida', 5.00, 'Venta #VENTA031', 1, '2025-10-24 13:37:18'),
(62, 6, 'Entrada', 5.00, 'Reversión por anulación de venta ID 82', NULL, '2025-10-24 13:54:04'),
(63, 1, 'Entrada', 1.00, 'Reversión por anulación de venta ID 81', NULL, '2025-10-24 14:36:10'),
(64, 6, 'Entrada', 5.00, 'Reversión por anulación de venta ID 82', NULL, '2025-10-24 14:39:27'),
(65, 7, 'Entrada', 5.00, 'Reversión por anulación de venta ID 77', NULL, '2025-10-24 14:41:47'),
(66, 10, 'Salida', 20.00, 'Anulación de compra #COMP012', 1, '2025-10-26 16:04:06'),
(67, 10, 'Entrada', 20.00, 'Reanudación de compra #COMP012', 1, '2025-10-26 16:04:16'),
(68, 6, 'Entrada', 5.00, 'Reversión por anulación de venta ID 82', NULL, '2025-10-26 16:04:35'),
(69, 4, 'Entrada', 1.00, 'Reanudación de compra #COMP004', 7, '2025-10-26 16:04:48'),
(70, 10, 'Salida', 20.00, 'Anulación de compra #COMP012', 1, '2025-10-26 16:04:56'),
(71, 10, 'Entrada', 20.00, 'Reanudación de compra #COMP012', 1, '2025-10-26 16:05:01'),
(72, 2, 'Entrada', 5.00, 'Compra #COMP013', 1, '2025-10-26 17:13:38'),
(73, 2, 'Salida', 5.00, 'Anulación de compra #COMP013', 1, '2025-10-26 17:20:36'),
(74, 2, 'Entrada', 5.00, 'Reanudación de compra #COMP013', 1, '2025-10-26 17:20:44'),
(75, 2, 'Salida', 5.00, 'Anulación de compra #COMP013', 1, '2025-10-26 17:21:13'),
(76, 2, 'Entrada', 5.00, 'Reanudación de compra #COMP013', 1, '2025-10-26 17:21:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_contables`
--

CREATE TABLE `movimientos_contables` (
  `id_movimiento` int(11) NOT NULL,
  `id_cuenta` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo_movimiento` enum('DEBITO','CREDITO') NOT NULL,
  `valor` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(12, 'Egreso', '458766899', 'Pago de mercancía.', 1000000.00, '2025-10-09 15:42:33', 'Efectivo', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_roles`
--

CREATE TABLE `permisos_roles` (
  `id_permiso` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `puede_ver` tinyint(1) NOT NULL DEFAULT 0,
  `puede_crear` tinyint(1) NOT NULL DEFAULT 0,
  `puede_editar` tinyint(1) NOT NULL DEFAULT 0,
  `puede_eliminar` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos_roles`
--

INSERT INTO `permisos_roles` (`id_permiso`, `id_rol`, `id_modulo`, `puede_ver`, `puede_crear`, `puede_editar`, `puede_eliminar`) VALUES
(31, 1, 1, 1, 1, 1, 1),
(32, 1, 2, 1, 1, 1, 1),
(33, 1, 3, 1, 1, 1, 1),
(34, 1, 4, 1, 1, 1, 1),
(35, 1, 5, 1, 1, 1, 1),
(36, 1, 6, 1, 1, 1, 1),
(37, 1, 7, 1, 1, 1, 1),
(38, 1, 8, 1, 1, 1, 1),
(39, 1, 9, 1, 1, 1, 1),
(40, 1, 10, 1, 1, 1, 1),
(46, 2, 2, 1, 1, 0, 0),
(47, 2, 6, 1, 1, 1, 0),
(48, 2, 8, 1, 1, 0, 0),
(49, 3, 5, 1, 0, 0, 0),
(50, 3, 6, 1, 0, 0, 0),
(51, 3, 8, 1, 0, 1, 0),
(52, 3, 9, 1, 0, 1, 0),
(53, 4, 3, 1, 1, 1, 0),
(54, 4, 5, 1, 1, 1, 0),
(55, 4, 8, 1, 1, 0, 0),
(56, 5, 4, 1, 1, 1, 0),
(57, 5, 7, 1, 0, 1, 0);

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
(1, 'ELE001', 'Cargador USB', 1, 'Cargador rápido para celulares', 28.00, 10.00, 'Unidad', 20000.00, 35000.00, 'Activo'),
(2, 'ELE002', 'Auriculares Bluetooth', 1, 'Auriculares inalámbricos', 35.00, 5.00, 'Unidad', 0.00, 80000.00, 'Activo'),
(3, 'MUE001', 'Silla Oficina', 2, 'Silla ergonómica de oficina', 11.00, 2.00, 'Unidad', 120000.00, 200000.00, 'Activo'),
(4, 'MUE002', 'Mesa Comedor', 2, 'Mesa de madera para comedor', 19.00, 1.00, 'Unidad', 250000.00, 400000.00, 'Activo'),
(5, 'ROP001', 'Camiseta Hombre', 3, 'Camiseta de algodón talla M', 93.00, 20.00, 'Unidad', 15000.00, 30000.00, 'Activo'),
(6, 'ALI001', 'Arroz 1kg', 4, 'Arroz blanco', 161.00, 50.00, 'Paquete', 3000.00, 5500.00, 'Activo'),
(7, 'BEB001', 'Gaseosa 2L', 5, 'Bebida carbonatada', 83.00, 20.00, 'Unidad', 4000.00, 7000.00, 'Activo'),
(8, 'PAP001', 'Cuaderno A4', 6, 'Cuaderno rayado 100 hojas', 53.00, 30.00, 'Unidad', 2000.00, 4000.00, 'Activo'),
(9, 'JUG001', 'Pelota Futbol', 7, 'Pelota profesional', 21.00, 5.00, 'Unidad', 30000.00, 55000.00, 'Activo'),
(10, 'DEPO001', 'Raqueta Tenis', 8, 'Raqueta de tenis profesional', 27.00, 2.00, 'Unidad', 80000.00, 150000.00, 'Activo'),
(11, 'BEL001', 'Gel Capilar', 9, 'Gel para el cabello.', 15.00, 10.00, 'Unidad', 23000.00, 30000.00, 'Activo');

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
(1, 'Provelectro S.A.', '900111222', '3101234567', 'contacto@provelectro.com', 'Calle 100 #10-20', 'Bogotá', '2025-10-07 14:26:30'),
(2, 'Muebles y Más', '900333444', '3112345678', 'ventas@mueblesymas.com', 'Carrera 50 #25-30', 'Medellín', '2025-10-07 14:26:30'),
(3, 'Alimentos del Sur', '900555666', '3123456789', 'info@alimentosdelsur.com', 'Calle 80 #15-20', 'Cali', '2025-10-07 14:26:30'),
(4, 'Distribuciones Caribe', '900777888', '3134567890', 'ventas@caribe.com', 'Carrera 60 #40-50', 'Barranquilla', '2025-10-07 14:26:30'),
(5, 'Ropa Fashion', '900999000', '3145678901', 'contacto@ropafashion.com', 'Calle 70 #30-10', 'Cartagena', '2025-10-07 14:26:30'),
(6, 'Bebidas y Licores', '901111222', '3156789012', 'ventas@bebidas.com', 'Carrera 20 #15-40', 'Bucaramanga', '2025-10-07 14:26:30'),
(7, 'Papelería Total', '901333444', '3167890123', 'info@papeleriatotal.com', 'Calle 30 #40-20', 'Pereira', '2025-10-07 14:26:30'),
(8, 'Juguetería Infantil', '901555666', '3178901234', 'ventas@juguetes.com', 'Carrera 10 #10-30', 'Manizales', '2025-10-07 14:26:30'),
(9, 'Deportes Activos', '901777888', '3189012345', 'info@deportesactivos.com', 'Calle 15 #20-50', 'Ibagué', '2025-10-07 14:26:30'),
(10, 'Belleza y Estilo', '901999000', '3190123456', 'ventas@belleza.com', 'Carrera 25 #30-60', 'Neiva', '2025-10-07 14:26:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
(1, 'Administrador'),
(2, 'Vendedor'),
(3, 'Contador'),
(4, 'Comprador'),
(5, 'Bodeguero');

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
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `token_recuperacion` varchar(64) DEFAULT NULL,
  `token_expiracion` datetime DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_completo`, `correo`, `usuario`, `contrasena`, `rol`, `estado`, `fecha_creacion`, `token_recuperacion`, `token_expiracion`, `id_rol`) VALUES
(1, 'Admin General', 'cparra02436@gmail.com', 'admin', 'admin123', 'Administrador', 'Activo', '2025-10-07 14:26:30', '2886502f9d5a6066bc76718bb6532143fa9e58d7fe5dd70eb7424b5afce70198', '2025-10-11 15:05:46', NULL),
(2, 'Vendedor 1', 'vendedor1@example.com', 'vend1', 'vend123', 'Vendedor', 'Activo', '2025-10-07 14:26:30', NULL, NULL, NULL),
(3, 'Vendedor 2', 'vendedor2@example.com', 'vend2', 'vend123', 'Vendedor', 'Activo', '2025-10-07 14:26:30', NULL, NULL, NULL),
(4, 'Contador 1', 'conta1@example.com', 'cont1', 'cont123', 'Contador', 'Activo', '2025-10-07 14:26:30', NULL, NULL, NULL),
(5, 'Contador 2', 'conta2@example.com', 'cont2', 'cont123', 'Contador', 'Activo', '2025-10-07 14:26:30', NULL, NULL, NULL),
(6, 'Bodeguero 1', 'bodega1@example.com', 'bod1', 'bod123', 'Bodeguero', 'Activo', '2025-10-07 14:26:30', NULL, NULL, NULL),
(7, 'Bodeguero 2', 'bodega2@example.com', 'bod2', 'bod123', 'Bodeguero', 'Activo', '2025-10-07 14:26:30', NULL, NULL, NULL),
(8, 'Vendedor 3', 'vendedor3@example.com', 'vend3', 'vend123', 'Vendedor', 'Activo', '2025-10-07 14:26:30', NULL, NULL, NULL),
(10, 'Bodeguero 3', 'bodega3@example.com', 'bod3', 'bod123', 'Bodeguero', 'Activo', '2025-10-07 14:26:30', NULL, NULL, NULL);

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
  `descuento_aplicado` decimal(10,2) DEFAULT 0.00,
  `porcentaje_descuento` decimal(5,2) DEFAULT 0.00,
  `estado` enum('Pendiente','Pagada','Anulada') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `codigo_venta`, `id_cliente`, `id_usuario`, `fecha_venta`, `metodo_pago`, `total_venta`, `descuento_aplicado`, `porcentaje_descuento`, `estado`) VALUES
(1, 'VENTA001', 1, 2, '2025-10-07 14:26:30', 'Efectivo', 350.00, 0.00, 0.00, 'Pagada'),
(2, 'VENTA002', 2, 3, '2025-10-07 14:26:30', 'Transferencia', 450.00, 0.00, 0.00, 'Pagada'),
(3, 'VENTA003', 3, 2, '2025-10-07 14:26:30', 'Tarjeta', 200.00, 0.00, 0.00, 'Pagada'),
(4, 'VENTA004', 4, 3, '2025-10-07 14:26:30', 'Crédito', 500.00, 0.00, 0.00, 'Pagada'),
(5, 'VENTA005', 5, 2, '2025-10-07 14:26:30', 'Efectivo', 300.00, 0.00, 0.00, 'Pagada'),
(6, 'VENTA006', 6, 3, '2025-10-07 14:26:30', 'Transferencia', 150.00, 0.00, 0.00, 'Pagada'),
(7, 'VENTA007', 7, 2, '2025-10-07 14:26:30', 'Tarjeta', 120.00, 0.00, 0.00, 'Pagada'),
(8, 'VENTA008', 8, 3, '2025-10-07 14:26:30', 'Efectivo', 250.00, 0.00, 0.00, 'Pagada'),
(9, 'VENTA009', 9, 2, '2025-10-07 14:26:30', 'Transferencia', 400.00, 0.00, 0.00, 'Pagada'),
(10, 'VENTA010', 10, 3, '2025-10-07 14:26:30', 'Crédito', 700.00, 0.00, 0.00, 'Pagada'),
(11, 'VENTA011', 3, 1, '2025-10-07 18:05:06', 'Tarjeta', 450000.00, 0.00, 0.00, 'Pagada'),
(12, 'VENTA012', 3, 1, '2025-10-07 18:17:11', 'Tarjeta', 476000.00, 0.00, 0.00, 'Pagada'),
(13, 'VENTA013', 8, 1, '2025-10-07 18:26:15', 'Crédito', 525000.00, 0.00, 0.00, 'Pagada'),
(14, 'VENTA014', 5, 1, '2025-10-08 10:26:14', 'Transferencia', 2027500.00, 0.00, 0.00, 'Pagada'),
(15, 'VENTA015', 7, 1, '2025-10-09 13:57:11', 'Efectivo', 1550000.00, 0.00, 0.00, 'Pagada'),
(16, 'VENTA016', 3, 1, '2025-10-09 21:08:44', 'Transferencia', 1140000.00, 0.00, 0.00, 'Pagada'),
(68, 'VENTA017', 4, 1, '2025-10-13 18:54:07', 'Tarjeta', 199500.00, 0.00, 0.00, 'Pagada'),
(69, 'VENTA018', 5, 1, '2025-10-13 20:13:00', 'Tarjeta', 220400.00, 0.00, 0.00, 'Pagada'),
(70, 'VENTA019', 6, 1, '2025-10-13 20:42:42', 'Efectivo', 680000.00, 0.00, 0.00, 'Pagada'),
(71, 'VENTA020', 8, 1, '2025-10-13 20:44:42', 'Tarjeta', 57000.00, 0.00, 0.00, 'Pagada'),
(72, 'VENTA021', 8, 1, '2025-10-13 20:58:18', 'Transferencia', 60000.00, 0.00, 0.00, 'Pagada'),
(73, 'VENTA022', 1, 1, '2025-10-13 21:10:36', 'Efectivo', 30000.00, 0.00, 0.00, 'Pagada'),
(74, 'VENTA023', 1, 1, '2025-10-13 21:11:33', 'Efectivo', 110000.00, 0.00, 0.00, 'Pagada'),
(75, 'VENTA024', 8, 1, '2025-10-13 21:16:39', 'Efectivo', 150000.00, 0.00, 0.00, 'Pagada'),
(76, 'VENTA025', 8, 1, '2025-10-14 12:22:01', 'Tarjeta', 266750.00, 0.00, 0.00, 'Pagada'),
(77, 'VENTA026', 7, 1, '2025-10-14 12:26:34', 'Transferencia', 33950.00, 0.00, 0.00, 'Pagada'),
(78, 'VENTA027', 1, 1, '2025-10-14 21:59:24', 'Efectivo', 50000.00, 0.00, 0.00, 'Pagada'),
(79, 'VENTA028', 10, 1, '2025-10-14 22:01:33', 'Crédito', 582000.00, 18000.00, 3.00, 'Pagada'),
(80, 'VENTA029', 8, 1, '2025-10-14 22:11:52', 'Tarjeta', 45125.00, 2375.00, 5.00, 'Pagada'),
(81, 'VENTA030', 6, 1, '2025-10-14 23:20:06', 'Crédito', 35000.00, 0.00, NULL, 'Pagada'),
(82, 'VENTA031', 4, 1, '2025-10-24 13:37:18', 'Transferencia', 26125.00, 1375.00, 5.00, 'Pagada');

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
  ADD UNIQUE KEY `uk_clientes_identificacion` (`identificacion`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id_compra`),
  ADD UNIQUE KEY `uk_compras_codigo` (`codigo_compra`),
  ADD KEY `idx_compras_id_proveedor` (`id_proveedor`),
  ADD KEY `idx_compras_id_usuario` (`id_usuario`);

--
-- Indices de la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  ADD PRIMARY KEY (`id_cuenta`),
  ADD UNIQUE KEY `uk_codigo_cuenta` (`codigo_cuenta`);

--
-- Indices de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `idx_detcomp_id_compra` (`id_compra`),
  ADD KEY `idx_detcomp_id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `idx_detven_id_venta` (`id_venta`),
  ADD KEY `idx_detven_id_producto` (`id_producto`);

--
-- Indices de la tabla `gastos_operativos`
--
ALTER TABLE `gastos_operativos`
  ADD PRIMARY KEY (`id_gasto`);

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
  ADD KEY `idx_movimientos_producto` (`id_producto`),
  ADD KEY `idx_movimientos_usuario` (`id_usuario`);

--
-- Indices de la tabla `movimientos_contables`
--
ALTER TABLE `movimientos_contables`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_cuenta` (`id_cuenta`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `idx_pagos_usuario` (`id_usuario`);

--
-- Indices de la tabla `permisos_roles`
--
ALTER TABLE `permisos_roles`
  ADD PRIMARY KEY (`id_permiso`),
  ADD UNIQUE KEY `unico_rol_modulo` (`id_rol`,`id_modulo`),
  ADD KEY `id_modulo` (`id_modulo`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `uk_productos_codigo` (`codigo_producto`),
  ADD KEY `idx_productos_id_categoria` (`id_categoria`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id_proveedor`),
  ADD UNIQUE KEY `uk_proveedores_nit` (`nit`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `uk_usuarios_correo` (`correo`),
  ADD UNIQUE KEY `uk_usuarios_usuario` (`usuario`),
  ADD KEY `idx_token_recuperacion` (`token_recuperacion`),
  ADD KEY `fk_usuario_rol` (`id_rol`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD UNIQUE KEY `uk_ventas_codigo` (`codigo_venta`),
  ADD KEY `idx_ventas_cliente` (`id_cliente`),
  ADD KEY `idx_ventas_usuario` (`id_usuario`);

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
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  MODIFY `id_cuenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `gastos_operativos`
--
ALTER TABLE `gastos_operativos`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `modulos_sistema`
--
ALTER TABLE `modulos_sistema`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `movimientos_bodega`
--
ALTER TABLE `movimientos_bodega`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT de la tabla `movimientos_contables`
--
ALTER TABLE `movimientos_contables`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `permisos_roles`
--
ALTER TABLE `permisos_roles`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

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
  ADD CONSTRAINT `fk_permiso_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulos_sistema` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_permiso_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE SET NULL ON UPDATE CASCADE;

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