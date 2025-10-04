<?php
namespace App\Models;
use PDO;

class Role extends Model {
    public function all() {
        $stmt = $this->pdo->query('SELECT * FROM roles');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
