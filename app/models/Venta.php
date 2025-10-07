<?php
class Venta {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT v.*, c.nombre_cliente, u.nombre_completo as usuario_nombre
                FROM ventas v
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
                WHERE v.id_venta = :id
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
            $stmt = $this->db->prepare("SELECT * FROM ventas WHERE codigo_venta = :codigo");
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
                SELECT v.*, c.nombre_cliente 
                FROM ventas v
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                ORDER BY v.fecha_venta DESC 
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
                SELECT v.*, c.nombre_cliente 
                FROM ventas v
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                WHERE v.estado = :estado
                ORDER BY v.fecha_venta DESC
            ");
            $stmt->bindParam(':estado', $estado);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarPorEstado: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerDetalle($id_venta) {
        try {
            $stmt = $this->db->prepare("
                SELECT dv.*, p.codigo_producto, p.nombre_producto
                FROM detalle_ventas dv
                JOIN productos p ON dv.id_producto = p.id_producto
                WHERE dv.id_venta = :id_venta
            ");
            $stmt->bindParam(':id_venta', $id_venta);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerDetalle: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerVentasMensuales($mes, $año) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_ventas,
                    SUM(total_venta) as monto_total,
                    AVG(total_venta) as promedio_venta
                FROM ventas 
                WHERE MONTH(fecha_venta) = :mes 
                AND YEAR(fecha_venta) = :año
                AND estado = 'Pagada'
            ");
            $stmt->bindParam(':mes', $mes);
            $stmt->bindParam(':año', $año);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerVentasMensuales: " . $e->getMessage());
            return ['total_ventas' => 0, 'monto_total' => 0, 'promedio_venta' => 0];
        }
    }

    public function obtenerVentasPorMetodoPago($fecha_inicio, $fecha_fin) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    metodo_pago,
                    COUNT(*) as total_ventas,
                    SUM(total_venta) as monto_total
                FROM ventas 
                WHERE fecha_venta BETWEEN :fecha_inicio AND :fecha_fin
                AND estado = 'Pagada'
                GROUP BY metodo_pago
            ");
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerVentasPorMetodoPago: " . $e->getMessage());
            return [];
        }
    }
}
?>