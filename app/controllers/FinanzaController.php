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

    public function getResumenFinanciero() {
        try {
            // Ingresos del mes actual (ventas pagadas)
            $stmt = $this->db->query("
                SELECT COALESCE(SUM(total_venta), 0) as total 
                FROM ventas 
                WHERE estado = 'Pagada' AND MONTH(fecha_venta) = MONTH(CURRENT_DATE()) AND YEAR(fecha_venta) = YEAR(CURRENT_DATE())
            ");
            $ingresos_mes = $stmt->fetch()['total'];

            // Egresos del mes actual (compras pagadas)
            $stmt = $this->db->query("
                SELECT COALESCE(SUM(total_compra), 0) as total 
                FROM compras 
                WHERE estado = 'Pagada' AND MONTH(fecha_compra) = MONTH(CURRENT_DATE()) AND YEAR(fecha_compra) = YEAR(CURRENT_DATE())
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

    public function getIngresosVsEgresos($periodo = 6) {
        try {
            // Obtener ingresos por mes (ventas pagadas)
            $stmt = $this->db->prepare("
                SELECT 
                    YEAR(fecha_venta) as año,
                    MONTH(fecha_venta) as mes,
                    COALESCE(SUM(total_venta), 0) as ingresos
                FROM ventas 
                WHERE estado = 'Pagada' AND fecha_venta >= DATE_SUB(CURRENT_DATE, INTERVAL :periodo MONTH)
                GROUP BY YEAR(fecha_venta), MONTH(fecha_venta)
                ORDER BY año, mes
            ");
            $stmt->bindValue(':periodo', $periodo, PDO::PARAM_INT);
            $stmt->execute();
            $ingresos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obtener egresos por mes (compras pagadas)
            $stmt = $this->db->prepare("
                SELECT 
                    YEAR(fecha_compra) as año,
                    MONTH(fecha_compra) as mes,
                    COALESCE(SUM(total_compra), 0) as egresos
                FROM compras 
                WHERE estado = 'Pagada' AND fecha_compra >= DATE_SUB(CURRENT_DATE, INTERVAL :periodo MONTH)
                GROUP BY YEAR(fecha_compra), MONTH(fecha_compra)
                ORDER BY año, mes
            ");
            $stmt->bindValue(':periodo', $periodo, PDO::PARAM_INT);
            $stmt->execute();
            $egresos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Combinar los resultados
            $datos = [];
            foreach ($ingresos as $ingreso) {
                $key = $ingreso['año'] . '-' . $ingreso['mes'];
                $datos[$key] = [
                    'año' => $ingreso['año'],
                    'mes' => $ingreso['mes'],
                    'ingresos' => $ingreso['ingresos'],
                    'egresos' => 0
                ];
            }

            foreach ($egresos as $egreso) {
                $key = $egreso['año'] . '-' . $egreso['mes'];
                if (isset($datos[$key])) {
                    $datos[$key]['egresos'] = $egreso['egresos'];
                } else {
                    $datos[$key] = [
                        'año' => $egreso['año'],
                        'mes' => $egreso['mes'],
                        'ingresos' => 0,
                        'egresos' => $egreso['egresos']
                    ];
                }
            }

            // Ordenar por año y mes
            usort($datos, function($a, $b) {
                if ($a['año'] == $b['año']) {
                    return $a['mes'] - $b['mes'];
                }
                return $a['año'] - $b['año'];
            });

            return $datos;

        } catch (PDOException $e) {
            error_log("Error en getIngresosVsEgresos: " . $e->getMessage());
            return [];
        }
    }

    // Método para obtener la distribución de métodos de pago en el mes actual
    public function getMetodosPagoMes() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    metodo_pago,
                    COUNT(*) as cantidad,
                    SUM(total_venta) as total
                FROM ventas 
                WHERE estado = 'Pagada' AND MONTH(fecha_venta) = MONTH(CURRENT_DATE()) AND YEAR(fecha_venta) = YEAR(CURRENT_DATE())
                GROUP BY metodo_pago
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getMetodosPagoMes: " . $e->getMessage());
            return [];
        }
    }
}
?>