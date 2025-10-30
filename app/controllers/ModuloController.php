<?php
class ModuloController {
    private $db;
    private $moduloModel;

    public function __construct($db) {
        $this->db = $db;
        $this->moduloModel = new ModuloModel($db);
    }

    /**
     * Listar todos los módulos activos
     */
    public function listar() {
        try {
            return $this->moduloModel->listar();
        } catch (Exception $e) {
            error_log("Error en ModuloController::listar: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Listar todos los módulos (incluyendo inactivos)
     */
    public function listarTodos() {
        try {
            return $this->moduloModel->listarTodos();
        } catch (Exception $e) {
            error_log("Error en ModuloController::listarTodos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener módulo por ID
     */
    public function obtenerPorId($id_modulo) {
        try {
            return $this->moduloModel->obtenerPorId($id_modulo);
        } catch (Exception $e) {
            error_log("Error en ModuloController::obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear nuevo módulo
     */
    public function crear($datos) {
        try {
            // Validar datos requeridos
            if (empty($datos['nombre_modulo'])) {
                return false;
            }

            // Asegurar que los campos opcionales tengan valores por defecto
            $datosCompletos = [
                'nombre_modulo' => trim($datos['nombre_modulo']),
                'descripcion' => trim($datos['descripcion'] ?? ''),
                'icono' => trim($datos['icono'] ?? 'cube'),
                'ruta' => trim($datos['ruta'] ?? '')
            ];

            // Verificar si el módulo ya existe
            if ($this->moduloModel->existeModulo($datosCompletos['nombre_modulo'])) {
                return false;
            }

            return $this->moduloModel->crear($datosCompletos);
        } catch (Exception $e) {
            error_log("Error en ModuloController::crear: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar módulo existente
     */
    public function actualizar($id_modulo, $datos) {
        try {
            // Validar datos requeridos
            if (empty($datos['nombre_modulo'])) {
                return false;
            }

            // Verificar si el módulo ya existe (excluyendo el actual)
            if ($this->moduloModel->existeModulo($datos['nombre_modulo'], $id_modulo)) {
                return false;
            }

            return $this->moduloModel->actualizar($id_modulo, $datos);
        } catch (Exception $e) {
            error_log("Error en ModuloController::actualizar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambiar estado de módulo
     */
    public function cambiarEstado($id_modulo, $accion) {
        try {
            return $this->moduloModel->cambiarEstado($id_modulo, $accion);
        } catch (Exception $e) {
            error_log("Error en ModuloController::cambiarEstado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener módulos disponibles para un rol específico
     */
    public function obtenerModulosDisponibles($id_rol) {
        try {
            return $this->moduloModel->obtenerModulosDisponibles($id_rol);
        } catch (Exception $e) {
            error_log("Error en ModuloController::obtenerModulosDisponibles: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar si un módulo existe
     */
    public function existeModulo($nombre_modulo, $id_modulo_excluir = null) {
        try {
            return $this->moduloModel->existeModulo($nombre_modulo, $id_modulo_excluir);
        } catch (Exception $e) {
            error_log("Error en ModuloController::existeModulo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reordenar módulos
     */
    public function reordenar($ordenes) {
        try {
            return $this->moduloModel->reordenar($ordenes);
        } catch (Exception $e) {
            error_log("Error en ModuloController::reordenar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener módulos para el menú de navegación
     */
    public function obtenerParaMenu($id_rol) {
        try {
            // Si el usuario es administrador, mostrar todos los módulos activos
            if ($id_rol == 1) { // Asumiendo que 1 es el ID del rol Administrador
                return $this->moduloModel->listar();
            }

            // Para otros roles, obtener solo los módulos a los que tienen acceso
            $permisoModel = new PermisoModel($this->db);
            $permisos = $permisoModel->obtenerPorRol($id_rol);
            
            $modulos_ids = [];
            foreach ($permisos as $permiso) {
                if ($permiso['puede_ver'] == 1) {
                    $modulos_ids[] = $permiso['id_modulo'];
                }
            }

            if (empty($modulos_ids)) {
                return [];
            }

            return $this->moduloModel->obtenerPorIds($modulos_ids);
        } catch (Exception $e) {
            error_log("Error en ModuloController::obtenerParaMenu: " . $e->getMessage());
            return [];
        }
    }
}