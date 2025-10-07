<?php
class MovimientoBodega {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT m.*, p.codigo_producto, p.nombre_producto, u.nombre_completo as usuario_nombre
                FROM movimientos_bodega m
                JOIN productos p ON m.id_producto = p.id_producto
                LEFT JOIN usuarios u ON m.id_usuario = u.id_usuario
                WHERE m.id_movimiento = :id
            ");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    public function listarRecientes($limite = 20) {
        try {
            $stmt = $this->db->prepare("
                SELECT m.*, p.codigo_producto, p.nombre_producto, u.nombre_completo as usuario_nombre
                FROM movimientos_bodega m
                JOIN productos p ON m.id_producto = p.id_producto
                LEFT JOIN usuarios u ON m.id_usuario = u.id_usuario
                ORDER BY m.fecha_movimiento DESC 
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

    public function listarPorProducto($id_producto, $limite = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT m.*, u.nombre_completo as usuario_nombre
                FROM movimientos_bodega m
                LEFT JOIN usuarios u ON m.id_usuario = u.id_usuario
                WHERE m.id_producto = :id_producto
                ORDER BY m.fecha_movimiento DESC 
                LIMIT :limite
            ");
            $stmt->bindParam(':id_producto', $id_producto);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarPorProducto: " . $e->getMessage());
            return [];
        }
    }

    public function listarPorTipo($tipo, $limite = 20) {
        try {
            $stmt = $this->db->prepare("
                SELECT m.*, p.codigo_producto, p.nombre_producto, u.nombre_completo as usuario_nombre
                FROM movimientos_bodega m
                JOIN productos p ON m.id_producto = p.id_producto
                LEFT JOIN usuarios u ON m.id_usuario = u.id_usuario
                WHERE m.tipo_movimiento = :tipo
                ORDER BY m.fecha_movimiento DESC 
                LIMIT :limite
            ");
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarPorTipo: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerMovimientosPeriodo($fecha_inicio, $fecha_fin) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    m.tipo_movimiento,
                    COUNT(*) as total_movimientos,
                    SUM(m.cantidad) as cantidad_total
                FROM movimientos_bodega m
                WHERE m.fecha_movimiento BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY m.tipo_movimiento
            ");
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerMovimientosPeriodo: " . $e->getMessage());
            return [];
        }
    }
}
?>