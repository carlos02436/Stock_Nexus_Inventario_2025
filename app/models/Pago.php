<?php
class Pago {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, u.nombre_completo as usuario_nombre
                FROM pagos p
                LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.id_pago = :id
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
                SELECT p.*, u.nombre_completo as usuario_nombre
                FROM pagos p
                LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                ORDER BY p.fecha_pago DESC 
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

    public function listarPorTipo($tipo, $limite = 20) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, u.nombre_completo as usuario_nombre
                FROM pagos p
                LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.tipo_pago = :tipo
                ORDER BY p.fecha_pago DESC 
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

    public function obtenerResumenMensual($mes, $año) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    tipo_pago,
                    COUNT(*) as total_pagos,
                    SUM(monto) as monto_total,
                    AVG(monto) as promedio_pago
                FROM pagos 
                WHERE MONTH(fecha_pago) = :mes AND YEAR(fecha_pago) = :año
                GROUP BY tipo_pago
            ");
            $stmt->bindParam(':mes', $mes);
            $stmt->bindParam(':año', $año);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerResumenMensual: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerFlujoCaja($fecha_inicio, $fecha_fin) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    DATE(fecha_pago) as fecha,
                    SUM(CASE WHEN tipo_pago = 'Ingreso' THEN monto ELSE 0 END) as ingresos,
                    SUM(CASE WHEN tipo_pago = 'Egreso' THEN monto ELSE 0 END) as egresos,
                    SUM(CASE WHEN tipo_pago = 'Ingreso' THEN monto ELSE -monto END) as neto
                FROM pagos 
                WHERE fecha_pago BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY DATE(fecha_pago)
                ORDER BY fecha
            ");
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerFlujoCaja: " . $e->getMessage());
            return [];
        }
    }
}
?>