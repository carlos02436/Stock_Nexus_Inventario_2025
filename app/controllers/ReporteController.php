<?php
class ReporteController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function generarReporteVentas($fecha_inicio, $fecha_fin) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    v.codigo_venta,
                    v.fecha_venta,
                    c.nombre_cliente,
                    v.total_venta,
                    v.estado,
                    v.metodo_pago,
                    u.nombre_completo as vendedor
                FROM ventas v
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
                WHERE v.fecha_venta BETWEEN :fecha_inicio AND :fecha_fin
                ORDER BY v.fecha_venta DESC
            ");
            $stmt->execute([
                ':fecha_inicio' => $fecha_inicio,
                ':fecha_fin' => $fecha_fin
            ]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en generarReporteVentas: " . $e->getMessage());
            return [];
        }
    }

    public function generarReporteInventario() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    p.codigo_producto,
                    p.nombre_producto,
                    c.nombre_categoria,
                    p.stock_actual,
                    p.stock_minimo,
                    p.precio_compra,
                    p.precio_venta,
                    (p.stock_actual * p.precio_compra) as valor_inventario,
                    CASE 
                        WHEN p.stock_actual <= p.stock_minimo THEN 'BAJO'
                        WHEN p.stock_actual <= (p.stock_minimo * 2) THEN 'MEDIO'
                        ELSE 'NORMAL'
                    END as estado_stock
                FROM productos p
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE p.estado = 'Activo'
                ORDER BY p.nombre_producto
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en generarReporteInventario: " . $e->getMessage());
            return [];
        }
    }

    public function generarReporteFinanciero($fecha_inicio, $fecha_fin) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    tipo_pago,
                    referencia,
                    descripcion,
                    monto,
                    metodo_pago,
                    fecha_pago,
                    u.nombre_completo as usuario
                FROM pagos p
                LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.fecha_pago BETWEEN :fecha_inicio AND :fecha_fin
                ORDER BY p.fecha_pago DESC
            ");
            $stmt->execute([
                ':fecha_inicio' => $fecha_inicio,
                ':fecha_fin' => $fecha_fin
            ]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en generarReporteFinanciero: " . $e->getMessage());
            return [];
        }
    }

    public function generarReporteCompras($fecha_inicio, $fecha_fin) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.codigo_compra,
                    c.fecha_compra,
                    p.nombre_proveedor,
                    c.total_compra,
                    c.estado,
                    u.nombre_completo as comprador
                FROM compras c
                LEFT JOIN proveedores p ON c.id_proveedor = p.id_proveedor
                LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
                WHERE c.fecha_compra BETWEEN :fecha_inicio AND :fecha_fin
                ORDER BY c.fecha_compra DESC
            ");
            $stmt->execute([
                ':fecha_inicio' => $fecha_inicio,
                ':fecha_fin' => $fecha_fin
            ]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en generarReporteCompras: " . $e->getMessage());
            return [];
        }
    }

    public function getEstadisticasVentas($periodo = 30) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    DATE(fecha_venta) as fecha,
                    COUNT(*) as total_ventas,
                    SUM(total_venta) as total_ingresos,
                    AVG(total_venta) as promedio_venta
                FROM ventas 
                WHERE fecha_venta >= DATE_SUB(CURRENT_DATE, INTERVAL :periodo DAY)
                    AND estado = 'Pagada'
                GROUP BY DATE(fecha_venta)
                ORDER BY fecha
            ");
            $stmt->bindValue(':periodo', $periodo, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en getEstadisticasVentas: " . $e->getMessage());
            return [];
        }
    }

    public function getProductosMasVendidos($limite = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.codigo_producto,
                    p.nombre_producto,
                    SUM(dv.cantidad) as total_vendido,
                    SUM(dv.subtotal) as total_ingresos
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
            error_log("Error en getProductosMasVendidos: " . $e->getMessage());
            return [];
        }
    }
}
?>