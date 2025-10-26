<?php
// app/controllers/reanudar_compra.php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'administrador') {
    $_SESSION['error'] = 'Acceso denegado. Se requiere rol de administrador.';
    header('Location: index.php?page=login');
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = 'ID de compra no válido';
    header('Location: index.php?page=compras');
    exit();
}

$id_compra = $_GET['id'];

try {
    $compraController = new CompraController($db);
    $inventarioController = new InventarioController($db);
    
    // Obtener los detalles de la compra para actualizar el inventario
    $detalles_compra = $compraController->obtenerDetalle($id_compra);
    
    if (!$detalles_compra) {
        $_SESSION['error'] = 'Compra no encontrada';
        header('Location: index.php?page=compras');
        exit();
    }
    
    // Verificar que la compra esté anulada
    $compra = $compraController->obtener($id_compra);
    if ($compra['estado'] != 'Anulada') {
        $_SESSION['error'] = 'La compra no está anulada';
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
            
            // Crear movimiento de entrada para reanudación
            $datosMovimiento = [
                'id_producto' => $id_producto,
                'tipo_movimiento' => 'Entrada',
                'cantidad' => $cantidad,
                'descripcion' => "Reanudación de compra #" . $compra['codigo_compra'],
                'id_usuario' => $_SESSION['id_usuario'] // Asegúrate de tener este dato en sesión
            ];
            
            $inventarioController->crearMovimiento($datosMovimiento);
        }
        
        $_SESSION['success'] = 'Compra reanudada correctamente y inventario actualizado';
    } else {
        $_SESSION['error'] = 'Error al reanudar la compra';
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
}

header('Location: index.php?page=compras');
exit();
?>