<?php
namespace App\Models;
use PDO;

class Movement extends Model {
    public function create($data) {
        $sql = 'INSERT INTO movements (product_id,type,quantity,unit_cost,total_cost,note,created_by) VALUES (?,?,?,?,?,?,?)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$data['product_id'],$data['type'],$data['quantity'],$data['unit_cost'],$data['total_cost'],$data['note'],$data['created_by']]);
    }
}
