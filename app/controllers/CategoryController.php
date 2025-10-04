<?php
namespace App\Controllers;
use App\Models\Category;

class CategoryController {
    protected $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function index() {
        $model = new Category($this->pdo);
        $categories = $model->all();
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/categories/index.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function create() {
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/categories/create.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        $data = ['nombre'=>$_POST['nombre'],'descripcion'=>$_POST['descripcion']];
        $model = new Category($this->pdo);
        $model->create($data);
        header('Location: /public/index.php?module=categories&action=index');
    }
}
