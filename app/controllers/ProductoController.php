<?php
class ProductoController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function listar() {
        try {
            $stmt = $this->db->query("
                SELECT p.*, c.nombre_categoria 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                ORDER BY p.nombre_producto
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listar productos: " . $e->getMessage());
            return [];
        }
    }

    public function obtener($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.nombre_categoria 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                WHERE p.id_producto = :id
            ");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtener producto: " . $e->getMessage());
            return false;
        }
    }

    public function crear($datos) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO productos 
                (codigo_producto, nombre_producto, id_categoria, descripcion, stock_actual, stock_minimo, 
                 unidad_medida, precio_compra, precio_venta, estado) 
                VALUES 
                (:codigo, :nombre, :categoria, :descripcion, :stock_actual, :stock_minimo, 
                 :unidad, :precio_compra, :precio_venta, :estado)
            ");
            
            return $stmt->execute([
                ':codigo' => $datos['codigo_producto'],
                ':nombre' => $datos['nombre_producto'],
                ':categoria' => $datos['id_categoria'],
                ':descripcion' => $datos['descripcion'],
                ':stock_actual' => $datos['stock_actual'] ?? 0,
                ':stock_minimo' => $datos['stock_minimo'] ?? 0,
                ':unidad' => $datos['unidad_medida'] ?? 'Unidad',
                ':precio_compra' => $datos['precio_compra'] ?? 0,
                ':precio_venta' => $datos['precio_venta'] ?? 0,
                ':estado' => $datos['estado'] ?? 'Activo'
            ]);
        } catch (PDOException $e) {
            error_log("Error en crear producto: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar($id, $datos) {
        try {
            $stmt = $this->db->prepare("
                UPDATE productos 
                SET codigo_producto = :codigo, nombre_producto = :nombre, id_categoria = :categoria,
                    descripcion = :descripcion, stock_actual = :stock_actual, stock_minimo = :stock_minimo,
                    unidad_medida = :unidad, precio_compra = :precio_compra, precio_venta = :precio_venta,
                    estado = :estado
                WHERE id_producto = :id
            ");
            
            return $stmt->execute([
                ':codigo' => $datos['codigo_producto'],
                ':nombre' => $datos['nombre_producto'],
                ':categoria' => $datos['id_categoria'],
                ':descripcion' => $datos['descripcion'],
                ':stock_actual' => $datos['stock_actual'],
                ':stock_minimo' => $datos['stock_minimo'],
                ':unidad' => $datos['unidad_medida'],
                ':precio_compra' => $datos['precio_compra'],
                ':precio_venta' => $datos['precio_venta'],
                ':estado' => $datos['estado'],
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Error en actualizar producto: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id, $eliminacionFisica = false) {
        try {
            // Verificar si el producto existe
            $sql = "SELECT * FROM productos WHERE id_producto = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            $producto = $stmt->fetch();
            if (!$producto) {
                throw new Exception('El producto no existe');
            }

            if ($eliminacionFisica) {
                // ELIMINACIÓN FÍSICA - Eliminar completamente de la base de datos
                
                // Primero eliminar registros relacionados en otras tablas
                // (Ajusta estas consultas según tu estructura de base de datos)
                
                // 1. Eliminar movimientos relacionados
                try {
                    $sqlMovimientos = "DELETE FROM movimientos WHERE id_producto = ?";
                    $stmtMovimientos = $this->db->prepare($sqlMovimientos);
                    $stmtMovimientos->execute([$id]);
                } catch (Exception $e) {
                    // Si no existe la tabla movimientos, continuar
                }
                
                // 2. Eliminar detalles de ventas relacionados
                try {
                    $sqlDetalles = "DELETE FROM detalle_ventas WHERE id_producto = ?";
                    $stmtDetalles = $this->db->prepare($sqlDetalles);
                    $stmtDetalles->execute([$id]);
                } catch (Exception $e) {
                    // Si no existe la tabla detalle_ventas, continuar
                }
                
                // Finalmente eliminar el producto
                $sql = "DELETE FROM productos WHERE id_producto = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$id]);

            } else {
                // ELIMINACIÓN LÓGICA - Cambiar estado a Inactivo
                $sql = "UPDATE productos SET estado = 'Inactivo' WHERE id_producto = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$id]);
            }

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("Error al eliminar producto: " . $e->getMessage());
            
            if ($e->getCode() == '23000') {
                throw new Exception('No se puede eliminar el producto porque tiene registros relacionados en el sistema');
            }
            
            throw new Exception('Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    public function actualizarStock($id_producto, $cantidad) {
        try {
            $stmt = $this->db->prepare("
                UPDATE productos 
                SET stock_actual = stock_actual + :cantidad 
                WHERE id_producto = :id
            ");
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':id', $id_producto);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarStock: " . $e->getMessage());
            return false;
        }
    }

    // En tu ProductoController
    public function obtenerUltimosCodigosPorCategoria() {
        try {
            $sql = "
                SELECT 
                    id_categoria,
                    MAX(codigo_producto) as ultimo_codigo
                FROM productos 
                WHERE id_categoria IS NOT NULL 
                AND codigo_producto REGEXP '^[A-Z]{3}[0-9]{3}$'
                GROUP BY id_categoria
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $ultimosCodigos = [];
            foreach ($resultados as $fila) {
                $ultimosCodigos[$fila['id_categoria']] = $fila['ultimo_codigo'];
            }
            
            return $ultimosCodigos;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerUltimosCodigosPorCategoria: " . $e->getMessage());
            return [];
        }
    }
}
?>