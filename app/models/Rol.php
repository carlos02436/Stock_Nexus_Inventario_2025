<?php
class Rol {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Listar todos los roles
    public function listar() {
        $stmt = $this->db->prepare("SELECT * FROM roles ORDER BY nombre_rol ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un rol por su ID
    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM roles WHERE id_rol = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo rol
    public function crear($nombre_rol, $estado) {
        $stmt = $this->db->prepare("INSERT INTO roles (nombre_rol, estado) VALUES (?, ?)");
        return $stmt->execute([$nombre_rol, $estado]);
    }

    // Editar un rol existente
    public function editar($id_rol, $nombre_rol, $estado) {
        $stmt = $this->db->prepare("UPDATE roles SET nombre_rol = ?, estado = ?, fecha_actualizacion = NOW() WHERE id_rol = ?");
        return $stmt->execute([$nombre_rol, $estado, $id_rol]);
    }

    // Cambiar estado (activar/inactivar)
    public function cambiarEstado($id_rol, $estado) {
        $stmt = $this->db->prepare("UPDATE roles SET estado = ?, fecha_actualizacion = NOW() WHERE id_rol = ?");
        return $stmt->execute([$estado, $id_rol]);
    }

    // Verificar duplicado
    public function existeNombre($nombre_rol, $id_rol = null) {
        if ($id_rol) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM roles WHERE nombre_rol = ? AND id_rol != ?");
            $stmt->execute([$nombre_rol, $id_rol]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM roles WHERE nombre_rol = ?");
            $stmt->execute([$nombre_rol]);
        }
        return $stmt->fetchColumn() > 0;
    }
}