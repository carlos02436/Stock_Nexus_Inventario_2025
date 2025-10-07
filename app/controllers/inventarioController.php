<?php
class InventarioController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function listarMovimientos() {
        try {
            $stmt = $this->db->query("
                SELECT m.*, p.codigo_producto, p.nombre_producto, u.nombre_completo as usuario_nombre
                FROM movimientos_bodega m
                JOIN productos p ON m.id_producto = p.id_producto
                LEFT JOIN usuarios u ON m.id_usuario = u.id_usuario
                ORDER BY m.fecha_movimiento DESC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarMovimientos: " . $e->getMessage());
            return [];
        }
    }

    public function crearMovimiento($datos) {
        try {
            $this->db->beginTransaction();

            // Insertar movimiento
            $stmt = $this->db->prepare("
                INSERT INTO movimientos_bodega 
                (id_producto, tipo_movimiento, cantidad, descripcion, id_usuario) 
                VALUES 
                (:producto, :tipo, :cantidad, :descripcion, :usuario)
            ");
            
            $stmt->execute([
                ':producto' => $datos['id_producto'],
                ':tipo' => $datos['tipo_movimiento'],
                ':cantidad' => $datos['cantidad'],
                ':descripcion' => $datos['descripcion'],
                ':usuario' => $datos['id_usuario']
            ]);

            // Actualizar stock del producto
            if ($datos['tipo_movimiento'] === 'Entrada') {
                $operacion = '+';
            } else {
                $operacion = '-';
            }

            $stmt = $this->db->prepare("
                UPDATE productos 
                SET stock_actual = stock_actual $operacion :cantidad 
                WHERE id_producto = :id
            ");
            $stmt->execute([
                ':cantidad' => $datos['cantidad'],
                ':id' => $datos['id_producto']
            ]);

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error en crearMovimiento: " . $e->getMessage());
            return false;
        }
    }

    public function getProductosConStockBajo() {
        try {
            $stmt = $this->db->query("
                SELECT *, (stock_actual - stock_minimo) as diferencia
                FROM productos 
                WHERE stock_actual <= stock_minimo AND estado = 'Activo'
                ORDER BY diferencia ASC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en getProductosConStockBajo: " . $e->getMessage());
            return [];
        }
    }

    public function getValorTotalInventario() {
        try {
            $stmt = $this->db->query("
                SELECT SUM(stock_actual * precio_compra) as valor_total 
                FROM productos 
                WHERE estado = 'Activo'
            ");
            $result = $stmt->fetch();
            return $result['valor_total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error en getValorTotalInventario: " . $e->getMessage());
            return 0;
        }
    }
}
?>