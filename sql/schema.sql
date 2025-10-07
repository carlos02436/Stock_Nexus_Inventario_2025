-- ===========================================================
-- CREACIÓN DE BASE DE DATOS
-- ===========================================================
CREATE DATABASE IF NOT EXISTS Stock_Nexus_Inventario_2025
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE Stock_Nexus_Inventario_2025;

-- ===========================================================
-- TABLA: USUARIOS (Módulo Seguridad)
-- ===========================================================
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('Administrador', 'Vendedor', 'Contador', 'Bodeguero') DEFAULT 'Vendedor',
    estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ===========================================================
-- TABLA: CLIENTES
-- ===========================================================
CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(100) NOT NULL,
    identificacion VARCHAR(30) UNIQUE,
    telefono VARCHAR(20),
    correo VARCHAR(100),
    direccion VARCHAR(150),
    ciudad VARCHAR(100),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ===========================================================
-- TABLA: PROVEEDORES
-- ===========================================================
CREATE TABLE proveedores (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    nombre_proveedor VARCHAR(100) NOT NULL,
    nit VARCHAR(30) UNIQUE,
    telefono VARCHAR(20),
    correo VARCHAR(100),
    direccion VARCHAR(150),
    ciudad VARCHAR(100),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ===========================================================
-- TABLA: CATEGORÍAS DE PRODUCTOS
-- ===========================================================
CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL,
    descripcion TEXT,
    estado ENUM('Activo','Inactivo') DEFAULT 'Activo'
);

-- ===========================================================
-- TABLA: PRODUCTOS
-- ===========================================================
CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    codigo_producto VARCHAR(50) UNIQUE NOT NULL,
    nombre_producto VARCHAR(150) NOT NULL,
    id_categoria INT,
    descripcion TEXT,
    stock_actual DECIMAL(10,2) DEFAULT 0,
    stock_minimo DECIMAL(10,2) DEFAULT 0,
    unidad_medida VARCHAR(30) DEFAULT 'Unidad',
    precio_compra DECIMAL(12,2) DEFAULT 0,
    precio_venta DECIMAL(12,2) DEFAULT 0,
    estado ENUM('Activo','Inactivo') DEFAULT 'Activo',
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
);

