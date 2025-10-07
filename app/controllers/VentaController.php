<?php
class VentaController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function listar() {
        try {
            $stmt = $this->db->query("
                SELECT v.*, c.nombre_cliente, u.nombre_completo as usuario_nombre
                FROM ventas v
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
                ORDER BY v.fecha_venta DESC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listar ventas: " . $e->getMessage());
            return [];
        }
    }

    public function obtener($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT v.*, c.nombre_cliente, u.nombre_completo as usuario_nombre
                FROM ventas v
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
                WHERE v.id_venta = :id
            ");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtener venta: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerDetalle($id_venta) {
        try {
            $stmt = $this->db->prepare("
                SELECT dv.*, p.codigo_producto, p.nombre_producto
                FROM detalle_ventas dv
                JOIN productos p ON dv.id_producto = p.id_producto
                WHERE dv.id_venta = :id_venta
            ");
            $stmt->bindParam(':id_venta', $id_venta);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerDetalle: " . $e->getMessage());
            return [];
        }
    }

    public function crear($datos) {
        try {
            $this->db->beginTransaction();

            // Insertar venta
            $stmt = $this->db->prepare("
                INSERT INTO ventas (codigo_venta, id_cliente, id_usuario, metodo_pago, total_venta, estado)
                VALUES (:codigo, :cliente, :usuario, :metodo_pago, :total, :estado)
            ");
            
            $stmt->execute([
                ':codigo' => $datos['codigo_venta'],
                ':cliente' => $datos['id_cliente'],
                ':usuario' => $datos['id_usuario'],
                ':metodo_pago' => $datos['metodo_pago'],
                ':total' => $datos['total_venta'],
                ':estado' => $datos['estado'] ?? 'Pendiente'
            ]);

            $id_venta = $this->db->lastInsertId();

            // Insertar detalles y actualizar stock
            foreach ($datos['productos'] as $producto) {
                // Verificar stock disponible
                $stmt = $this->db->prepare("SELECT stock_actual FROM productos WHERE id_producto = :id");
                $stmt->bindParam(':id', $producto['id_producto']);
                $stmt->execute();
                $stock = $stmt->fetch();

                if ($stock['stock_actual'] < $producto['cantidad']) {
                    throw new Exception("Stock insuficiente para " . $producto['nombre_producto']);
                }

                // Insertar detalle
                $stmt = $this->db->prepare("
                    INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario)
                    VALUES (:venta, :producto, :cantidad, :precio)
                ");
                $stmt->execute([
                    ':venta' => $id_venta,
                    ':producto' => $producto['id_producto'],
                    ':cantidad' => $producto['cantidad'],
                    ':precio' => $producto['precio_unitario']
                ]);

                // Actualizar stock del producto
                $stmt = $this->db->prepare("
                    UPDATE productos 
                    SET stock_actual = stock_actual - :cantidad
                    WHERE id_producto = :id
                ");
                $stmt->execute([
                    ':cantidad' => $producto['cantidad'],
                    ':id' => $producto['id_producto']
                ]);

                // Registrar movimiento de bodega
                $stmt = $this->db->prepare("
                    INSERT INTO movimientos_bodega (id_producto, tipo_movimiento, cantidad, descripcion, id_usuario)
                    VALUES (:producto, 'Salida', :cantidad, :descripcion, :usuario)
                ");
                $stmt->execute([
                    ':producto' => $producto['id_producto'],
                    ':cantidad' => $producto['cantidad'],
                    ':descripcion' => "Venta #" . $datos['codigo_venta'],
                    ':usuario' => $datos['id_usuario']
                ]);
            }

            $this->db->commit();
            return $id_venta;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error en crear venta: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarEstado($id, $estado) {
        try {
            $stmt = $this->db->prepare("
                UPDATE ventas SET estado = :estado WHERE id_venta = :id
            ");
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarEstado: " . $e->getMessage());
            return false;
        }
    }
}
?>