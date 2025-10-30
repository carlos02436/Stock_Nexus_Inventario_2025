<?php
class ModuloModel {
    private $db;
    private $table = 'modulos_sistema';

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Obtener todos los módulos activos
     */
    public function listar() {
        $query = "SELECT * FROM {$this->table} WHERE estado = 'Activo' ORDER BY orden";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los módulos (incluyendo inactivos)
     */
    public function listarTodos() {
        $query = "SELECT * FROM {$this->table} ORDER BY orden";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener módulo por ID
     */
    public function obtenerPorId($id_modulo) {
        $query = "SELECT * FROM {$this->table} WHERE id_modulo = :id_modulo";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_modulo', $id_modulo, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear nuevo módulo - CORREGIDO
     */
    public function crear($datos) {
        // Obtener el siguiente orden
        $orden = $this->obtenerSiguienteOrden();

        // Preparar valores para evitar el error de referencia
        $nombre_modulo = $datos['nombre_modulo'];
        $descripcion = isset($datos['descripcion']) ? $datos['descripcion'] : '';
        $icono = isset($datos['icono']) ? $datos['icono'] : '';
        $ruta = isset($datos['ruta']) ? $datos['ruta'] : '';

        $query = "INSERT INTO {$this->table} 
                  (nombre_modulo, descripcion, icono, ruta, estado, orden) 
                  VALUES (:nombre_modulo, :descripcion, :icono, :ruta, 'Activo', :orden)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre_modulo', $nombre_modulo, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':icono', $icono, PDO::PARAM_STR);
        $stmt->bindParam(':ruta', $ruta, PDO::PARAM_STR);
        $stmt->bindParam(':orden', $orden, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Actualizar módulo existente - CORREGIDO
     */
    public function actualizar($id_modulo, $datos) {
        // Preparar valores para evitar el error de referencia
        $nombre_modulo = $datos['nombre_modulo'];
        $descripcion = isset($datos['descripcion']) ? $datos['descripcion'] : '';
        $icono = isset($datos['icono']) ? $datos['icono'] : '';
        $ruta = isset($datos['ruta']) ? $datos['ruta'] : '';
        $orden = $datos['orden'];

        $query = "UPDATE {$this->table} 
                  SET nombre_modulo = :nombre_modulo,
                      descripcion = :descripcion,
                      icono = :icono,
                      ruta = :ruta,
                      orden = :orden
                  WHERE id_modulo = :id_modulo";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre_modulo', $nombre_modulo, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':icono', $icono, PDO::PARAM_STR);
        $stmt->bindParam(':ruta', $ruta, PDO::PARAM_STR);
        $stmt->bindParam(':orden', $orden, PDO::PARAM_INT);
        $stmt->bindParam(':id_modulo', $id_modulo, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Cambiar estado de módulo
     */
    public function cambiarEstado($id_modulo, $accion) {
        $estado = ($accion === 'activar') ? 'Activo' : 'Inactivo';
        
        $query = "UPDATE {$this->table} 
                  SET estado = :estado
                  WHERE id_modulo = :id_modulo";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':id_modulo', $id_modulo, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Verificar si un módulo existe
     */
    public function existeModulo($nombre_modulo, $id_modulo_excluir = null) {
        $query = "SELECT COUNT(*) as count 
                  FROM {$this->table} 
                  WHERE nombre_modulo = :nombre_modulo";

        if ($id_modulo_excluir) {
            $query .= " AND id_modulo != :id_modulo_excluir";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre_modulo', $nombre_modulo, PDO::PARAM_STR);
        
        if ($id_modulo_excluir) {
            $stmt->bindParam(':id_modulo_excluir', $id_modulo_excluir, PDO::PARAM_INT);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] > 0;
    }

    /**
     * Obtener módulos disponibles para un rol específico
     */
    public function obtenerModulosDisponibles($id_rol) {
        $query = "SELECT m.* 
                  FROM {$this->table} m
                  WHERE m.id_modulo NOT IN (
                      SELECT pr.id_modulo 
                      FROM permisos_roles pr 
                      WHERE pr.id_rol = :id_rol
                  )
                  AND m.estado = 'Activo'
                  ORDER BY m.orden";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener módulos por IDs
     */
    public function obtenerPorIds($ids_modulos) {
        if (empty($ids_modulos)) {
            return [];
        }

        $placeholders = str_repeat('?,', count($ids_modulos) - 1) . '?';
        $query = "SELECT * FROM {$this->table} WHERE id_modulo IN ($placeholders) AND estado = 'Activo' ORDER BY orden";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($ids_modulos);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar módulos por nombre
     */
    public function buscarPorNombre($nombre) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE nombre_modulo LIKE :nombre 
                  AND estado = 'Activo' 
                  ORDER BY orden";
        
        $stmt = $this->db->prepare($query);
        $nombreBusqueda = '%' . $nombre . '%';
        $stmt->bindParam(':nombre', $nombreBusqueda, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener el siguiente valor para el campo orden
     */
    private function obtenerSiguienteOrden() {
        $query = "SELECT COALESCE(MAX(orden), 0) + 1 as siguiente_orden FROM {$this->table}";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['siguiente_orden'];
    }

    /**
     * Reordenar módulos - CORREGIDO
     */
    public function reordenar($ordenes) {
        try {
            $this->db->beginTransaction();

            $query = "UPDATE {$this->table} SET orden = :orden WHERE id_modulo = :id_modulo";
            $stmt = $this->db->prepare($query);

            foreach ($ordenes as $id_modulo => $orden) {
                $id = $id_modulo;
                $ord = $orden;
                $stmt->bindParam(':orden', $ord, PDO::PARAM_INT);
                $stmt->bindParam(':id_modulo', $id, PDO::PARAM_INT);
                $stmt->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al reordenar módulos: " . $e->getMessage());
            return false;
        }
    }
}