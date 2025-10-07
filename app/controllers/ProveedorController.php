<?php
class ProveedorController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function listar() {
        try {
            $stmt = $this->db->query("SELECT * FROM proveedores ORDER BY nombre_proveedor");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listar proveedores: " . $e->getMessage());
            return [];
        }
    }

    public function obtener($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM proveedores WHERE id_proveedor = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtener proveedor: " . $e->getMessage());
            return false;
        }
    }

    public function crear($datos) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO proveedores 
                (nombre_proveedor, nit, telefono, correo, direccion, ciudad) 
                VALUES 
                (:nombre, :nit, :telefono, :correo, :direccion, :ciudad)
            ");
            
            return $stmt->execute([
                ':nombre' => $datos['nombre_proveedor'],
                ':nit' => $datos['nit'],
                ':telefono' => $datos['telefono'],
                ':correo' => $datos['correo'],
                ':direccion' => $datos['direccion'],
                ':ciudad' => $datos['ciudad']
            ]);
        } catch (PDOException $e) {
            error_log("Error en crear proveedor: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar($id, $datos) {
        try {
            $stmt = $this->db->prepare("
                UPDATE proveedores 
                SET nombre_proveedor = :nombre, nit = :nit, telefono = :telefono,
                    correo = :correo, direccion = :direccion, ciudad = :ciudad
                WHERE id_proveedor = :id
            ");
            
            return $stmt->execute([
                ':nombre' => $datos['nombre_proveedor'],
                ':nit' => $datos['nit'],
                ':telefono' => $datos['telefono'],
                ':correo' => $datos['correo'],
                ':direccion' => $datos['direccion'],
                ':ciudad' => $datos['ciudad'],
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Error en actualizar proveedor: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id) {
        try {
            // Verificar si hay compras asociadas
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM compras WHERE id_proveedor = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch();

            if ($result['total'] > 0) {
                return false; // No se puede eliminar si hay compras asociadas
            }

            $stmt = $this->db->prepare("DELETE FROM proveedores WHERE id_proveedor = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminar proveedor: " . $e->getMessage());
            return false;
        }
    }
}
?>