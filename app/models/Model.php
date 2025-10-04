<?php
namespace App\Models;
class Model {
    protected $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
}
