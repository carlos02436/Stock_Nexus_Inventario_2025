<?php
class DashboardController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getEstadisticas() {
        try {
            $stats = [];

            // Total de productos
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM productos WHERE estado = 'Activo'");
            $stats['total_productos'] = $stmt->fetch()['total'];

            // Productos con stock bajo
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM productos WHERE stock_actual <= stock_minimo AND estado = 'Activo'");
            $stats['stock_bajo'] = $stmt->fetch()['total'];

            // Total de ventas del mes
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM ventas WHERE MONTH(fecha_venta) = MONTH(CURRENT_DATE()) AND estado = 'Pagada'");
            $stats['ventas_mes'] = $stmt->fetch()['total'];

            // Ingresos del mes
            $stmt = $this->db->query("SELECT COALESCE(SUM(total_venta), 0) as total FROM ventas WHERE MONTH(fecha_venta) = MONTH(CURRENT_DATE()) AND estado = 'Pagada'");
            $stats['ingresos_mes'] = $stmt->fetch()['total'];

            // Compras del mes
            $stmt = $this->db->query("SELECT COALESCE(SUM(total_compra), 0) as total FROM compras WHERE MONTH(fecha_compra) = MONTH(CURRENT_DATE()) AND estado = 'Pagada'");
            $stats['compras_mes'] = $stmt->fetch()['total'];

            // Clientes activos
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM clientes");
            $stats['total_clientes'] = $stmt->fetch()['total'];

            return $stats;

        } catch (PDOException $e) {
            error_log("Error en getEstadisticas: " . $e->getMessage());
            return [];
        }
    }

    public function getVentasRecientes($limite = 5) {
        try {
            $stmt = $this->db->prepare("
                SELECT v.codigo_venta, c.nombre_cliente, v.total_venta, v.fecha_venta, v.estado 
                FROM ventas v 
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente 
                ORDER BY v.fecha_venta DESC 
                LIMIT :limite
            ");
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en getVentasRecientes: " . $e->getMessage());
            return [];
        }
    }

    public function getProductosStockBajo() {
        try {
            $stmt = $this->db->query("
                SELECT codigo_producto, nombre_producto, stock_actual, stock_minimo 
                FROM productos 
                WHERE stock_actual <= stock_minimo AND estado = 'Activo' 
                ORDER BY stock_actual ASC 
                LIMIT 10
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en getProductosStockBajo: " . $e->getMessage());
            return [];
        }
    }
}
?>