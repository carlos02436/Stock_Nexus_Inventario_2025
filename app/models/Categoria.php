<?php
class Categoria {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categorias WHERE id_categoria = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    public function listarActivas() {
        try {
            $stmt = $this->db->query("
                SELECT * FROM categorias 
                WHERE estado = 'Activo' 
                ORDER BY nombre_categoria
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarActivas: " . $e->getMessage());
            return [];
        }
    }

    // función contarProductos
    public function contarProductos($id_categoria) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM productos 
                WHERE id_categoria = :id
            ");
            $stmt->bindParam(':id', $id_categoria);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error en contarProductos: " . $e->getMessage());
            return 0;
        }
    }

    public function obtenerConEstadisticas() {
        try {
            $stmt = $this->db->query("
                SELECT c.*, COUNT(p.id_producto) as total_productos
                FROM categorias c
                LEFT JOIN productos p ON c.id_categoria = p.id_categoria AND p.estado = 'Activo'
                WHERE c.estado = 'Activo'
                GROUP BY c.id_categoria
                ORDER BY c.nombre_categoria
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerConEstadisticas: " . $e->getMessage());
            return [];
        }
    }
}
?>