-- ===========================================================
-- TABLA: BODEGA (Movimientos de Inventario)
-- ===========================================================
CREATE TABLE movimientos_bodega (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    tipo_movimiento ENUM('Entrada', 'Salida', 'Ajuste') NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    descripcion TEXT,
    id_usuario INT,
    fecha_movimiento DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- ===========================================================
-- TABLA: COMPRAS
-- ===========================================================
CREATE TABLE compras (
    id_compra INT AUTO_INCREMENT PRIMARY KEY,
    codigo_compra VARCHAR(50) UNIQUE NOT NULL,
    id_proveedor INT NOT NULL,
    id_usuario INT NOT NULL,
    fecha_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_compra DECIMAL(14,2) DEFAULT 0,
    estado ENUM('Pendiente','Pagada','Anulada') DEFAULT 'Pendiente',
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- ===========================================================
-- TABLA: DETALLE DE COMPRAS
-- ===========================================================
CREATE TABLE detalle_compras (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_compra INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    precio_unitario DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(14,2) GENERATED ALWAYS AS (cantidad * precio_unitario) STORED,
    FOREIGN KEY (id_compra) REFERENCES compras(id_compra),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- ===========================================================
-- TABLA: VENTAS
-- ===========================================================
CREATE TABLE ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    codigo_venta VARCHAR(50) UNIQUE NOT NULL,
    id_cliente INT,
    id_usuario INT NOT NULL,
    fecha_venta DATETIME DEFAULT CURRENT_TIMESTAMP,
    metodo_pago ENUM('Efectivo', 'Transferencia', 'Tarjeta', 'Crédito') DEFAULT 'Efectivo',
    total_venta DECIMAL(14,2) DEFAULT 0,
    estado ENUM('Pendiente','Pagada','Anulada') DEFAULT 'Pendiente',
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- ===========================================================
-- TABLA: DETALLE DE VENTAS
-- ===========================================================
CREATE TABLE detalle_ventas (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    precio_unitario DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(14,2) GENERATED ALWAYS AS (cantidad * precio_unitario) STORED,
    FOREIGN KEY (id_venta) REFERENCES ventas(id_venta),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- ===========================================================
-- TABLA: PAGOS (Módulo de Finanzas)
-- ===========================================================
CREATE TABLE pagos (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    tipo_pago ENUM('Ingreso','Egreso') NOT NULL,
    referencia VARCHAR(100),
    descripcion TEXT,
    monto DECIMAL(14,2) NOT NULL,
    fecha_pago DATETIME DEFAULT CURRENT_TIMESTAMP,
    metodo_pago ENUM('Efectivo', 'Transferencia', 'Tarjeta') DEFAULT 'Efectivo',
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- ===========================================================
-- TABLA: BALANCE GENERAL (Resumen contable)
-- ===========================================================
CREATE TABLE balance_general (
    id_balance INT AUTO_INCREMENT PRIMARY KEY,
    fecha_balance DATE DEFAULT (CURRENT_DATE),
    total_ingresos DECIMAL(14,2) DEFAULT 0,
    total_egresos DECIMAL(14,2) DEFAULT 0,
    utilidad DECIMAL(14,2) GENERATED ALWAYS AS (total_ingresos - total_egresos) STORED
);

-- ===========================================================
-- INSERTS: USUARIOS
-- ===========================================================
INSERT INTO usuarios (nombre_completo, correo, usuario, contrasena, rol, estado) VALUES
('Carlos Parra', 'carlos@stocknexus.com', 'carlos_admin', '123456', 'Administrador', 'Activo'),
('Juan Pérez', 'juan@stocknexus.com', 'juan_vendedor', '123456', 'Vendedor', 'Activo'),
('Ana Gómez', 'ana@stocknexus.com', 'ana_contadora', '123456', 'Contador', 'Activo'),
('Luis Herrera', 'luis@stocknexus.com', 'luis_bodega', '123456', 'Bodeguero', 'Activo'),
('María Rojas', 'maria@stocknexus.com', 'maria_vendedora', '123456', 'Vendedor', 'Activo'),
('David López', 'david@stocknexus.com', 'david_admin', '123456', 'Administrador', 'Activo'),
('Sofía Castro', 'sofia@stocknexus.com', 'sofia_cont', '123456', 'Contador', 'Activo'),
('Pedro Ramírez', 'pedro@stocknexus.com', 'pedro_bodega', '123456', 'Bodeguero', 'Activo'),
('Laura Torres', 'laura@stocknexus.com', 'laura_vend', '123456', 'Vendedor', 'Activo'),
('Andrés Díaz', 'andres@stocknexus.com', 'andres_admin', '123456', 'Administrador', 'Activo');

-- ===========================================================
-- INSERTS: CLIENTES
-- ===========================================================
INSERT INTO clientes (nombre_cliente, identificacion, telefono, correo, direccion, ciudad) VALUES
('Comercial S.A.', '900101001-1', '3201112233', 'ventas@comercial.com', 'Cra 10 #25-30', 'Bogotá'),
('Tienda El Sol', '800220032-2', '3123334455', 'contacto@elsol.com', 'Calle 12 #8-10', 'Medellín'),
('Ferretería La 33', '901330045-3', '3105556677', 'info@la33.com', 'Av 33 #45-67', 'Cali'),
('Restaurante El Buen Sabor', '902440056-4', '3112223344', 'gerencia@buen-sabor.com', 'Cl 40 #21-80', 'Barranquilla'),
('Panadería Don Pan', '901550067-5', '3006667788', 'ventas@donpan.com', 'Cl 3 #12-40', 'Cartagena'),
('Insumos Agropecuarios SAS', '900660078-6', '3208889999', 'info@agroinsumos.com', 'Cra 80 #14-22', 'Bucaramanga'),
('Café La Palma', '900770089-7', '3100001111', 'admin@lapalma.com', 'Cl 50 #23-14', 'Pereira'),
('Supermercado Central', '900880090-8', '3113332222', 'contacto@central.com', 'Av 5 #67-10', 'Santa Marta'),
('Distribuidora Del Norte', '900990101-9', '3007778888', 'ventas@norte.com', 'Cra 12 #45-15', 'Cúcuta'),
('Hotel Bahía', '901110112-0', '3202221111', 'reservas@bahia.com', 'Cl 25 #20-50', 'Valledupar');

-- ===========================================================
-- INSERTS: PROVEEDORES
-- ===========================================================
INSERT INTO proveedores (nombre_proveedor, nit, telefono, correo, direccion, ciudad) VALUES
('Proveedora Andina', '900100001-1', '3201234567', 'contacto@andina.com', 'Cl 30 #15-22', 'Bogotá'),
('Distribuciones El Valle', '901200002-2', '3107654321', 'ventas@elvalle.com', 'Av 45 #12-30', 'Cali'),
('Suministros del Norte', '902300003-3', '3119876543', 'info@norte.com', 'Cra 60 #14-10', 'Barranquilla'),
('Importadora del Sur', '903400004-4', '3123456789', 'contacto@importsur.com', 'Cl 22 #18-20', 'Medellín'),
('Ferrosoluciones SAS', '904500005-5', '3002223344', 'ventas@ferrosol.com', 'Av 68 #45-90', 'Bogotá'),
('Papelería Central', '905600006-6', '3015556677', 'info@papecentral.com', 'Cra 9 #30-80', 'Pereira'),
('AgroProveedores', '906700007-7', '3204445566', 'contacto@agropro.com', 'Cl 11 #45-33', 'Montería'),
('TecnoDistribuciones', '907800008-8', '3107778899', 'ventas@tecno.com', 'Cl 35 #8-90', 'Cúcuta'),
('Textiles del Caribe', '908900009-9', '3116667788', 'contacto@caribe.com', 'Av 30 #10-22', 'Cartagena'),
('RefriParts Ltda', '909000010-0', '3129990000', 'info@refriparts.com', 'Cra 80 #11-44', 'Bucaramanga');

-- ===========================================================
-- INSERTS: CATEGORÍAS
-- ===========================================================
INSERT INTO categorias (nombre_categoria, descripcion) VALUES
('Bebidas', 'Refrescos, jugos, cervezas y aguas embotelladas'),
('Alimentos', 'Comestibles y abarrotes en general'),
('Limpieza', 'Artículos de aseo y desinfección'),
('Electrodomésticos', 'Pequeños aparatos eléctricos'),
('Ferretería', 'Herramientas y materiales'),
('Papelería', 'Útiles de oficina y escolares'),
('Tecnología', 'Equipos y accesorios electrónicos'),
('Ropa', 'Prendas de vestir y calzado'),
('Repuestos', 'Componentes mecánicos y eléctricos'),
('Otros', 'Artículos varios y misceláneos');

-- ===========================================================
-- INSERTS: PRODUCTOS
-- ===========================================================
INSERT INTO productos (codigo_producto, nombre_producto, id_categoria, descripcion, stock_actual, stock_minimo, unidad_medida, precio_compra, precio_venta) VALUES
('P001', 'Agua 600ml', 1, 'Botella de agua pura', 150, 30, 'Unidad', 800, 1500),
('P002', 'Gaseosa 1.5L', 1, 'Bebida gaseosa sabor cola', 100, 20, 'Unidad', 2000, 3500),
('P003', 'Arroz 5Kg', 2, 'Arroz blanco premium', 80, 15, 'Bulto', 12000, 18000),
('P004', 'Aceite 1L', 2, 'Aceite vegetal refinado', 90, 20, 'Unidad', 7000, 10500),
('P005', 'Cloro 1L', 3, 'Desinfectante líquido', 120, 25, 'Unidad', 2500, 4200),
('P006', 'Escoba plástica', 3, 'Escoba de cerdas gruesas', 60, 10, 'Unidad', 4000, 7500),
('P007', 'Bombillo LED 9W', 4, 'Luz blanca bajo consumo', 200, 30, 'Unidad', 2500, 6000),
('P008', 'Martillo 16oz', 5, 'Mango de fibra, cabeza de acero', 40, 10, 'Unidad', 10000, 18000),
('P009', 'Cuaderno universitario', 6, '100 hojas cuadriculado', 90, 15, 'Unidad', 2500, 4500),
('P010', 'Cable USB tipo C', 7, '1 metro de longitud', 50, 10, 'Unidad', 3500, 8000);

-- ===========================================================
-- INSERTS: MOVIMIENTOS DE BODEGA
-- ===========================================================
INSERT INTO movimientos_bodega (id_producto, tipo_movimiento, cantidad, descripcion, id_usuario) VALUES
(1, 'Entrada', 150, 'Ingreso inicial de inventario', 4),
(2, 'Entrada', 100, 'Ingreso inicial de inventario', 4),
(3, 'Entrada', 80, 'Ingreso inicial de inventario', 4),
(4, 'Entrada', 90, 'Ingreso inicial de inventario', 4),
(5, 'Entrada', 120, 'Ingreso inicial de inventario', 4),
(6, 'Entrada', 60, 'Ingreso inicial de inventario', 4),
(7, 'Entrada', 200, 'Ingreso inicial de inventario', 4),
(8, 'Entrada', 40, 'Ingreso inicial de inventario', 4),
(9, 'Entrada', 90, 'Ingreso inicial de inventario', 4),
(10, 'Entrada', 50, 'Ingreso inicial de inventario', 4);

-- ===========================================================
-- INSERTS: COMPRAS
-- ===========================================================
INSERT INTO compras (codigo_compra, id_proveedor, id_usuario, total_compra, estado) VALUES
('C001', 1, 2, 350000, 'Pagada'),
('C002', 2, 2, 420000, 'Pagada'),
('C003', 3, 2, 290000, 'Pendiente'),
('C004', 4, 2, 315000, 'Pagada'),
('C005', 5, 2, 175000, 'Pendiente'),
('C006', 6, 2, 212000, 'Pagada'),
('C007', 7, 2, 280000, 'Pagada'),
('C008', 8, 2, 360000, 'Pagada'),
('C009', 9, 2, 190000, 'Pendiente'),
('C010', 10, 2, 450000, 'Pagada');

-- ===========================================================
-- INSERTS: DETALLE DE COMPRAS
-- ===========================================================
INSERT INTO detalle_compras (id_compra, id_producto, cantidad, precio_unitario) VALUES
(1, 1, 100, 800),
(2, 2, 50, 2000),
(3, 3, 40, 12000),
(4, 4, 60, 7000),
(5, 5, 80, 2500),
(6, 6, 30, 4000),
(7, 7, 100, 2500),
(8, 8, 20, 10000),
(9, 9, 60, 2500),
(10, 10, 25, 3500);

-- ===========================================================
-- INSERTS: VENTAS
-- ===========================================================
INSERT INTO ventas (codigo_venta, id_cliente, id_usuario, metodo_pago, total_venta, estado) VALUES
('V001', 1, 2, 'Efectivo', 45000, 'Pagada'),
('V002', 2, 2, 'Tarjeta', 72000, 'Pagada'),
('V003', 3, 2, 'Efectivo', 55000, 'Pendiente'),
('V004', 4, 2, 'Transferencia', 120000, 'Pagada'),
('V005', 5, 2, 'Efectivo', 30000, 'Pagada'),
('V006', 6, 2, 'Crédito', 90000, 'Pendiente'),
('V007', 7, 2, 'Efectivo', 75000, 'Pagada'),
('V008', 8, 2, 'Transferencia', 68000, 'Pagada'),
('V009', 9, 2, 'Efectivo', 49000, 'Pagada'),
('V010', 10, 2, 'Crédito', 115000, 'Pendiente');

-- ===========================================================
-- INSERTS: DETALLE DE VENTAS
-- ===========================================================
INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES
(1, 1, 10, 1500),
(2, 2, 20, 3500),
(3, 3, 3, 18000),
(4, 4, 8, 10500),
(5, 5, 5, 4200),
(6, 6, 4, 7500),
(7, 7, 15, 6000),
(8, 8, 5, 18000),
(9, 9, 8, 4500),
(10, 10, 10, 8000);

-- ===========================================================
-- INSERTS: PAGOS
-- ===========================================================
INSERT INTO pagos (tipo_pago, referencia, descripcion, monto, metodo_pago, id_usuario) VALUES
('Ingreso', 'V001', 'Venta al cliente Comercial S.A.', 45000, 'Efectivo', 3),
('Ingreso', 'V002', 'Venta al cliente Tienda El Sol', 72000, 'Tarjeta', 3),
('Ingreso', 'V004', 'Venta al cliente Buen Sabor', 120000, 'Transferencia', 3),
('Egreso', 'C001', 'Compra a Proveedora Andina', 350000, 'Transferencia', 3),
('Egreso', 'C002', 'Compra a Distribuciones El Valle', 420000, 'Efectivo', 3),
('Ingreso', 'V005', 'Venta al cliente Don Pan', 30000, 'Efectivo', 3),
('Egreso', 'C006', 'Compra a Papelería Central', 212000, 'Tarjeta', 3),
('Ingreso', 'V007', 'Venta al cliente La Palma', 75000, 'Efectivo', 3),
('Egreso', 'C008', 'Compra a TecnoDistribuciones', 360000, 'Transferencia', 3),
('Ingreso', 'V009', 'Venta a Distribuidora del Norte', 49000, 'Efectivo', 3);

-- ===========================================================
-- INSERTS: BALANCE GENERAL
-- ===========================================================
INSERT INTO balance_general (fecha_balance, total_ingresos, total_egresos) VALUES
('2025-01-01', 500000, 300000),
('2025-02-01', 720000, 450000),
('2025-03-01', 600000, 320000),
('2025-04-01', 800000, 500000),
('2025-05-01', 700000, 400000),
('2025-06-01', 900000, 550000),
('2025-07-01', 850000, 480000),
('2025-08-01', 880000, 600000),
('2025-09-01', 920000, 620000),
('2025-10-01', 950000, 650000);