<?php
namespace App\Controllers;
class HomeController {
    protected $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    public function index() {
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/dashboard.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }
}
