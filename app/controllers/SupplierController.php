<?php
namespace App\Controllers;
use App\Models\Supplier;

class SupplierController {
    protected $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function index() {
        $model = new Supplier($this->pdo);
        $suppliers = $model->all();
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/suppliers/index.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function create() {
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/suppliers/create.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        $data = ['nombre'=>$_POST['nombre'],'contacto'=>$_POST['contacto'],'telefono'=>$_POST['telefono'],'email'=>$_POST['email']];
        $model = new Supplier($this->pdo);
        $model->create($data);
        header('Location: /public/index.php?module=suppliers&action=index');
    }
}
