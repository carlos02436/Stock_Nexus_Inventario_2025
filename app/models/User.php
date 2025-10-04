<?php
namespace App\Models;
use PDO;

class User extends Model {
    protected $table = 'users';
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ? AND active = 1 LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByUsername($username) {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE usuario = ? LIMIT 1');
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function all() {
        $stmt = $this->pdo->query('SELECT id, nombre, email, usuario, role, active FROM users');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare('INSERT INTO users (nombre,email,usuario,password_hash,role,active) VALUES (?,?,?,?,?,1)');
        return $stmt->execute([$data['nombre'],$data['email'],$data['usuario'],$data['password_hash'],$data['role']]);
    }

    public function deactivate($id) {
        $stmt = $this->pdo->prepare('UPDATE users SET active = 0 WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
