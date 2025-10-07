<?php
class Proveedor {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM proveedores WHERE id_proveedor = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorNit($nit) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM proveedores WHERE nit = :nit");
            $stmt->bindParam(':nit', $nit);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerPorNit: " . $e->getMessage());
            return false;
        }
    }

    public function listarTodos() {
        try {
            $stmt = $this->db->query("SELECT * FROM proveedores ORDER BY nombre_proveedor");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarTodos: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerCompras($id_proveedor, $limite = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM compras 
                WHERE id_proveedor = :id 
                ORDER BY fecha_compra DESC 
                LIMIT :limite
            ");
            $stmt->bindParam(':id', $id_proveedor);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerCompras: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerEstadisticasCompras($id_proveedor) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_compras,
                    SUM(total_compra) as monto_total,
                    AVG(total_compra) as promedio_compra
                FROM compras 
                WHERE id_proveedor = :id AND estado = 'Pagada'
            ");
            $stmt->bindParam(':id', $id_proveedor);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerEstadisticasCompras: " . $e->getMessage());
            return ['total_compras' => 0, 'monto_total' => 0, 'promedio_compra' => 0];
        }
    }
}
?>