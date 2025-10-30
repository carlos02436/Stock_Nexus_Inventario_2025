<?php
class PermisoController {
    private $db;
    private $permisoModel;

    public function __construct($db) {
        $this->db = $db;
        $this->permisoModel = new PermisoModel($db);
    }

    /**
     * Listar todos los permisos con información completa
     */
    public function listarPermisosCompletos() {
        try {
            return $this->permisoModel->obtenerPermisosCompletos();
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

            return $this->permisoModel->actualizar($id_permiso, $datos);
        } catch (Exception $e) {
            error_log("Error en PermisoController::actualizar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambiar estado de permiso
     */
    public function cambiarEstado($id_permiso, $accion) {
        try {
            // Como no hay campo estado, eliminamos el permiso si se quiere "inactivar"
            if ($accion === 'inactivar') {
                return $this->permisoModel->eliminar($id_permiso);
            }
            return false;
        } catch (Exception $e) {
            error_log("Error en PermisoController::cambiarEstado: " . $e->getMessage());
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
     * Obtener permisos por rol
     */
    public function obtenerPermisosPorRol($id_rol) {
        try {
            return $this->permisoModel->obtenerPorRol($id_rol);
        } catch (Exception $e) {
            error_log("Error en PermisoController::obtenerPermisosPorRol: " . $e->getMessage());
            return [];
        }
    }
}