<?php
namespace App\Models;
use PDO;

class Category extends Model {
    public function all() {
        $stmt = $this->pdo->query('SELECT * FROM categories WHERE active = 1');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $stmt = $this->pdo->prepare('INSERT INTO categories (nombre,descripcion) VALUES (?,?)');
        return $stmt->execute([$data['nombre'],$data['descripcion']]);
    }
}
