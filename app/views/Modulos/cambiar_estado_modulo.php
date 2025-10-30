<?php
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $moduloController = new ModuloController($db);
    
    $id_modulo = $_POST['id'] ?? '';
    $accion = $_POST['accion'] ?? '';
    
    if (!empty($id_modulo) && !empty($accion)) {
        $resultado = $moduloController->cambiarEstado($id_modulo, $accion);
        
        if ($resultado) {
            $_SESSION['mensaje'] = "Módulo {$accion}do exitosamente";
            $_SESSION['mensaje_tipo'] = 'success';
        } else {
            $_SESSION['mensaje'] = "Error al {$accion} el módulo";
            $_SESSION['mensaje_tipo'] = 'danger';
        }
    } else {
        $_SESSION['mensaje'] = "Datos incompletos";
        $_SESSION['mensaje_tipo'] = 'danger';
    }
}

header("Location: index.php?page=modulos");
exit;