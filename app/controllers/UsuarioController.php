<?php
class UsuarioController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function login($usuario, $contrasena) {
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
            error_log("Error en login: " . $e->getMessage());
            return false;
        }
    }

    public function listar() {
        try {
            $stmt = $this->db->query("SELECT * FROM usuarios ORDER BY fecha_creacion DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listar usuarios: " . $e->getMessage());
            return [];
        }
    }

    public function obtener($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id_usuario = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtener usuario: " . $e->getMessage());
            return false;
        }
    }

    public function crear($datos) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO usuarios (nombre_completo, correo, usuario, contrasena, rol, estado) 
                VALUES (:nombre, :correo, :usuario, :contrasena, :rol, :estado)
            ");
            
            return $stmt->execute([
                ':nombre' => $datos['nombre_completo'],
                ':correo' => $datos['correo'],
                ':usuario' => $datos['usuario'],
                ':contrasena' => $datos['contrasena'],
                ':rol' => $datos['rol'],
                ':estado' => $datos['estado'] ?? 'Activo'
            ]);
        } catch (PDOException $e) {
            error_log("Error en crear usuario: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar($id, $datos) {
        try {
            $stmt = $this->db->prepare("
                UPDATE usuarios 
                SET nombre_completo = :nombre, correo = :correo, usuario = :usuario, 
                    rol = :rol, estado = :estado 
                WHERE id_usuario = :id
            ");
            
            return $stmt->execute([
                ':nombre' => $datos['nombre_completo'],
                ':correo' => $datos['correo'],
                ':usuario' => $datos['usuario'],
                ':rol' => $datos['rol'],
                ':estado' => $datos['estado'],
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Error en actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id_usuario = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminar usuario: " . $e->getMessage());
            return false;
        }
    }
}
?>