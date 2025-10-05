<?php
namespace App\Models;
use PDO;

class Product extends Model {

    // Obtener todos los productos activos
    public function all() {
        $sql = 'SELECT p.*, c.nombre AS category_name, s.nombre AS supplier_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                WHERE p.active = 1
                ORDER BY p.id DESC';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar producto por ID
    public function find($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nuevo producto
    public function create($data) {
        $sql = 'INSERT INTO products 
                (sku, nombre, descripcion, category_id, supplier_id, costo_unitario, precio_venta, stock_minimo, stock_actual, unidad_medida, active)
                VALUES 
                (:sku, :nombre, :descripcion, :category_id, :supplier_id, :costo_unitario, :precio_venta, :stock_minimo, :stock_actual, :unidad_medida, 1)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    // Actualizar producto existente
    public function update($id, $data) {
        $sql = 'UPDATE products SET
                    sku = :sku,
                    nombre = :nombre,
                    descripcion = :descripcion,
                    category_id = :category_id,
                    supplier_id = :supplier_id,
                    costo_unitario = :costo_unitario,
                    precio_venta = :precio_venta,
                    stock_minimo = :stock_minimo,
                    stock_actual = :stock_actual,
                    unidad_medida = :unidad_medida
                WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $data[':id'] = $id;
        return $stmt->execute($data);
    }

    // Desactivar (eliminar lógico) producto
    public function deactivate($id) {
        $stmt = $this->pdo->prepare('UPDATE products SET active = 0 WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // (Opcional) Reactivar producto si lo necesitas en el futuro
    public function activate($id) {
        $stmt = $this->pdo->prepare('UPDATE products SET active = 1 WHERE id = ?');
        return $stmt->execute([$id]);
    }
}