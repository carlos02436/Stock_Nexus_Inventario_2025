<?php
// app/views/ventas/anular_venta.php

require_once __DIR__ . '/../../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar que se envíe el id_venta
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?page=compras#historial_compras");
    exit;
}

$id_compra = $_GET['id'];

try {
    // Incluir los controladores con la ruta correcta desde views/Compras
    require_once __DIR__ . '/../../controllers/CompraController.php';
    require_once __DIR__ . '/../../controllers/InventarioController.php';
    
    $compraController = new CompraController($db);
    $inventarioController = new InventarioController($db);
    
    // Obtener la compra completa
    $compra = $compraController->obtener($id_compra);
    
    if (!$compra) {
        $_SESSION['error'] = 'Compra no encontrada';
        header('Location: index.php?page=compras');
        exit();
    }
    
    // Verificar que la compra no esté ya anulada
    if ($compra['estado'] == 'Anulada') {
        $_SESSION['error'] = 'La compra ya está anulada';
        header('Location: index.php?page=compras');
        exit();
    }
    
    // Obtener los detalles de la compra para actualizar el inventario
    $detalles_compra = $compraController->obtenerDetalle($id_compra);
    
    if (!$detalles_compra) {
        $_SESSION['error'] = 'No se encontraron detalles de la compra';
        header('Location: index.php?page=compras');
        exit();
    }
    
    // Anular la compra
    $resultado = $compraController->actualizarEstado($id_compra, 'Anulada');
    
    if ($resultado) {
        // Actualizar inventario - restar productos
        foreach ($detalles_compra as $detalle) {
            $id_producto = $detalle['id_producto'];
            $cantidad = $detalle['cantidad'];
            
            // Usar el ID de usuario de la sesión o de la compra
            $id_usuario = $_SESSION['id_usuario'] ?? $compra['id_usuario'] ?? 1; // Fallback a 1 si no existe
            
            // Crear movimiento de salida para anulación
            $datosMovimiento = [
                'id_producto' => $id_producto,
                'tipo_movimiento' => 'Salida',
                'cantidad' => $cantidad,
                'descripcion' => "Anulación de compra #" . $compra['codigo_compra'],
                'id_usuario' => $id_usuario
            ];
            
            $inventarioController->crearMovimiento($datosMovimiento);
        }
        
        $_SESSION['success'] = 'Compra anulada correctamente y inventario actualizado';
    } else {
        $_SESSION['error'] = 'Error al anular la compra';
    }
    
} catch (Exception $e) {
    error_log("Error en anular_compra: " . $e->getMessage());
    $_SESSION['error'] = 'Error al procesar la anulación: ' . $e->getMessage();
}

header('Location: index.php?page=compras');
exit();
?>