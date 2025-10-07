<?php
class Permiso {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerPermisosPorRol($rol) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, m.nombre_modulo, m.ruta, m.icono 
                FROM permisos_roles p 
                JOIN modulos_sistema m ON p.id_modulo = m.id_modulo 
                WHERE p.id_rol = :rol AND m.estado = 'Activo'
                ORDER BY m.orden
            ");
            $stmt->bindParam(':rol', $rol);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerPermisosPorRol: " . $e->getMessage());
            return [];
        }
    }

    public function verificarPermiso($rol, $modulo, $accion) {
        try {
            $stmt = $this->db->prepare("
                SELECT $accion as permitido 
                FROM permisos_roles p 
                JOIN modulos_sistema m ON p.id_modulo = m.id_modulo 
                WHERE p.id_rol = :rol AND m.ruta = :modulo
            ");
            $stmt->bindParam(':rol', $rol);
            $stmt->bindParam(':modulo', $modulo);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ? (bool)$result['permitido'] : false;
        } catch (PDOException $e) {
            error_log("Error en verificarPermiso: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerModulosActivos() {
        try {
            $stmt = $this->db->query("
                SELECT * FROM modulos_sistema 
                WHERE estado = 'Activo' 
                ORDER BY orden
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en obtenerModulosActivos: " . $e->getMessage());
            return [];
        }
    }

    public function actualizarPermisos($rol, $permisos) {
        try {
            $this->db->beginTransaction();

            // Eliminar permisos existentes
            $stmt = $this->db->prepare("DELETE FROM permisos_roles WHERE id_rol = :rol");
            $stmt->bindParam(':rol', $rol);
            $stmt->execute();

            // Insertar nuevos permisos
            $stmt = $this->db->prepare("
                INSERT INTO permisos_roles (id_rol, id_modulo, puede_ver, puede_crear, puede_editar, puede_eliminar) 
                VALUES (:rol, :modulo, :ver, :crear, :editar, :eliminar)
            ");

            foreach ($permisos as $modulo => $permiso) {
                $stmt->execute([
                    ':rol' => $rol,
                    ':modulo' => $modulo,
                    ':ver' => $permiso['ver'] ? 1 : 0,
                    ':crear' => $permiso['crear'] ? 1 : 0,
                    ':editar' => $permiso['editar'] ? 1 : 0,
                    ':eliminar' => $permiso['eliminar'] ? 1 : 0
                ]);
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error en actualizarPermisos: " . $e->getMessage());
            return false;
        }
    }
}
?>