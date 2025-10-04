<?php
namespace App\Controllers;
use App\Models\Role;

class RoleController {
    protected $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function index() {
        $model = new Role($this->pdo);
        $roles = $model->all();
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/roles/index.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }
}
