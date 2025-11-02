<?php
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $permisoController = new PermisoController($db);
    
    $id_permiso = $_POST['id'] ?? '';
    $accion = $_POST['accion'] ?? '';
    
    if (!empty($id_permiso) && !empty($accion)) {
        if ($accion === 'activar') {
            $resultado = $permisoController->reactivar($id_permiso);
            $mensaje_accion = 'activado';
        } else {
            $resultado = $permisoController->eliminar($id_permiso);
            $mensaje_accion = 'inactivado';
        }
        
        if ($resultado) {
            $_SESSION['mensaje'] = "Permiso {$mensaje_accion} exitosamente";
            $_SESSION['mensaje_tipo'] = 'success';
        } else {
            $_SESSION['mensaje'] = "Error al {$mensaje_accion} el permiso";
            $_SESSION['mensaje_tipo'] = 'danger';
        }
    } else {
        $_SESSION['mensaje'] = "Datos incompletos";
        $_SESSION['mensaje_tipo'] = 'danger';
    }
}

header("Location: index.php?page=permisos");
exit;