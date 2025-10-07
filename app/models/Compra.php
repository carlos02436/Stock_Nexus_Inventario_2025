<?php
class Compra {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, p.nombre_proveedor, u.nombre_completo as usuario_nombre
                FROM compras c
                LEFT JOIN proveedores p ON c.id_proveedor = p.id_proveedor
                LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
                WHERE c.id_compra = :id
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
            $stmt = $this->db->prepare("SELECT * FROM compras WHERE codigo_compra = :codigo");
            $stmt->bindParam(':codigo', $codigo);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerPorCodigo: " . $e->getMessage());
            return false;
        }
    }

    public function listarRecientes($limite = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, p.nombre_proveedor 
                FROM compras c
                LEFT JOIN proveedores p ON c.id_proveedor = p.id_proveedor
                ORDER BY c.fecha_compra DESC 
                LIMIT :limite
            ");
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarRecientes: " . $e->getMessage());
            return [];
        }
    }

    public function listarPorEstado($estado) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, p.nombre_proveedor 
                FROM compras c
                LEFT JOIN proveedores p ON c.id_proveedor = p.id_proveedor
                WHERE c.estado = :estado
                ORDER BY c.fecha_compra DESC
            ");
            $stmt->bindParam(':estado', $estado);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarPorEstado: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerDetalle($id_compra) {
        try {
            $stmt = $this->db->prepare("
                SELECT dc.*, p.codigo_producto, p.nombre_producto
                FROM detalle_compras dc
                JOIN productos p ON dc.id_producto = p.id_producto
                WHERE dc.id_compra = :id_compra
            ");
            $stmt->bindParam(':id_compra', $id_compra);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerDetalle: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerComprasMensuales($mes, $año) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_compras,
                    SUM(total_compra) as monto_total,
                    AVG(total_compra) as promedio_compra
                FROM compras 
                WHERE MONTH(fecha_compra) = :mes 
                AND YEAR(fecha_compra) = :año
                AND estado = 'Pagada'
            ");
            $stmt->bindParam(':mes', $mes);
            $stmt->bindParam(':año', $año);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerComprasMensuales: " . $e->getMessage());
            return ['total_compras' => 0, 'monto_total' => 0, 'promedio_compra' => 0];
        }
    }
}
?>