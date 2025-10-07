<?php
class Cliente {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM clientes WHERE id_cliente = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorIdentificacion($identificacion) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM clientes WHERE identificacion = :identificacion");
            $stmt->bindParam(':identificacion', $identificacion);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerPorIdentificacion: " . $e->getMessage());
            return false;
        }
    }

    public function listarTodos() {
        try {
            $stmt = $this->db->query("SELECT * FROM clientes ORDER BY nombre_cliente");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarTodos: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerVentas($id_cliente, $limite = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM ventas 
                WHERE id_cliente = :id 
                ORDER BY fecha_venta DESC 
                LIMIT :limite
            ");
            $stmt->bindParam(':id', $id_cliente);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerVentas: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerEstadisticasCompras($id_cliente) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_compras,
                    SUM(total_venta) as monto_total,
                    AVG(total_venta) as promedio_compra,
                    MAX(fecha_venta) as ultima_compra
                FROM ventas 
                WHERE id_cliente = :id AND estado = 'Pagada'
            ");
            $stmt->bindParam(':id', $id_cliente);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerEstadisticasCompras: " . $e->getMessage());
            return ['total_compras' => 0, 'monto_total' => 0, 'promedio_compra' => 0, 'ultima_compra' => null];
        }
    }

    public function buscar($termino) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM clientes 
                WHERE nombre_cliente LIKE :termino OR identificacion LIKE :termino
                ORDER BY nombre_cliente
            ");
            $termino = "%$termino%";
            $stmt->bindParam(':termino', $termino);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en buscar: " . $e->getMessage());
            return [];
        }
    }
}
?>