<?php
namespace App\Controllers;
use App\Models\User;
use App\Models\Role;

class UserController {
    protected $pdo;
    public function __construct($pdo) { 
        $this->pdo = $pdo; 
    }

    protected function requireAdmin() {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            die('Acceso denegado. Requiere rol admin.');
        }
    }

    public function index() {
        $this->requireAdmin();
        $model = new User($this->pdo);
        $users = $model->all();
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/users/index.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function create() {
        $this->requireAdmin();
        $roleModel = new Role($this->pdo);
        $roles = $roleModel->all();
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/users/create.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        $this->requireAdmin();
        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'email' => $_POST['email'] ?? '',
            'usuario' => $_POST['usuario'] ?? '',
            'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'role' => $_POST['role'] ?? 'operador'
        ];
        $model = new User($this->pdo);
        $model->create($data);
        header('Location: /public/index.php?module=users&action=index');
    }

    public function edit($id) {
        $this->requireAdmin();
        $model = new User($this->pdo);
        $user = $model->find($id);

        $roleModel = new Role($this->pdo);
        $roles = $roleModel->all();

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/users/edit.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function update($id) {
        $this->requireAdmin();
        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'email' => $_POST['email'] ?? '',
            'usuario' => $_POST['usuario'] ?? '',
            'role' => $_POST['role'] ?? 'operador'
        ];

        // Si el admin quiere cambiar la contraseña
        if (!empty($_POST['password'])) {
            $data['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        $model = new User($this->pdo);
        $model->update($id, $data);

        header('Location: /public/index.php?module=users&action=index');
    }

    public function deactivate($id) {
        $this->requireAdmin();
        $model = new User($this->pdo);
        $model->deactivate($id);
        header('Location: /public/index.php?module=users&action=index');
    }
}