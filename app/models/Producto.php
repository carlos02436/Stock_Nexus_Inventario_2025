<?php
class Producto {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.nombre_categoria 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                WHERE p.id_producto = :id
            ");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorCodigo($codigo) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM productos WHERE codigo_producto = :codigo");
            $stmt->bindParam(':codigo', $codigo);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerPorCodigo: " . $e->getMessage());
            return false;
        }
    }

    public function listarActivos() {
        try {
            $stmt = $this->db->query("
                SELECT p.*, c.nombre_categoria 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                WHERE p.estado = 'Activo' 
                ORDER BY p.nombre_producto
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarActivos: " . $e->getMessage());
            return [];
        }
    }

    public function listarConStockBajo() {
        try {
            $stmt = $this->db->query("
                SELECT * FROM productos 
                WHERE stock_actual <= stock_minimo AND estado = 'Activo'
                ORDER BY (stock_actual - stock_minimo) ASC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarConStockBajo: " . $e->getMessage());
            return [];
        }
    }

    public function actualizarStock($id, $cantidad) {
        try {
            $stmt = $this->db->prepare("
                UPDATE productos 
                SET stock_actual = stock_actual + :cantidad 
                WHERE id_producto = :id
            ");
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarStock: " . $e->getMessage());
            return false;
        }
    }

    public function buscar($termino) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM productos 
                WHERE (nombre_producto LIKE :termino OR codigo_producto LIKE :termino) 
                AND estado = 'Activo'
                ORDER BY nombre_producto
            ");
            $termino = "%$termino%";
            $stmt->bindParam(':termino', $termino);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en buscar: " . $e->getMessage());
            return [];
        }
    }
}
?>