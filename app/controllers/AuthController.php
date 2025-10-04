<?php
namespace App\Controllers;
use App\Models\User;

class AuthController {
    protected $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function login() {
        require __DIR__ . '/../views/auth/login.php';
    }

    public function doLogin() {
        $username = $_POST['usuario'] ?? '';
        $password = $_POST['password'] ?? '';
        $userModel = new User($this->pdo);
        $user = $userModel->findByUsername($username);
        if ($user && password_verify($password, $user['password_hash'] ?? $user['password'])) {
            session_start();
            // normalize session user
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nombre' => $user['nombre'] ?? $user['usuario'],
                'email' => $user['email'] ?? null,
                'usuario' => $user['usuario'] ?? ($user['email'] ?? null),
                'role' => $user['role'] ?? $user['rol'] ?? 'operador'
            ];
            header('Location: /public/index.php');
            exit();
        } else {
            $error = 'Credenciales inválidas';
            require __DIR__ . '/../views/auth/login.php';
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: /login.php');
        exit();
    }
}
