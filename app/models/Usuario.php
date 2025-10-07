<?php
class Usuario {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }

    // Métodos existentes para operaciones básicas
    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id_usuario = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorUsuario($usuario) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerPorUsuario: " . $e->getMessage());
            return false;
        }
    }

    public function listarActivos() {
        try {
            $stmt = $this->db->query("SELECT * FROM usuarios WHERE estado = 'Activo' ORDER BY nombre_completo");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarActivos: " . $e->getMessage());
            return [];
        }
    }

    public function verificarCredenciales($usuario, $contrasena) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM usuarios 
                WHERE usuario = :usuario AND contrasena = :contrasena AND estado = 'Activo'
            ");
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':contrasena', $contrasena);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en verificarCredenciales: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarUltimoAcceso($id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE usuarios SET fecha_ultimo_acceso = NOW() WHERE id_usuario = :id
            ");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarUltimoAcceso: " . $e->getMessage());
            return false;
        }
    }
}
?>