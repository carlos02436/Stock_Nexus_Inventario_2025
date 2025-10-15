<?php
class VentaController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function listar() {
        try {
            $stmt = $this->db->query("
                SELECT v.*, c.nombre_cliente, u.nombre_completo AS usuario_nombre
                FROM ventas v
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
                ORDER BY v.id_venta DESC
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
                SELECT v.*, c.nombre_cliente, u.nombre_completo AS usuario_nombre
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

    public function crear($datos = null) {
        try {
            if ($datos === null) {
                if (session_status() === PHP_SESSION_NONE) {
                    @session_start();
                }

                // Procesar total
                $rawTotal = $_POST['total_venta'] ?? ($_POST['total_venta_limpio'] ?? '0');
                $totalLimpio = str_replace(['$', ' '], ['', ''], $rawTotal);
                $totalLimpio = str_replace('.', '', $totalLimpio);
                $totalLimpio = str_replace(',', '.', $totalLimpio);
                $total = floatval($totalLimpio);

                // Procesar productos
                $productos = [];
                if (!empty($_POST['productos']) && is_array($_POST['productos'])) {
                    foreach ($_POST['productos'] as $p) {
                        if (!empty($p['id_producto']) && !empty($p['cantidad']) && $p['cantidad'] > 0) {
                            $precio = $p['precio_unitario'] ?? 0;
                            if (is_string($precio)) {
                                $precio = str_replace(',', '.', $precio);
                            }

                            $productos[] = [
                                'id_producto' => $p['id_producto'],
                                'cantidad' => (float) $p['cantidad'],
                                'precio_unitario' => floatval($precio)
                            ];
                        }
                    }
                }

                $metodo_pago = $_POST['metodo_pago'] ?? 'Efectivo';

                // Estado automático según el tipo de pago
                $estado_venta = in_array(strtolower($metodo_pago), ['efectivo', 'transferencia', 'tarjeta'])
                    ? 'Pagada'
                    : 'Pendiente';

                $datos = [
                    'codigo_venta' => $_POST['codigo_venta'] ?? null,
                    'id_cliente' => !empty($_POST['id_cliente']) ? $_POST['id_cliente'] : null,
                    'id_usuario' => $_POST['id_usuario'] ?? ($_SESSION['id_usuario'] ?? null),
                    'metodo_pago' => $metodo_pago,
                    'total_venta' => $total,
                    'descuento_aplicado' => floatval($_POST['descuento_aplicado'] ?? 0),
                    'porcentaje_descuento' => floatval($_POST['porcentaje_descuento'] ?? 0),
                    'estado' => $estado_venta,
                    'productos' => $productos
                ];
            }

            // Validaciones básicas
            if (empty($datos['id_usuario'])) {
                throw new Exception("Falta id_usuario (verifica la sesión o el campo oculto id_usuario).");
            }
            if (empty($datos['productos']) || !is_array($datos['productos']) || count($datos['productos']) === 0) {
                throw new Exception("No se recibieron productos para la venta.");
            }
            if ($datos['total_venta'] <= 0) {
                throw new Exception("El total de la venta debe ser mayor a cero.");
            }

            // Generar código único
            if (empty($datos['codigo_venta'])) {
                $datos['codigo_venta'] = $this->generarCodigoUnico();
            }

            $this->db->beginTransaction();

            $codigo = $datos['codigo_venta'];
            $attempt = 0;
            $maxAttempts = 50;

            // Insertar venta con manejo de códigos duplicados
            while (true) {
                try {
                    $stmt = $this->db->prepare("
                        INSERT INTO ventas 
                        (codigo_venta, id_cliente, id_usuario, metodo_pago, total_venta, estado, descuento_aplicado, porcentaje_descuento)
                        VALUES (:codigo, :cliente, :usuario, :metodo_pago, :total, :estado, :descuento, :porcentaje)
                    ");
                    $stmt->execute([
                        ':codigo' => $codigo,
                        ':cliente' => $datos['id_cliente'],
                        ':usuario' => $datos['id_usuario'],
                        ':metodo_pago' => $datos['metodo_pago'],
                        ':total' => $datos['total_venta'],
                        ':estado' => $datos['estado'],
                        ':descuento' => $datos['descuento_aplicado'],
                        ':porcentaje' => $datos['porcentaje_descuento']
                    ]);
                    break;
                } catch (PDOException $e) {
                    $sqlState = $e->getCode();
                    $errorNo = $e->errorInfo[1] ?? null;
                    if (($sqlState == '23000' || $errorNo == 1062) && $attempt < $maxAttempts) {
                        // Código duplicado, generar uno nuevo
                        $num = (int) filter_var($codigo, FILTER_SANITIZE_NUMBER_INT);
                        $num++;
                        $codigo = 'VENTA' . str_pad($num, 3, '0', STR_PAD_LEFT);
                        $attempt++;
                        continue;
                    }
                    error_log("Error SQL crear venta: " . $e->getMessage());
                    throw $e;
                }
            }

            $id_venta = $this->db->lastInsertId();

            // Insertar detalles y actualizar stock
            foreach ($datos['productos'] as $producto) {
                $idProd = $producto['id_producto'] ?? null;
                $cantidad = $producto['cantidad'] ?? 0;
                $precio_unit = $producto['precio_unitario'] ?? 0;

                if (empty($idProd) || $cantidad <= 0) {
                    throw new Exception("Producto inválido en el detalle (id: {$idProd}, cantidad: {$cantidad}).");
                }

                // Verificar existencia y stock del producto
                $stmt = $this->db->prepare("SELECT stock_actual, nombre_producto FROM productos WHERE id_producto = :id");
                $stmt->execute([':id' => $idProd]);
                $productoInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$productoInfo) {
                    throw new Exception("El producto con id {$idProd} no existe.");
                }
                if ($productoInfo['stock_actual'] < $cantidad) {
                    throw new Exception("Stock insuficiente para el producto '{$productoInfo['nombre_producto']}'. Stock disponible: {$productoInfo['stock_actual']}, solicitado: {$cantidad}.");
                }

                // Insertar detalle de venta
                $stmt = $this->db->prepare("
                    INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario)
                    VALUES (:venta, :producto, :cantidad, :precio)
                ");
                $stmt->execute([
                    ':venta' => $id_venta,
                    ':producto' => $idProd,
                    ':cantidad' => $cantidad,
                    ':precio' => $precio_unit
                ]);

                // Actualizar stock del producto
                $stmt = $this->db->prepare("
                    UPDATE productos 
                    SET stock_actual = stock_actual - :cantidad
                    WHERE id_producto = :id
                ");
                $stmt->execute([':cantidad' => $cantidad, ':id' => $idProd]);

                // Registrar movimiento de bodega
                $stmt = $this->db->prepare("
                    INSERT INTO movimientos_bodega (id_producto, tipo_movimiento, cantidad, descripcion, id_usuario)
                    VALUES (:producto, 'Salida', :cantidad, :descripcion, :usuario)
                ");
                $stmt->execute([
                    ':producto' => $idProd,
                    ':cantidad' => $cantidad,
                    ':descripcion' => "Venta #" . $codigo,
                    ':usuario' => $datos['id_usuario']
                ]);
            }

            $this->db->commit();
            return $id_venta;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("ERROR crear venta: " . $e->getMessage());
            error_log("Datos de la venta: " . print_r($datos, true));
            return false;
        }
    }

    private function generarCodigoUnico() {
        $attempt = 0;
        $maxAttempts = 100;

        while ($attempt < $maxAttempts) {
            // Obtener el último código de venta
            $stmt = $this->db->query("SELECT codigo_venta FROM ventas ORDER BY id_venta DESC LIMIT 1");
            $ultimo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($ultimo && preg_match('/VENTA(\d+)/', $ultimo['codigo_venta'], $matches)) {
                $num = (int)$matches[1] + 1;
            } else {
                $num = 1;
            }

            $nuevoCodigo = 'VENTA' . str_pad($num, 3, '0', STR_PAD_LEFT);

            // Verificar si el código ya existe
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM ventas WHERE codigo_venta = ?");
            $stmt->execute([$nuevoCodigo]);
            $existe = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existe['count'] == 0) {
                return $nuevoCodigo;
            }

            $attempt++;
        }

        throw new Exception("No se pudo generar un código único después de $maxAttempts intentos");
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

    public function obtenerUltimoCodigo() {
        try {
            $stmt = $this->db->query("SELECT codigo_venta FROM ventas ORDER BY id_venta DESC LIMIT 1");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['codigo_venta'] : null;
        } catch (PDOException $e) {
            error_log("Error en obtenerUltimoCodigo: " . $e->getMessage());
            return null;
        }
    }
}