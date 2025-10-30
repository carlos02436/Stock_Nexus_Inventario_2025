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
        // Verificar si ya existe un permiso para este rol y módulo
        $existe = $this->verificarPermisoExistente($datos['id_rol'], $datos['id_modulo']);
        if ($existe) {
            return false;
        }

        $query = "INSERT INTO {$this->table} 
                  (id_rol, id_modulo, puede_ver, puede_crear, puede_editar, puede_eliminar) 
                  VALUES (:id_rol, :id_modulo, :puede_ver, :puede_crear, :puede_editar, :puede_eliminar)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_rol', $datos['id_rol'], PDO::PARAM_INT);
        $stmt->bindParam(':id_modulo', $datos['id_modulo'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_ver', $datos['puede_ver'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_crear', $datos['puede_crear'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_editar', $datos['puede_editar'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_eliminar', $datos['puede_eliminar'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Actualizar un permiso existente
     */
    public function actualizar($id_permiso, $datos) {
        // Verificar si ya existe otro permiso para este rol y módulo (excluyendo el actual)
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
                      puede_eliminar = :puede_eliminar
                  WHERE id_permiso = :id_permiso";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_rol', $datos['id_rol'], PDO::PARAM_INT);
        $stmt->bindParam(':id_modulo', $datos['id_modulo'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_ver', $datos['puede_ver'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_crear', $datos['puede_crear'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_editar', $datos['puede_editar'], PDO::PARAM_INT);
        $stmt->bindParam(':puede_eliminar', $datos['puede_eliminar'], PDO::PARAM_INT);
        $stmt->bindParam(':id_permiso', $id_permiso, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * ELIMINAR método cambiarEstado - NO EXISTE EL CAMPO ESTADO
     */
    public function cambiarEstado($id_permiso, $accion) {
        // Como no hay campo estado, no podemos cambiar el estado
        // En su lugar, podríamos eliminar el permiso o dejarlo como está
        return false;
    }

    /**
     * Verificar si ya existe un permiso para un rol y módulo específicos
     */
    private function verificarPermisoExistente($id_rol, $id_modulo, $id_permiso_excluir = null) {
        $query = "SELECT COUNT(*) as count 
                  FROM {$this->table} 
                  WHERE id_rol = :id_rol 
                  AND id_modulo = :id_modulo";

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
     * Obtener permisos por rol
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
                    m.nombre_modulo,
                    m.ruta,
                    m.icono
                  FROM {$this->table} pr
                  INNER JOIN modulos_sistema m ON pr.id_modulo = m.id_modulo
                  WHERE pr.id_rol = :id_rol
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
        $query = "SELECT pr.{$accion} as tiene_permiso
                  FROM {$this->table} pr
                  INNER JOIN modulos_sistema m ON pr.id_modulo = m.id_modulo
                  WHERE pr.id_rol = :id_rol 
                  AND m.nombre_modulo = :modulo";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
        $stmt->bindParam(':modulo', $modulo, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result && $result['tiene_permiso'] == 1;
    }

    /**
     * Eliminar un permiso (eliminación física)
     */
    public function eliminar($id_permiso) {
        $query = "DELETE FROM {$this->table} WHERE id_permiso = :id_permiso";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_permiso', $id_permiso, PDO::PARAM_INT);
        
        return $stmt->execute();
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
                  )
                  AND m.estado = 'Activo'
                  ORDER BY m.orden";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}