<?php
require_once __DIR__ . '/BaseController.php';

class GastoController extends BaseController {
    private $gastoModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->gastoModel = new GastoOperativo($db);
    }

    /**
     * Listar todos los gastos
     */
    public function listar() {
        $this->requerirPermiso('Finanzas', 'ver');
        
        $gastos = $this->gastoModel->obtenerTodos();
        
        $datos = [
            'titulo' => 'Gestión de Gastos Operativos',
            'gastos' => $gastos
        ];
        
        $this->cargarVista('finanzas/gastos', $datos);
    }

    /**
     * Mostrar formulario para crear gasto
     */
    public function mostrarCrear() {
        $this->requerirPermiso('Finanzas', 'crear');
        
        $datos = [
            'titulo' => 'Crear Nuevo Gasto',
            'categorias' => $this->gastoModel->obtenerCategorias()
        ];
        
        $this->cargarVista('finanzas/crear_gasto', $datos);
    }

    /**
     * Crear nuevo gasto
     */
    public function crear($datos) {
        $this->requerirPermiso('Finanzas', 'crear');
        
        try {
            // Validar datos
            if (empty($datos['fecha']) || empty($datos['valor'])) {
                $_SESSION['mensaje'] = 'Fecha y valor son obligatorios';
                $_SESSION['tipo_mensaje'] = 'error';
                header('Location: index.php?page=crear_gasto');
                exit();
            }

            $resultado = $this->gastoModel->crear($datos);
            
            if ($resultado) {
                $_SESSION['mensaje'] = 'Gasto creado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al crear el gasto';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        } catch (Exception $e) {
            $_SESSION['mensaje'] = 'Error del sistema: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'error';
        }
        
        header('Location: index.php?page=gastos');
        exit();
    }

    /**
     * Mostrar formulario para editar gasto
     */
    public function mostrarEditar($id_gasto) {
        $this->requerirPermiso('Finanzas', 'editar');
        
        $gasto = $this->gastoModel->obtenerPorId($id_gasto);
        
        if (!$gasto) {
            $_SESSION['mensaje'] = 'Gasto no encontrado';
            $_SESSION['tipo_mensaje'] = 'error';
            header('Location: index.php?page=gastos');
            exit();
        }
        
        $datos = [
            'titulo' => 'Editar Gasto',
            'gasto' => $gasto,
            'categorias' => $this->gastoModel->obtenerCategorias()
        ];
        
        $this->cargarVista('finanzas/editar_gasto', $datos);
    }

    /**
     * Actualizar gasto existente
     */
    public function actualizar($id_gasto, $datos) {
        $this->requerirPermiso('Finanzas', 'editar');
        
        try {
            // Validar datos
            if (empty($datos['fecha']) || empty($datos['valor'])) {
                $_SESSION['mensaje'] = 'Fecha y valor son obligatorios';
                $_SESSION['tipo_mensaje'] = 'error';
                header("Location: index.php?page=editar_gasto&id=$id_gasto");
                exit();
            }

            $resultado = $this->gastoModel->actualizar($id_gasto, $datos);
            
            if ($resultado) {
                $_SESSION['mensaje'] = 'Gasto actualizado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar el gasto';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        } catch (Exception $e) {
            $_SESSION['mensaje'] = 'Error del sistema: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'error';
        }
        
        header('Location: index.php?page=gastos');
        exit();
    }

    /**
     * Eliminar gasto
     */
    public function eliminar($id_gasto) {
        $this->requerirPermiso('Finanzas', 'eliminar');
        
        try {
            $resultado = $this->gastoModel->eliminar($id_gasto);
            
            if ($resultado) {
                $_SESSION['mensaje'] = 'Gasto eliminado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al eliminar el gasto';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        } catch (Exception $e) {
            $_SESSION['mensaje'] = 'Error del sistema: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'error';
        }
        
        header('Location: index.php?page=gastos');
        exit();
    }
}
?>