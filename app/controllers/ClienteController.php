<?php
class ClienteController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function listar() {
        try {
            $stmt = $this->db->query("SELECT * FROM clientes ORDER BY nombre_cliente");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listar clientes: " . $e->getMessage());
            return [];
        }
    }

    public function obtener($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM clientes WHERE id_cliente = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtener cliente: " . $e->getMessage());
            return false;
        }
    }

    public function crear($datos) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO clientes 
                (nombre_cliente, identificacion, telefono, correo, direccion, ciudad) 
                VALUES 
                (:nombre, :identificacion, :telefono, :correo, :direccion, :ciudad)
            ");
            
            return $stmt->execute([
                ':nombre' => $datos['nombre_cliente'],
                ':identificacion' => $datos['identificacion'],
                ':telefono' => $datos['telefono'],
                ':correo' => $datos['correo'],
                ':direccion' => $datos['direccion'],
                ':ciudad' => $datos['ciudad']
            ]);
        } catch (PDOException $e) {
            error_log("Error en crear cliente: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar($id, $datos) {
        try {
            $stmt = $this->db->prepare("
                UPDATE clientes 
                SET nombre_cliente = :nombre, identificacion = :identificacion, 
                    telefono = :telefono, correo = :correo, direccion = :direccion, 
                    ciudad = :ciudad
                WHERE id_cliente = :id
            ");
            
            return $stmt->execute([
                ':nombre' => $datos['nombre_cliente'],
                ':identificacion' => $datos['identificacion'],
                ':telefono' => $datos['telefono'],
                ':correo' => $datos['correo'],
                ':direccion' => $datos['direccion'],
                ':ciudad' => $datos['ciudad'],
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Error en actualizar cliente: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id) {
        try {
            // Verificar si hay ventas asociadas
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM ventas WHERE id_cliente = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch();

            if ($result['total'] > 0) {
                return false; // No se puede eliminar si hay ventas asociadas
            }

            $stmt = $this->db->prepare("DELETE FROM clientes WHERE id_cliente = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminar cliente: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorIdentificacion($identificacion) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM clientes WHERE identificacion = :identificacion");
            $stmt->bindParam(':identificacion', $identificacion);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en buscarPorIdentificacion: " . $e->getMessage());
            return false;
        }
    }
}
?>