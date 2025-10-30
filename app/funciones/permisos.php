<?php
// funciones/permisos.php

// Función para conectar a la base de datos
require_once __DIR__ . '/config/database.php'; // Conexión PDO

function tienePermiso($id_usuario, $nombre_modulo, $accion) {
    // Conectar a la base de datos usando tu clase Database
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT pr.puede_ver, pr.puede_crear, pr.puede_editar, pr.puede_eliminar 
              FROM permisos_roles pr 
              JOIN modulos_sistema ms ON pr.id_modulo = ms.id_modulo 
              JOIN usuarios u ON u.id_rol = pr.id_rol 
              WHERE u.id_usuario = :id_usuario AND ms.nombre_modulo = :nombre_modulo";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->bindParam(':nombre_modulo', $nombre_modulo, PDO::PARAM_STR);
    $stmt->execute();
    
    if ($permiso = $stmt->fetch(PDO::FETCH_ASSOC)) {
        switch($accion) {
            case 'ver': return (bool)$permiso['puede_ver'];
            case 'crear': return (bool)$permiso['puede_crear'];
            case 'editar': return (bool)$permiso['puede_editar'];
            case 'eliminar': return (bool)$permiso['puede_eliminar'];
            default: return false;
        }
    }
    
    return false;
}

function verificarPermiso($modulo, $accion) {
    // Asegurar que la sesión esté iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: ../login.php');
        exit;
    }
    
    if (!tienePermiso($_SESSION['id_usuario'], $modulo, $accion)) {
        http_response_code(403);
        die('No tienes permisos para realizar esta acción');
    }
}

// Función helper para mostrar elementos condicionalmente
function mostrarSiTienePermiso($modulo, $accion, $contenido) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['id_usuario']) && tienePermiso($_SESSION['id_usuario'], $modulo, $accion)) {
        echo $contenido;
    }
}

// Función para obtener todos los permisos de un usuario (útil para menús)
function obtenerPermisosUsuario($id_usuario) {
    // Conectar a la base de datos usando tu clase Database
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT ms.nombre_modulo, pr.puede_ver, pr.puede_crear, pr.puede_editar, pr.puede_eliminar 
              FROM permisos_roles pr 
              JOIN modulos_sistema ms ON pr.id_modulo = ms.id_modulo 
              JOIN usuarios u ON u.id_rol = pr.id_rol 
              WHERE u.id_usuario = :id_usuario AND pr.puede_ver = 1";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    
    $permisos = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $permisos[$row['nombre_modulo']] = $row;
    }
    
    return $permisos;
}

// Función adicional para verificar si un módulo es visible en el menú
function moduloEsVisible($id_usuario, $nombre_modulo) {
    return tienePermiso($id_usuario, $nombre_modulo, 'ver');
}
?>