<?php
class PermisoController {
    private $db;
    private $permisoModel;

    public function __construct($db) {
        $this->db = $db;
        $this->permisoModel = new PermisoModel($db);
    }

    /**
     * Listar todos los permisos con información completa (activos e inactivos)
     */
    public function listarTodosPermisosCompletos() {
        try {
            return $this->permisoModel->obtenerPermisosCompletos();
        } catch (Exception $e) {
            error_log("Error en PermisoController::listarTodosPermisosCompletos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Listar solo permisos activos con información completa
     */
    public function listarPermisosCompletos() {
        try {
            return $this->permisoModel->obtenerPermisosActivosCompletos();
        } catch (Exception $e) {
            error_log("Error en PermisoController::listarPermisosCompletos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener permiso por ID
     */
    public function obtenerPorId($id_permiso) {
        try {
            return $this->permisoModel->obtenerPorId($id_permiso);
        } catch (Exception $e) {
            error_log("Error en PermisoController::obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear nuevo permiso
     */
    public function crear($datos) {
        try {
            // Validar datos requeridos
            if (empty($datos['id_rol']) || empty($datos['id_modulo'])) {
                return false;
            }

            // Asegurar que el estado esté definido
            if (!isset($datos['estado'])) {
                $datos['estado'] = 'activo';
            }

            return $this->permisoModel->crear($datos);
        } catch (Exception $e) {
            error_log("Error en PermisoController::crear: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar permiso existente
     */
    public function actualizar($id_permiso, $datos) {
        try {
            // Validar datos requeridos
            if (empty($datos['id_rol']) || empty($datos['id_modulo'])) {
                return false;
            }

            // Asegurar que el estado esté definido
            if (!isset($datos['estado'])) {
                $datos['estado'] = 'activo';
            }

            return $this->permisoModel->actualizar($id_permiso, $datos);
        } catch (Exception $e) {
            error_log("Error en PermisoController::actualizar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambiar estado de permiso (activo/inactivo)
     */
    public function cambiarEstado($id_permiso, $estado) {
        try {
            // Validar que el estado sea válido
            if (!in_array($estado, ['activo', 'inactivo'])) {
                return false;
            }

            return $this->permisoModel->cambiarEstado($id_permiso, $estado);
        } catch (Exception $e) {
            error_log("Error en PermisoController::cambiarEstado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * "Eliminar" permiso (cambiar estado a inactivo)
     */
    public function eliminar($id_permiso) {
        try {
            return $this->cambiarEstado($id_permiso, 'inactivo');
        } catch (Exception $e) {
            error_log("Error en PermisoController::eliminar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reactivar permiso
     */
    public function reactivar($id_permiso) {
        try {
            return $this->cambiarEstado($id_permiso, 'activo');
        } catch (Exception $e) {
            error_log("Error en PermisoController::reactivar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si un usuario tiene permiso para una acción
     */
    public function verificarPermisoUsuario($id_rol, $modulo, $accion) {
        try {
            return $this->permisoModel->verificarPermiso($id_rol, $modulo, $accion);
        } catch (Exception $e) {
            error_log("Error en PermisoController::verificarPermisoUsuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener permisos por rol (solo activos)
     */
    public function obtenerPermisosPorRol($id_rol) {
        try {
            return $this->permisoModel->obtenerPorRol($id_rol);
        } catch (Exception $e) {
            error_log("Error en PermisoController::obtenerPermisosPorRol: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener módulos disponibles para un rol
     */
    public function obtenerModulosDisponibles($id_rol) {
        try {
            return $this->permisoModel->obtenerModulosDisponibles($id_rol);
        } catch (Exception $e) {
            error_log("Error en PermisoController::obtenerModulosDisponibles: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar si existe un permiso duplicado activo
     */
    public function verificarPermisoDuplicado($id_rol, $id_modulo, $id_permiso_excluir = null) {
        try {
            // Esta verificación ahora se hace internamente en el modelo
            // Solo exponemos el método si es necesario
            return false;
        } catch (Exception $e) {
            error_log("Error en PermisoController::verificarPermisoDuplicado: " . $e->getMessage());
            return true; // Por seguridad, asumimos que existe duplicado si hay error
        }
    }
}