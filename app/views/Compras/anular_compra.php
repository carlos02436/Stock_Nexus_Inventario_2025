<?php
// app/controllers/anular_compra.php
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
    
    // Verificar que la compra no esté ya anulada
    $compra = $compraController->obtener($id_compra);
    if ($compra['estado'] == 'Anulada') {
        $_SESSION['error'] = 'La compra ya está anulada';
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
            
            // Crear movimiento de salida para anulación
            $datosMovimiento = [
                'id_producto' => $id_producto,
                'tipo_movimiento' => 'Salida',
                'cantidad' => $cantidad,
                'descripcion' => "Anulación de compra #" . $compra['codigo_compra'],
                'id_usuario' => $_SESSION['id_usuario'] // Asegúrate de tener este dato en sesión
            ];
            
            $inventarioController->crearMovimiento($datosMovimiento);
        }
        
        $_SESSION['success'] = 'Compra anulada correctamente y inventario actualizado';
    } else {
        $_SESSION['error'] = 'Error al anular la compra';
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
}

header('Location: index.php?page=compras');
exit();
?>