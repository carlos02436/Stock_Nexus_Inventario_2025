<?php
class CategoriaController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function listar() {
        try {
            $stmt = $this->db->query("SELECT * FROM categorias ORDER BY nombre_categoria");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en listar categorias: " . $e->getMessage());
            return [];
        }
    }

    public function obtener($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categorias WHERE id_categoria = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtener categoria: " . $e->getMessage());
            return false;
        }
    }

    public function crear($datos) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO categorias (nombre_categoria, descripcion, estado) 
                VALUES (:nombre, :descripcion, :estado)
            ");
            
            return $stmt->execute([
                ':nombre' => $datos['nombre_categoria'],
                ':descripcion' => $datos['descripcion'],
                ':estado' => $datos['estado'] ?? 'Activo'
            ]);
        } catch (PDOException $e) {
            error_log("Error en crear categoria: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar($id, $datos) {
        try {
            $stmt = $this->db->prepare("
                UPDATE categorias 
                SET nombre_categoria = :nombre, descripcion = :descripcion, estado = :estado 
                WHERE id_categoria = :id
            ");
            
            return $stmt->execute([
                ':nombre' => $datos['nombre_categoria'],
                ':descripcion' => $datos['descripcion'],
                ':estado' => $datos['estado'],
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Error en actualizar categoria: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id) {
        try {
            // Verificar si hay productos usando esta categoría (sin filtrar por estado)
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM productos WHERE id_categoria = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch();

            if ($result['total'] > 0) {
                return false; // No se puede eliminar si hay productos asociados
            }

            $stmt = $this->db->prepare("DELETE FROM categorias WHERE id_categoria = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminar categoria: " . $e->getMessage());
            return false;
        }
    }
}
?>