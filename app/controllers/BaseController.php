<?php
// controllers/BaseController.php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class BaseController {
    protected $db;
    protected $authMiddleware;
    
    public function __construct($db) {
        $this->db = $db;
        $this->authMiddleware = new AuthMiddleware($db);
    }
    
    /**
     * Método helper para verificar permisos en controladores hijos
     */
    protected function requerirPermiso($modulo, $accion) {
        return $this->authMiddleware->verificarPermiso($modulo, $accion);
    }
    
    /**
     * Cargar vista con datos
     */
    protected function cargarVista($vista, $datos = []) {
        extract($datos);
        require_once __DIR__ . "/../views/{$vista}.php";
    }
    
    /**
     * Redirigir a otra página
     */
    protected function redirigir($url) {
        header("Location: {$url}");
        exit;
    }
}
?>