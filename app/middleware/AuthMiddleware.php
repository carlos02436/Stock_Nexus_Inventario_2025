<?php
// app/middleware/AuthMiddleware.php
require_once __DIR__ . '/../models/PermisoModel.php';

class AuthMiddleware {
    private $permisoModel;
    
    public function __construct($db) {
        $this->permisoModel = new PermisoModel($db);
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    public function verificarAutenticacion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario_logged_in']) || $_SESSION['usuario_logged_in'] !== true) {
            header('Location: /Stock_Nexus_Inventario_2025/index.php?page=login');
            exit;
        }
    }
    
    /**
     * Obtener el ID del rol basado en el nombre del rol
     */
    private function obtenerIdRol($nombreRol) {
        // Mapeo de nombres de rol a IDs (ajusta según tu base de datos)
        $rolesMap = [
            'Administrador' => 1,
            'Vendedor' => 2, 
            'Contador' => 3,
            'Comprador' => 4,
            'Bodeguero' => 5
        ];
        
        return $rolesMap[$nombreRol] ?? null;
    }
    
    /**
     * Verificar permisos para un módulo y acción específicos
     */
    public function verificarPermiso($modulo, $accion) {
        $this->verificarAutenticacion();
        
        // Si es administrador, tiene todos los permisos
        if ($_SESSION['usuario_rol'] === 'Administrador') {
            return true;
        }
        
        $id_rol = $this->obtenerIdRol($_SESSION['usuario_rol']);
        
        if (!$id_rol || !$this->permisoModel->verificarPermiso($id_rol, $modulo, $accion)) {
            http_response_code(403);
            
            // Si es AJAX, devolver JSON
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No tienes permisos para realizar esta acción'
                ]);
            } else {
                // Obtener la ruta base del proyecto - CORREGIDA
                $base_path = dirname(dirname(__DIR__)); // Esto apunta a Stock_Nexus_Inventario_2025/
                
                // Verificar si los archivos existen antes de incluirlos
                $header_path = $base_path . '/app/views/plantillas/header.php';
                $footer_path = $base_path . '/app/views/plantillas/footer.php';
                
                // Incluir header si existe
                if (file_exists($header_path)) {
                    include $header_path;
                } else {
                    // Header básico si no existe el archivo
                    echo '<!DOCTYPE html><html><head><title>Acceso Denegado</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"></head><body>';
                }
                
                echo '
                <main class="container py-5" style="margin-top: 80px;">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card shadow-lg border-3 border-success"> <!-- Borde verde añadido -->
                                <div class="card-body text-center py-5">
                                    <div class="mb-4">
                                        <i class="fas fa-ban fa-4x text-danger"></i>
                                    </div>
                                    <h1 class="card-title text-danger mb-3">❌ Acceso Denegado</h1>
                                    <p class="card-text text-muted mb-3">No tienes permisos para acceder a esta sección.</p>
                                    <div class="bg-light p-3 rounded mb-4 border border-success"> <!-- Borde verde añadido -->
                                        <small class="text-muted">
                                            <strong>Módulo:</strong> ' . htmlspecialchars($modulo) . '<br>
                                            <strong>Acción:</strong> ' . htmlspecialchars($accion) . '<br>
                                            <strong>Rol:</strong> ' . htmlspecialchars($_SESSION['usuario_rol']) . '
                                        </small>
                                    </div>
                                    <a href="index.php?page=dashboard" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>';
                
                // Incluir footer si existe
                if (file_exists($footer_path)) {
                    include $footer_path;
                } else {
                    // Footer básico si no existe el archivo
                    echo '</body></html>';
                }
            }
            exit;
        }
        
        return true;
    }
    
    /**
     * Verificar permiso sin redirección (para lógica condicional en vistas)
     */
    public function tienePermiso($modulo, $accion) {
        if (!isset($_SESSION['usuario_logged_in']) || $_SESSION['usuario_logged_in'] !== true) {
            return false;
        }
        
        // Administrador tiene todos los permisos
        if ($_SESSION['usuario_rol'] === 'Administrador') {
            return true;
        }
        
        $id_rol = $this->obtenerIdRol($_SESSION['usuario_rol']);
        if (!$id_rol) {
            return false;
        }
        
        return $this->permisoModel->verificarPermiso($id_rol, $modulo, $accion);
    }
}
?>