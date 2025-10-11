<?php
class BalanceGeneral {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerPorFecha($fecha) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM balance_general WHERE fecha_balance = :fecha");
            $stmt->bindParam(':fecha', $fecha);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerPorFecha: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerBalanceActual() {
        try {
            $stmt = $this->db->query("
                SELECT * FROM balance_general 
                ORDER BY fecha_balance DESC 
                LIMIT 1
            ");
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerBalanceActual: " . $e->getMessage());
            return false;
        }
    }

    public function listarBalances($limite = 12) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM balance_general 
                ORDER BY fecha_balance DESC 
                LIMIT :limite
            ");
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarBalances: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerBalancesMensuales($año) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM balance_general 
                WHERE YEAR(fecha_balance) = :año
                ORDER BY fecha_balance
            ");
            $stmt->bindParam(':año', $año);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerBalancesMensuales: " . $e->getMessage());
            return [];
        }
    }

    public function calcularBalanceDelDia() {
        try {
            // Calcular ingresos del día
            $stmt = $this->db->query("
                SELECT COALESCE(SUM(total_venta), 0) as ingresos_ventas
                FROM ventas 
                WHERE DATE(fecha_venta) = CURDATE() AND estado = 'Pagada'
            ");
            $ingresos_ventas = $stmt->fetch()['ingresos_ventas'];

            // Calcular egresos del día (compras)
            $stmt = $this->db->query("
                SELECT COALESCE(SUM(total_compra), 0) as egresos_compras
                FROM compras 
                WHERE DATE(fecha_compra) = CURDATE() AND estado = 'Pagada'
            ");
            $egresos_compras = $stmt->fetch()['egresos_compras'];

            // Calcular otros pagos del día
            $stmt = $this->db->query("
                SELECT 
                    COALESCE(SUM(CASE WHEN tipo_pago = 'Ingreso' THEN monto ELSE 0 END), 0) as otros_ingresos,
                    COALESCE(SUM(CASE WHEN tipo_pago = 'Egreso' THEN monto ELSE 0 END), 0) as otros_egresos
                FROM pagos 
                WHERE DATE(fecha_pago) = CURDATE()
            ");
            $otros = $stmt->fetch();

            $total_ingresos = $ingresos_ventas + $otros['otros_ingresos'];
            $total_egresos = $egresos_compras + $otros['otros_egresos'];
            $utilidad = $total_ingresos - $total_egresos;

            return [
                'fecha_balance' => date('Y-m-d'),
                'total_ingresos' => $total_ingresos,
                'total_egresos' => $total_egresos,
                'utilidad' => $utilidad
            ];

        } catch (PDOException $e) {
            error_log("Error en calcularBalanceDelDia: " . $e->getMessage());
            return ['total_ingresos' => 0, 'total_egresos' => 0, 'utilidad' => 0];
        }
    }

    public function obtenerAniosDisponibles() {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT YEAR(fecha_balance) as anio 
                FROM balance_general 
                ORDER BY anio DESC
            ");
            $stmt->execute();
            $anios = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $anios ?: [date('Y')];
        } catch (PDOException $e) {
            error_log("Error en obtenerAniosDisponibles: " . $e->getMessage());
            return [date('Y')];
        }
    }

    // Total de ingresos y egresos y utilidad neta
    public function obtenerTotales() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    COALESCE(SUM(total_ingresos), 0) as total_ingresos,
                    COALESCE(SUM(total_egresos), 0) as total_egresos,
                    COALESCE(SUM(utilidad), 0) as utilidad_neta
                FROM balance_general
            ");
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerTotales: " . $e->getMessage());
            return ['total_ingresos' => 0, 'total_egresos' => 0, 'utilidad_neta' => 0];
        }
    }
}
?>