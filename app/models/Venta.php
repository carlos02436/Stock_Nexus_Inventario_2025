<?php
class Venta {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /** ==============================
     *  📋 LISTAR TODAS LAS VENTAS
     *  ============================== */
    public function listar() {
        try {
            $sql = "
                SELECT v.*, c.nombre_cliente, u.nombre_completo AS usuario_nombre
                FROM ventas v
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
                ORDER BY v.fecha_venta DESC
            ";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar ventas: " . $e->getMessage());
            return [];
        }
    }

    /** ==============================
     *  🔍 OBTENER UNA VENTA POR ID
     *  ============================== */
    public function obtenerPorId($id_venta) {
        try {
            $sql = "
                SELECT v.*, c.nombre_cliente, u.nombre_completo AS usuario_nombre
                FROM ventas v
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
                WHERE v.id_venta = :id_venta
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_venta', $id_venta);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener venta: " . $e->getMessage());
            return false;
        }
    }

    /** =================================
     *  📦 OBTENER DETALLE DE UNA VENTA
     *  ================================= */
    public function obtenerDetalle($id_venta) {
        try {
            $sql = "
                SELECT dv.*, p.codigo_producto, p.nombre_producto
                FROM detalle_ventas dv
                INNER JOIN productos p ON dv.id_producto = p.id_producto
                WHERE dv.id_venta = :id_venta
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_venta', $id_venta);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener detalle de venta: " . $e->getMessage());
            return [];
        }
    }

    /** ==============================
     *  ➕ CREAR UNA NUEVA VENTA
     *  ============================== */
    public function crearVenta($datos) {
        try {
            $this->db->beginTransaction();

            // Insertar la venta principal
            $sql = "
                INSERT INTO ventas (codigo_venta, id_cliente, id_usuario, metodo_pago, total_venta, estado)
                VALUES (:codigo, :cliente, :usuario, :metodo_pago, :total, :estado)
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':codigo' => $datos['codigo_venta'],
                ':cliente' => $datos['id_cliente'],
                ':usuario' => $datos['id_usuario'],
                ':metodo_pago' => $datos['metodo_pago'],
                ':total' => $datos['total_venta'],
                ':estado' => $datos['estado'] ?? 'Pendiente'
            ]);

            $id_venta = $this->db->lastInsertId();

            // Insertar cada producto vendido
            foreach ($datos['productos'] as $producto) {
                // Validar stock
                $sqlStock = "SELECT stock_actual FROM productos WHERE id_producto = :id";
                $stmt = $this->db->prepare($sqlStock);
                $stmt->bindParam(':id', $producto['id_producto']);
                $stmt->execute();
                $stock = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($stock['stock_actual'] < $producto['cantidad']) {
                    throw new Exception("Stock insuficiente para el producto: " . $producto['nombre_producto']);
                }

                // Insertar detalle
                $sqlDetalle = "
                    INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario)
                    VALUES (:venta, :producto, :cantidad, :precio)
                ";
                $stmt = $this->db->prepare($sqlDetalle);
                $stmt->execute([
                    ':venta' => $id_venta,
                    ':producto' => $producto['id_producto'],
                    ':cantidad' => $producto['cantidad'],
                    ':precio' => $producto['precio_unitario']
                ]);

                // Actualizar stock
                $sqlUpdate = "
                    UPDATE productos 
                    SET stock_actual = stock_actual - :cantidad
                    WHERE id_producto = :id
                ";
                $stmt = $this->db->prepare($sqlUpdate);
                $stmt->execute([
                    ':cantidad' => $producto['cantidad'],
                    ':id' => $producto['id_producto']
                ]);

                // Registrar movimiento en bodega
                $sqlMov = "
                    INSERT INTO movimientos_bodega (id_producto, tipo_movimiento, cantidad, descripcion, id_usuario)
                    VALUES (:producto, 'Salida', :cantidad, :descripcion, :usuario)
                ";
                $stmt = $this->db->prepare($sqlMov);
                $stmt->execute([
                    ':producto' => $producto['id_producto'],
                    ':cantidad' => $producto['cantidad'],
                    ':descripcion' => 'Venta #' . $datos['codigo_venta'],
                    ':usuario' => $datos['id_usuario']
                ]);
            }

            $this->db->commit();
            return $id_venta;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al crear venta: " . $e->getMessage());
            return false;
        }
    }

    /** ==============================
     *  🔄 ACTUALIZAR ESTADO DE VENTA
     *  ============================== */
    public function actualizarEstado($id_venta, $estado) {
        try {
            $sql = "UPDATE ventas SET estado = :estado WHERE id_venta = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id', $id_venta);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar estado: " . $e->getMessage());
            return false;
        }
    }

    /** ==============================
     *  🧾 OBTENER ÚLTIMO CÓDIGO
     *  ============================== */
    public function obtenerUltimoCodigo() {
        try {
            $stmt = $this->db->query("SELECT codigo_venta FROM ventas ORDER BY id_venta DESC LIMIT 1");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['codigo_venta'] : null;
        } catch (PDOException $e) {
            error_log("Error al obtener último código: " . $e->getMessage());
            return null;
        }
    }
}
?>