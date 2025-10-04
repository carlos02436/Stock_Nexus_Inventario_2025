-- SQL: Stock_Nexus schema and sample data
CREATE DATABASE IF NOT EXISTS Stock_Nexus CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE Stock_Nexus;

-- Roles
CREATE TABLE IF NOT EXISTS roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL UNIQUE
);

-- Users
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150),
  email VARCHAR(150),
  usuario VARCHAR(100) UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(50),
  active TINYINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT,
  active TINYINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Suppliers
CREATE TABLE IF NOT EXISTS suppliers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  contacto VARCHAR(100),
  telefono VARCHAR(50),
  email VARCHAR(150),
  active TINYINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(50) UNIQUE,
  nombre VARCHAR(150) NOT NULL,
  descripcion TEXT,
  category_id INT,
  supplier_id INT,
  costo_unitario DECIMAL(10,2) DEFAULT 0,
  precio_venta DECIMAL(10,2) DEFAULT 0,
  stock_minimo INT DEFAULT 0,
  stock_actual INT DEFAULT 0,
  unidad_medida VARCHAR(50),
  active TINYINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
);

-- Movements
CREATE TABLE IF NOT EXISTS movements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT,
  type ENUM('ingreso','salida','ajuste'),
  quantity INT,
  unit_cost DECIMAL(10,2),
  total_cost DECIMAL(12,2),
  note TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  created_by INT,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Report logs
CREATE TABLE IF NOT EXISTS reports_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  report_name VARCHAR(150),
  params_json TEXT,
  file_path VARCHAR(255),
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert roles
INSERT INTO roles (nombre) VALUES ('admin'),('compras'),('ventas'),('inventario'),('contabilidad');

-- Password hash placeholder (use scripts/generate_password_hash.php to create real hashes)
SET @h = '$2y$10$J7qfGyKJwG4o9xT2L9W9GuFz5H3uv6I3DYH.n3p1pnhKXgF0Voe.q';

-- Insert users (5)
INSERT INTO users (nombre,email,usuario,password_hash,role) VALUES
('Carlos Parra','carlos@example.com','cparra',@h,'admin'),
('María López','maria@example.com','mlopez',@h,'compras'),
('Juan Pérez','juan@example.com','jperez',@h,'ventas'),
('Carla Ruiz','carla@example.com','cruiz',@h,'inventario'),
('Pedro Gómez','pedro@example.com','pgomez',@h,'contabilidad');

-- Insert categories (5)
INSERT INTO categories (nombre,descripcion) VALUES
('Electrónica','Dispositivos electrónicos'),
('Ropa','Prendas'),
('Alimentos','Productos alimenticios'),
('Herramientas','Herramientas y ferretería'),
('Muebles','Mobiliario');

-- Insert suppliers (5)
INSERT INTO suppliers (nombre,contacto,telefono,email) VALUES
('TechSupplier','Pedro Ruiz','3001112233','tech@example.com'),
('ModaCorp','Sandra Diaz','3102223344','moda@example.com'),
('FoodMaster','Carlos Vega','3203334455','food@example.com'),
('ToolCenter','Jorge Rios','3004445566','tools@example.com'),
('FurniHouse','Paula Mora','3115556677','furni@example.com');

-- Insert products (5)
INSERT INTO products (sku,nombre,descripcion,category_id,supplier_id,costo_unitario,precio_venta,stock_minimo,stock_actual,unidad_medida) VALUES
('ELEC001','Celular Samsung','Teléfono inteligente',1,1,800.00,1000.00,5,20,'unidad'),
('ROP001','Camisa Azul','Camisa de algodón',2,2,20.00,35.00,10,50,'unidad'),
('ALIM001','Arroz 1kg','Bolsa de arroz',3,3,2.00,3.50,50,200,'kg'),
('HER001','Martillo','Martillo de acero',4,4,10.00,15.00,5,30,'unidad'),
('MUE001','Silla Oficina','Silla ergonómica',5,5,40.00,70.00,5,15,'unidad');

-- Insert movements (5)
INSERT INTO movements (product_id,type,quantity,unit_cost,total_cost,created_by,note) VALUES
(1,'ingreso',10,800.00,8000.00,1,'Compra inicial'),
(2,'ingreso',50,20.00,1000.00,1,'Compra inicial'),
(3,'ingreso',200,2.00,400.00,1,'Compra inicial'),
(4,'ingreso',30,10.00,300.00,1,'Compra inicial'),
(5,'ingreso',15,40.00,600.00,1,'Compra inicial');

-- Reports log sample (2)
INSERT INTO reports_log (report_name,params_json,file_path,created_by) VALUES
('inventory_pdf','{}','storage/reports/inventory_sample.pdf',1),
('financial_summary','{"period":"2025-01"}','storage/reports/financial_sample.pdf',1);

