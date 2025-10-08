<?php
// app/controllers/eliminar_producto.php

// Verificar si es una petición AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

try {
    // Verificar que se haya proporcionado un ID
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('ID de producto no proporcionado');
    }

    $idProducto = intval($_GET['id']);

    // Verificar permisos de usuario si es necesario
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception('No tiene permisos para realizar esta acción');
    }

    // Crear instancia del controlador
    $productoController = new ProductoController($db);

    // Intentar eliminar el producto
    $resultado = $productoController->eliminar($idProducto);

    if ($resultado) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Producto eliminado correctamente'
            ]);
        } else {
            $_SESSION['success'] = 'Producto eliminado correctamente';
            header('Location: index.php?page=inventario');
        }
    } else {
        throw new Exception('No se pudo eliminar el producto');
    }

} catch (Exception $e) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    } else {
        $_SESSION['error'] = $e->getMessage();
        header('Location: index.php?page=inventario');
    }
}
exit;