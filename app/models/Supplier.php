<?php
namespace App\Models;
use PDO;

class Supplier extends Model {
    public function all() {
        $stmt = $this->pdo->query('SELECT * FROM suppliers WHERE active = 1');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $stmt = $this->pdo->prepare('INSERT INTO suppliers (nombre,contacto,telefono,email) VALUES (?,?,?,?)');
        return $stmt->execute([$data['nombre'],$data['contacto'],$data['telefono'],$data['email']]);
    }
}
