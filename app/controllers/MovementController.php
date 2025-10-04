<?php
namespace App\Controllers;
use App\Models\Movement;
use App\Models\Product;

class MovementController {
    protected $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function create() {
        $productModel = new Product($this->pdo);
        $products = $productModel->all();
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/movements/create.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        $data = [
            'product_id' => $_POST['product_id'],
            'type' => $_POST['type'],
            'quantity' => intval($_POST['quantity']),
            'unit_cost' => floatval($_POST['unit_cost']),
            'total_cost' => floatval($_POST['unit_cost']) * intval($_POST['quantity']),
            'note' => $_POST['note'],
            'created_by' => $_SESSION['user']['id'] ?? 1
        ];
        $mov = new Movement($this->pdo);
        $mov->create($data);
        header('Location: /public/index.php?module=products&action=index');
    }
}
