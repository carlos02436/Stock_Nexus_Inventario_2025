<?php
namespace App\Models;
use PDO;

class Product extends Model {
    public function all() {
        $sql = 'SELECT p.*, c.nombre as category_name, s.nombre as supplier_name FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                WHERE p.active = 1';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function find($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $sql = 'INSERT INTO products (sku,nombre,descripcion,category_id,supplier_id,costo_unitario,precio_venta,stock_minimo,stock_actual,unidad_medida,active)
                VALUES (:sku,:nombre,:descripcion,:category_id,:supplier_id,:costo_unitario,:precio_venta,:stock_minimo,:stock_actual,:unidad_medida,1)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }
}
