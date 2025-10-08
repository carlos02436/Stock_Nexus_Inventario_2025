<?php
class Dashboard {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerEstadisticasGenerales($fechaInicio = null, $fechaFin = null) {
        try {
            $stats = [];

            // Construir WHERE para fechas
            $whereFecha = "";
            if ($fechaInicio && $fechaFin) {
                $whereFecha = "WHERE fecha_venta BETWEEN '$fechaInicio 00:00:00' AND '$fechaFin 23:59:59'";
            } else {
                // Por defecto: mes actual
                $whereFecha = "WHERE MONTH(fecha_venta) = MONTH(CURRENT_DATE()) AND YEAR(fecha_venta) = YEAR(CURRENT_DATE())";
            }

            // Total de ventas y monto
            $sqlVentas = "
                SELECT 
                    COUNT(*) as cantidad_ventas,
                    COALESCE(SUM(total_venta), 0) as ingresos_ventas
                FROM ventas 
                $whereFecha
            ";
            
            $stmt = $this->db->query($sqlVentas);
            $ventas = $stmt->fetch();
            $stats['cantidad_ventas'] = $ventas['cantidad_ventas'];
            $stats['ingresos_ventas_mes'] = $ventas['ingresos_ventas'];

            // Ventas pagadas
            $sqlPagadas = "
                SELECT 
                    COUNT(*) as cantidad_pagadas,
                    COALESCE(SUM(total_venta), 0) as ventas_pagadas
                FROM ventas 
                $whereFecha AND estado = 'Pagada'
            ";
            
            $stmt = $this->db->query($sqlPagadas);
            $pagadas = $stmt->fetch();
            $stats['cantidad_pagadas'] = $pagadas['cantidad_pagadas'];
            $stats['ventas_pagadas'] = $pagadas['ventas_pagadas'];

            // Otras estadísticas generales
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM productos WHERE estado = 'Activo'");
            $stats['total_productos'] = $stmt->fetch()['total'];

            $stmt = $this->db->query("SELECT COUNT(*) as total FROM clientes");
            $stats['total_clientes'] = $stmt->fetch()['total'];

            $stmt = $this->db->query("
                SELECT COALESCE(SUM(stock_actual * precio_compra), 0) as valor_total 
                FROM productos 
                WHERE estado = 'Activo'
            ");
            $stats['valor_inventario'] = $stmt->fetch()['valor_total'];

            return $stats;

        } catch (PDOException $e) {
            error_log("Error en obtenerEstadisticasGenerales: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerVentasUltimosMeses($meses = 6) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    YEAR(fecha_venta) as año,
                    MONTH(fecha_venta) as mes,
                    COUNT(*) as total_ventas,
                    COALESCE(SUM(total_venta), 0) as ingresos
                FROM ventas 
                WHERE fecha_venta >= DATE_SUB(CURRENT_DATE(), INTERVAL :meses MONTH)
                    AND estado = 'Pagada'
                GROUP BY YEAR(fecha_venta), MONTH(fecha_venta)
                ORDER BY año, mes
            ");
            $stmt->bindValue(':meses', $meses, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerVentasUltimosMeses: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerProductosMasVendidos($limite = 5, $fechaInicio = null, $fechaFin = null) {
        try {
            $where = "WHERE v.estado = 'Pagada'";
            if ($fechaInicio && $fechaFin) {
                $where = "WHERE v.fecha_venta BETWEEN '$fechaInicio 00:00:00' AND '$fechaFin 23:59:59' AND v.estado = 'Pagada'";
            }

            $sql = "
                SELECT 
                    p.codigo_producto,
                    p.nombre_producto,
                    SUM(dv.cantidad) as total_vendido,
                    SUM(dv.subtotal) as ingresos_generados
                FROM detalle_ventas dv
                JOIN productos p ON dv.id_producto = p.id_producto
                JOIN ventas v ON dv.id_venta = v.id_venta
                $where
                GROUP BY p.id_producto, p.codigo_producto, p.nombre_producto
                ORDER BY total_vendido DESC
                LIMIT :limite
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerProductosMasVendidos: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerAlertasStock() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    codigo_producto,
                    nombre_producto,
                    stock_actual,
                    stock_minimo,
                    (stock_actual - stock_minimo) as diferencia
                FROM productos 
                WHERE stock_actual <= stock_minimo AND estado = 'Activo'
                ORDER BY diferencia ASC
                LIMIT 10
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerAlertasStock: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerVentasRecientes($limite = 5, $fechaInicio = null, $fechaFin = null) {
        try {
            $where = "";
            if ($fechaInicio && $fechaFin) {
                $where = "WHERE v.fecha_venta BETWEEN '$fechaInicio 00:00:00' AND '$fechaFin 23:59:59'";
            }

            $sql = "
                SELECT 
                    v.codigo_venta,
                    c.nombre_cliente,
                    v.total_venta,
                    v.fecha_venta,
                    v.estado,
                    v.metodo_pago
                FROM ventas v
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                $where
                ORDER BY v.fecha_venta DESC
                LIMIT :limite
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerVentasRecientes: " . $e->getMessage());
            return [];
        }
    }

    // MÉTODO PARA VERIFICAR VENTAS HOY - CORREGIDO
    public function debugVentasPeriodo($fechaInicio, $fechaFin) {
        try {
            $sql = "
                SELECT 
                    COUNT(*) as total_ventas,
                    COALESCE(SUM(total_venta), 0) as ingresos_ventas,
                    MIN(fecha_venta) as primera_venta,
                    MAX(fecha_venta) as ultima_venta
                FROM ventas 
                WHERE fecha_venta BETWEEN '$fechaInicio 00:00:00' AND '$fechaFin 23:59:59' 
                AND estado = 'Pagada'
            ";
            
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch();
            
            // Verificar TODAS las ventas del período (incluyendo no pagadas)
            $sqlVentas = "
                SELECT codigo_venta, fecha_venta, total_venta, estado
                FROM ventas 
                WHERE fecha_venta BETWEEN '$fechaInicio 00:00:00' AND '$fechaFin 23:59:59'
                ORDER BY fecha_venta DESC
            ";
            
            $stmtVentas = $this->db->query($sqlVentas);
            $ventasDetalle = $stmtVentas->fetchAll();
            
            return [
                'resumen' => $result,
                'ventas' => $ventasDetalle
            ];
            
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
?>