<?php
class PermisoModel {
    private $db;
    private $table = 'permisos_roles';

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Obtener todos los permisos con información de roles y módulos
     */
    public function obtenerPermisosCompletos() {
        $query = "SELECT 
                    pr.id_permiso,
                    pr.id_rol,
                    pr.id_modulo,
                    pr.puede_ver,
                    pr.puede_crear,
                    pr.puede_editar,
                    pr.puede_eliminar,
                    pr.estado,
                    r.nombre_rol,
                    m.nombre_modulo,
                    m.descripcion,
                    m.icono,
                    m.ruta
                  FROM {$this->table} pr
                  INNER JOIN roles r ON pr.id_rol = r.id_rol
                  INNER JOIN modulos_sistema m ON pr.id_modulo = m.id_modulo
                  ORDER BY r.nombre_rol, m.nombre_modulo";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener permisos activos con información de roles y módulos
     */
    public function obtenerPermisosActivosCompletos() {
        $query = "SELECT 
                    pr.id_permiso,
                    pr.id_rol,
                    pr.id_modulo,
                    pr.puede_ver,
                    pr.puede_crear,
                    pr.puede_editar,
                    pr.puede_eliminar,
                    pr.estado,
                    r.nombre_rol,
                    m.nombre_modulo,
                    m.descripcion,
                    m.icono,
                    m.ruta
                  FROM {$this->table} pr
                  INNER JOIN roles r ON pr.id_rol = r.id_rol
                  INNER JOIN modulos_sistema m ON pr.id_modulo = m.id_modulo
                  WHERE pr.estado = 'activo'
                  ORDER BY r.nombre_rol, m.nombre_modulo";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un permiso por su ID
     */
    public function obtenerPorId($id_permiso) {
        $query = "SELECT 
                    pr.id_permiso,
                    pr.id_rol,
                    pr.id_modulo,
                    pr.puede_ver,
                    pr.puede_crear,
                    pr.puede_editar,
                    pr.puede_eliminar,
                    pr.estado,
                    r.nombre_rol,
                    m.nombre_modulo,
                    m.descripcion,
                    m.icono,
                    m.ruta
                  FROM {$this->table} pr
                  INNER JOIN roles r ON pr.id_rol = r.id_rol
                  INNER JOIN modulos_sistema m ON pr.id_modulo = m.id_modulo
                  WHERE pr.id_permiso = :id_permiso";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_permiso', $id_permiso, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear un nuevo permiso
     */
    public function crear($datos) {
        // Verificar si ya existe un permiso activo para este rol y módulo
        $existe = $this->verificarPermisoExistente($datos['id_rol'], $datos['id_modulo']);
        if ($existe) {
            return false;
        }

        $query = "INSERT INTO {$this->table} 
                  (id_rol, id_modulo, puede_ver, puede_crear, puede_editar, puede_eliminar, estado) 
                  VALUES (:id_rol, :id_modulo, :puede_ver, :puede_crear, :puede_editar, :puede_eliminar, :estado)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_rol', $datos['id_rol'], PDO::PARAM_INT);
        $stmt->bindParam(':id_modulo', $datos['id_modulo'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_ver', $datos['puede_ver'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_crear', $datos['puede_crear'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_editar', $datos['puede_editar'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_eliminar', $datos['puede_eliminar'], PDO::PARAM_INT);
        $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Actualizar un permiso existente
     */
    public function actualizar($id_permiso, $datos) {
        // Verificar si ya existe otro permiso activo para este rol y módulo (excluyendo el actual)
        $existe = $this->verificarPermisoExistente($datos['id_rol'], $datos['id_modulo'], $id_permiso);
        if ($existe) {
            return false;
        }

        $query = "UPDATE {$this->table} 
                  SET id_rol = :id_rol,
                      id_modulo = :id_modulo,
                      puede_ver = :puede_ver,
                      puede_crear = :puede_crear,
                      puede_editar = :puede_editar,
                      puede_eliminar = :puede_eliminar,
                      estado = :estado
                  WHERE id_permiso = :id_permiso";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_rol', $datos['id_rol'], PDO::PARAM_INT);
        $stmt->bindParam(':id_modulo', $datos['id_modulo'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_ver', $datos['puede_ver'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_crear', $datos['puede_crear'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_editar', $datos['puede_editar'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_eliminar', $datos['puede_eliminar'], PDO::PARAM_INT);
        $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_STR);
        $stmt->bindParam(':id_permiso', $id_permiso, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Cambiar estado de permiso (activo/inactivo)
     */
    public function cambiarEstado($id_permiso, $estado) {
        $query = "UPDATE {$this->table} 
                  SET estado = :estado 
                  WHERE id_permiso = :id_permiso";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':id_permiso', $id_permiso, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Verificar si ya existe un permiso ACTIVO para un rol y módulo específicos
     */
    private function verificarPermisoExistente($id_rol, $id_modulo, $id_permiso_excluir = null) {
        $query = "SELECT COUNT(*) as count 
                  FROM {$this->table} 
                  WHERE id_rol = :id_rol 
                  AND id_modulo = :id_modulo
                  AND estado = 'activo'";

        if ($id_permiso_excluir) {
            $query .= " AND id_permiso != :id_permiso_excluir";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
        $stmt->bindParam(':id_modulo', $id_modulo, PDO::PARAM_INT);
        
        if ($id_permiso_excluir) {
            $stmt->bindParam(':id_permiso_excluir', $id_permiso_excluir, PDO::PARAM_INT);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] > 0;
    }

    /**
     * Obtener permisos por rol (solo activos)
     */
    public function obtenerPorRol($id_rol) {
        $query = "SELECT 
                    pr.id_permiso,
                    pr.id_rol,
                    pr.id_modulo,
                    pr.puede_ver,
                    pr.puede_crear,
                    pr.puede_editar,
                    pr.puede_eliminar,
                    pr.estado,
                    m.nombre_modulo,
                    m.ruta,
                    m.icono
                  FROM {$this->table} pr
                  INNER JOIN modulos_sistema m ON pr.id_modulo = m.id_modulo
                  WHERE pr.id_rol = :id_rol
                  AND pr.estado = 'activo'
                  ORDER BY m.nombre_modulo";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar si un rol tiene permiso para una acción específica en un módulo
     */
    public function verificarPermiso($id_rol, $modulo, $accion) {
        // Mapear acciones a nombres de columna reales
        $columnasPermitidas = [
            'puede_ver', 'puede_crear', 'puede_editar', 'puede_eliminar'
        ];
        
        $columnasMap = [
            'ver' => 'puede_ver',
            'crear' => 'puede_crear', 
            'editar' => 'puede_editar',
            'eliminar' => 'puede_eliminar'
        ];
        
        // Obtener el nombre real de la columna
        $columna = $columnasMap[$accion] ?? null;
        
        // Validar que la columna sea permitida (seguridad)
        if (!$columna || !in_array($columna, $columnasPermitidas)) {
            return false;
        }
        
        $query = "SELECT 
                    pr.puede_ver,
                    pr.puede_crear, 
                    pr.puede_editar,
                    pr.puede_eliminar
                FROM {$this->table} pr
                INNER JOIN modulos_sistema m ON pr.id_modulo = m.id_modulo
                WHERE pr.id_rol = :id_rol 
                AND m.nombre_modulo = :modulo
                AND pr.estado = 'activo'";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
        $stmt->bindParam(':modulo', $modulo, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return false;
        }
        
        // Verificar el permiso específico
        switch($accion) {
            case 'ver': return $result['puede_ver'] == 1;
            case 'crear': return $result['puede_crear'] == 1;
            case 'editar': return $result['puede_editar'] == 1;
            case 'eliminar': return $result['puede_eliminar'] == 1;
            default: return false;
        }
    }

    /**
     * Eliminar un permiso (eliminación física) - Ya no se usa, se usa cambiarEstado
     */
    public function eliminar($id_permiso) {
        // En lugar de eliminar, cambiamos el estado a inactivo
        return $this->cambiarEstado($id_permiso, 'inactivo');
    }

    /**
     * Obtener módulos disponibles para asignar permisos
     */
    public function obtenerModulosDisponibles($id_rol) {
        $query = "SELECT m.* 
                  FROM modulos_sistema m
                  WHERE m.id_modulo NOT IN (
                      SELECT pr.id_modulo 
                      FROM {$this->table} pr 
                      WHERE pr.id_rol = :id_rol
                      AND pr.estado = 'activo'
                  )
                  AND m.estado = 'Activo'
                  ORDER BY m.orden";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reactivar un permiso inactivo
     */
    public function reactivarPermiso($id_rol, $id_modulo) {
        $query = "UPDATE {$this->table} 
                  SET estado = 'activo' 
                  WHERE id_rol = :id_rol 
                  AND id_modulo = :id_modulo";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
        $stmt->bindParam(':id_modulo', $id_modulo, PDO::PARAM_INT);

        return $stmt->execute();
    }
}