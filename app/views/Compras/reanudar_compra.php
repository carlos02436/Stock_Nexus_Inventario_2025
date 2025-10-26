<?php
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
    
    // Verificar que la compra esté anulada
    if ($compra['estado'] != 'Anulada') {
        $_SESSION['error'] = 'La compra no está anulada';
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
    
    // Reanudar la compra (cambiar a estado Pendiente)
    $resultado = $compraController->actualizarEstado($id_compra, 'Pendiente');
    
    if ($resultado) {
        // Actualizar inventario - sumar productos nuevamente
        foreach ($detalles_compra as $detalle) {
            $id_producto = $detalle['id_producto'];
            $cantidad = $detalle['cantidad'];
            
            // Usar el ID de usuario de la sesión o de la compra
            $id_usuario = $_SESSION['id_usuario'] ?? $compra['id_usuario'] ?? 1; // Fallback a 1 si no existe
            
            // Crear movimiento de entrada para reanudación
            $datosMovimiento = [
                'id_producto' => $id_producto,
                'tipo_movimiento' => 'Entrada',
                'cantidad' => $cantidad,
                'descripcion' => "Reanudación de compra #" . $compra['codigo_compra'],
                'id_usuario' => $id_usuario
            ];
            
            $inventarioController->crearMovimiento($datosMovimiento);
        }
        
        $_SESSION['success'] = 'Compra reanudada correctamente y inventario actualizado';
    } else {
        $_SESSION['error'] = 'Error al reanudar la compra';
    }
    
} catch (Exception $e) {
    error_log("Error en reanudar_compra: " . $e->getMessage());
    $_SESSION['error'] = 'Error al procesar la reanudación: ' . $e->getMessage();
}

header('Location: index.php?page=compras');
exit();
?>