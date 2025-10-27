<?php
class ClienteController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function listar() {
        try {
            $stmt = $this->db->query("SELECT * FROM clientes WHERE estado = 'activo' ORDER BY nombre_cliente");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listar clientes: " . $e->getMessage());
            return [];
        }
    }

    public function listarTodos() {
        try {
            $stmt = $this->db->query("SELECT * FROM clientes ORDER BY nombre_cliente");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarTodos clientes: " . $e->getMessage());
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
                (nombre_cliente, identificacion, telefono, correo, direccion, ciudad, estado) 
                VALUES 
                (:nombre, :identificacion, :telefono, :correo, :direccion, :ciudad, 'activo')
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
            // En lugar de eliminar, marcamos como inactivo
            $stmt = $this->db->prepare("UPDATE clientes SET estado = 'inactivo' WHERE id_cliente = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminar (inactivar) cliente: " . $e->getMessage());
            return false;
        }
    }

    public function activar($id) {
        try {
            $stmt = $this->db->prepare("UPDATE clientes SET estado = 'activo' WHERE id_cliente = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en activar cliente: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorIdentificacion($identificacion) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM clientes WHERE identificacion = :identificacion AND estado = 'activo'");
            $stmt->bindParam(':identificacion', $identificacion);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en buscarPorIdentificacion: " . $e->getMessage());
            return false;
        }
    }

    public function listarInactivos() {
        try {
            $stmt = $this->db->query("SELECT * FROM clientes WHERE estado = 'inactivo' ORDER BY nombre_cliente");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listarInactivos: " . $e->getMessage());
            return [];
        }
    }
}
?>