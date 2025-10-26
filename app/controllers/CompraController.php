<?php
class CompraController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Obtiene el próximo código de compra consecutivo
     */
    public function obtenerProximoCodigo() {
        try {
            $stmt = $this->db->query("
                SELECT MAX(CAST(SUBSTRING(codigo_compra, 5) AS UNSIGNED)) as ultimo_numero 
                FROM compras 
                WHERE codigo_compra LIKE 'COMP%'
            ");
            $resultado = $stmt->fetch();
            
            $proximoNumero = ($resultado['ultimo_numero'] ?? 0) + 1;
            return "COMP" . str_pad($proximoNumero, 3, '0', STR_PAD_LEFT);
        } catch (PDOException $e) {
            error_log("Error en obtenerProximoCodigo: " . $e->getMessage());
            return "COMP001";
        }
    }

    public function listar() {
        try {
            $stmt = $this->db->query("
                SELECT c.*, p.nombre_proveedor, u.nombre_completo as usuario_nombre
                FROM compras c
                LEFT JOIN proveedores p ON c.id_proveedor = p.id_proveedor
                LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
                ORDER BY c.fecha_compra DESC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listar compras: " . $e->getMessage());
            return [];
        }
    }

    public function obtener($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, p.nombre_proveedor, u.nombre_completo as usuario_nombre
                FROM compras c
                LEFT JOIN proveedores p ON c.id_proveedor = p.id_proveedor
                LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
                WHERE c.id_compra = :id
            ");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtener compra: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerDetalle($id_compra) {
        try {
            $stmt = $this->db->prepare("
                SELECT dc.*, p.codigo_producto, p.nombre_producto
                FROM detalle_compras dc
                JOIN productos p ON dc.id_producto = p.id_producto
                WHERE dc.id_compra = :id_compra
            ");
            $stmt->bindParam(':id_compra', $id_compra);
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

            // Insertar compra
            $stmt = $this->db->prepare("
                INSERT INTO compras (codigo_compra, id_proveedor, id_usuario, total_compra, descuento, estado)
                VALUES (:codigo, :proveedor, :usuario, :total, :descuento, :estado)
            ");
            
            $stmt->execute([
                ':codigo' => $datos['codigo_compra'],
                ':proveedor' => $datos['id_proveedor'],
                ':usuario' => $datos['id_usuario'],
                ':total' => $datos['total_compra'],
                ':descuento' => $datos['descuento'] ?? 0,
                ':estado' => $datos['estado'] ?? 'Pagada'
            ]);

            $id_compra = $this->db->lastInsertId();

            // Insertar detalles y actualizar stock
            foreach ($datos['productos'] as $producto) {
                // Insertar detalle
                $stmt = $this->db->prepare("
                    INSERT INTO detalle_compras (id_compra, id_producto, cantidad, precio_unitario)
                    VALUES (:compra, :producto, :cantidad, :precio)
                ");
                $stmt->execute([
                    ':compra' => $id_compra,
                    ':producto' => $producto['id_producto'],
                    ':cantidad' => $producto['cantidad'],
                    ':precio' => $producto['precio_unitario']
                ]);

                // Actualizar stock del producto
                $stmt = $this->db->prepare("
                    UPDATE productos 
                    SET stock_actual = stock_actual + :cantidad,
                        precio_compra = :precio
                    WHERE id_producto = :id
                ");
                $stmt->execute([
                    ':cantidad' => $producto['cantidad'],
                    ':precio' => $producto['precio_unitario'],
                    ':id' => $producto['id_producto']
                ]);

                // Registrar movimiento de bodega
                $stmt = $this->db->prepare("
                    INSERT INTO movimientos_bodega (id_producto, tipo_movimiento, cantidad, descripcion, id_usuario)
                    VALUES (:producto, 'Entrada', :cantidad, :descripcion, :usuario)
                ");
                $stmt->execute([
                    ':producto' => $producto['id_producto'],
                    ':cantidad' => $producto['cantidad'],
                    ':descripcion' => "Compra #" . $datos['codigo_compra'],
                    ':usuario' => $datos['id_usuario']
                ]);
            }

            $this->db->commit();
            return $id_compra;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error en crear compra: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarEstado($id, $estado) {
        try {
            $stmt = $this->db->prepare("
                UPDATE compras SET estado = :estado WHERE id_compra = :id
            ");
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarEstado: " . $e->getMessage());
            return false;
        }
    }
    public function obtenerProveedores() {
        try {
            $stmt = $this->db->query("SELECT id_proveedor, nombre_proveedor FROM proveedores WHERE estado = 1 ORDER BY nombre_proveedor");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerProveedores: " . $e->getMessage());
            return [];
        }
    }

    /* Obtener compra por ID */
    public function obtenerCompraPorId($id_compra) {
        return $this->obtener($id_compra);
    }

    /* Obtener detalles de compra para inventario */
    public function obtenerDetallesCompra($id_compra) {
        return $this->obtenerDetalle($id_compra);
    }
}
?>