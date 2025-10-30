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
        $resultado = $permisoController->cambiarEstado($id_permiso, $accion);
        
        if ($resultado) {
            $_SESSION['mensaje'] = "Permiso {$accion}do exitosamente";
            $_SESSION['mensaje_tipo'] = 'success';
        } else {
            $_SESSION['mensaje'] = "Error al {$accion} el permiso";
            $_SESSION['mensaje_tipo'] = 'danger';
        }
    } else {
        $_SESSION['mensaje'] = "Datos incompletos";
        $_SESSION['mensaje_tipo'] = 'danger';
    }
}

header("Location: index.php?page=permisos");
exit;