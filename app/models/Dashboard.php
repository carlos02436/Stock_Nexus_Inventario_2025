<?php
class Dashboard {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerEstadisticasGenerales() {
        try {
            $stats = [];

            // Total de productos activos
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM productos WHERE estado = 'Activo'");
            $stats['total_productos'] = $stmt->fetch()['total'];

            // Productos con stock bajo
            $stmt = $this->db->query("
                SELECT COUNT(*) as total 
                FROM productos 
                WHERE stock_actual <= stock_minimo AND estado = 'Activo'
            ");
            $stats['productos_stock_bajo'] = $stmt->fetch()['total'];

            // Ventas del mes
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total_ventas,
                    COALESCE(SUM(total_venta), 0) as ingresos_ventas
                FROM ventas 
                WHERE MONTH(fecha_venta) = MONTH(CURRENT_DATE()) 
                AND YEAR(fecha_venta) = YEAR(CURRENT_DATE())
                AND estado = 'Pagada'
            ");
            $ventas_mes = $stmt->fetch();
            $stats['ventas_mes'] = $ventas_mes['total_ventas'];
            $stats['ingresos_ventas_mes'] = $ventas_mes['ingresos_ventas'];

            // Compras del mes
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total_compras,
                    COALESCE(SUM(total_compra), 0) as egresos_compras
                FROM compras 
                WHERE MONTH(fecha_compra) = MONTH(CURRENT_DATE()) 
                AND YEAR(fecha_compra) = YEAR(CURRENT_DATE())
                AND estado = 'Pagada'
            ");
            $compras_mes = $stmt->fetch();
            $stats['compras_mes'] = $compras_mes['total_compras'];
            $stats['egresos_compras_mes'] = $compras_mes['egresos_compras'];

            // Clientes totales
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM clientes");
            $stats['total_clientes'] = $stmt->fetch()['total'];

            // Proveedores totales
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM proveedores");
            $stats['total_proveedores'] = $stmt->fetch()['total'];

            // Valor total del inventario
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
                WHERE fecha_venta >= DATE_SUB(CURRENT_DATE, INTERVAL :meses MONTH)
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

    public function obtenerProductosMasVendidos($limite = 5) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.codigo_producto,
                    p.nombre_producto,
                    SUM(dv.cantidad) as total_vendido,
                    SUM(dv.subtotal) as ingresos_generados
                FROM detalle_ventas dv
                JOIN productos p ON dv.id_producto = p.id_producto
                JOIN ventas v ON dv.id_venta = v.id_venta
                WHERE v.estado = 'Pagada'
                GROUP BY p.id_producto
                ORDER BY total_vendido DESC
                LIMIT :limite
            ");
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

    public function obtenerVentasRecientes($limite = 5) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    v.codigo_venta,
                    c.nombre_cliente,
                    v.total_venta,
                    v.fecha_venta,
                    v.estado,
                    v.metodo_pago
                FROM ventas v
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                ORDER BY v.fecha_venta DESC
                LIMIT :limite
            ");
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerVentasRecientes: " . $e->getMessage());
            return [];
        }
    }
}
?>