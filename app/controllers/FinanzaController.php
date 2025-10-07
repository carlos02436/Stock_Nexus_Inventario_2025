<?php
class FinanzaController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function listarPagos() {
        try {
            $stmt = $this->db->query("
                SELECT p.*, u.nombre_completo as usuario_nombre
                FROM pagos p
                LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                ORDER BY p.fecha_pago DESC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarPagos: " . $e->getMessage());
            return [];
        }
    }

    public function crearPago($datos) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO pagos 
                (tipo_pago, referencia, descripcion, monto, metodo_pago, id_usuario) 
                VALUES 
                (:tipo, :referencia, :descripcion, :monto, :metodo, :usuario)
            ");
            
            return $stmt->execute([
                ':tipo' => $datos['tipo_pago'],
                ':referencia' => $datos['referencia'],
                ':descripcion' => $datos['descripcion'],
                ':monto' => $datos['monto'],
                ':metodo' => $datos['metodo_pago'],
                ':usuario' => $datos['id_usuario']
            ]);
        } catch (PDOException $e) {
            error_log("Error en crearPago: " . $e->getMessage());
            return false;
        }
    }

    public function getBalanceMensual($mes, $año) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COALESCE(SUM(CASE WHEN tipo_pago = 'Ingreso' THEN monto ELSE 0 END), 0) as total_ingresos,
                    COALESCE(SUM(CASE WHEN tipo_pago = 'Egreso' THEN monto ELSE 0 END), 0) as total_egresos,
                    COALESCE(SUM(CASE WHEN tipo_pago = 'Ingreso' THEN monto ELSE -monto END), 0) as utilidad
                FROM pagos 
                WHERE MONTH(fecha_pago) = :mes AND YEAR(fecha_pago) = :año
            ");
            $stmt->execute([':mes' => $mes, ':año' => $año]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en getBalanceMensual: " . $e->getMessage());
            return ['total_ingresos' => 0, 'total_egresos' => 0, 'utilidad' => 0];
        }
    }

    public function getIngresosVsEgresos($periodo = 6) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    YEAR(fecha_pago) as año,
                    MONTH(fecha_pago) as mes,
                    SUM(CASE WHEN tipo_pago = 'Ingreso' THEN monto ELSE 0 END) as ingresos,
                    SUM(CASE WHEN tipo_pago = 'Egreso' THEN monto ELSE 0 END) as egresos
                FROM pagos 
                WHERE fecha_pago >= DATE_SUB(CURRENT_DATE, INTERVAL :periodo MONTH)
                GROUP BY YEAR(fecha_pago), MONTH(fecha_pago)
                ORDER BY año, mes
            ");
            $stmt->bindValue(':periodo', $periodo, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en getIngresosVsEgresos: " . $e->getMessage());
            return [];
        }
    }

    public function getResumenFinanciero() {
        try {
            // Ingresos del mes actual
            $stmt = $this->db->query("
                SELECT COALESCE(SUM(monto), 0) as total 
                FROM pagos 
                WHERE tipo_pago = 'Ingreso' AND MONTH(fecha_pago) = MONTH(CURRENT_DATE())
            ");
            $ingresos_mes = $stmt->fetch()['total'];

            // Egresos del mes actual
            $stmt = $this->db->query("
                SELECT COALESCE(SUM(monto), 0) as total 
                FROM pagos 
                WHERE tipo_pago = 'Egreso' AND MONTH(fecha_pago) = MONTH(CURRENT_DATE())
            ");
            $egresos_mes = $stmt->fetch()['total'];

            // Utilidad del mes
            $utilidad_mes = $ingresos_mes - $egresos_mes;

            return [
                'ingresos_mes' => $ingresos_mes,
                'egresos_mes' => $egresos_mes,
                'utilidad_mes' => $utilidad_mes
            ];

        } catch (PDOException $e) {
            error_log("Error en getResumenFinanciero: " . $e->getMessage());
            return ['ingresos_mes' => 0, 'egresos_mes' => 0, 'utilidad_mes' => 0];
        }
    }
}
?>