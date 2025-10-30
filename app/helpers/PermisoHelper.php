<?php
// app/helpers/PermisoHelper.php

class PermisoHelper {
    private static $authMiddleware = null;
    
    private static function getAuthMiddleware() {
        if (self::$authMiddleware === null) {
            // Necesitamos la conexión a la base de datos
            // Esta función se llamará desde las vistas, así que usamos una variable global
            if (isset($GLOBALS['db'])) {
                require_once __DIR__ . '/../middleware/AuthMiddleware.php';
                self::$authMiddleware = new AuthMiddleware($GLOBALS['db']);
            }
        }
        return self::$authMiddleware;
    }
    
    /**
     * Mostrar contenido solo si tiene permiso
     */
    public static function mostrarSiTienePermiso($modulo, $accion, $contenido) {
        $auth = self::getAuthMiddleware();
        if ($auth && $auth->tienePermiso($modulo, $accion)) {
            echo $contenido;
        }
    }
    
    /**
     * Verificar si tiene permiso (para lógica en vistas)
     */
    public static function puede($modulo, $accion) {
        $auth = self::getAuthMiddleware();
        return $auth && $auth->tienePermiso($modulo, $accion);
    }
}
?>