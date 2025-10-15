<?php
// app/views/ventas/anular_venta.php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../app/controllers/VentaController.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?page=ventas");
    exit;
}

$id_venta = intval($_GET['id']);
$ventaController = new VentaController($db);

try {
    $db->beginTransaction();

    // Obtener los productos del detalle
    $stmt = $db->prepare("
        SELECT dv.id_producto, dv.cantidad, p.nombre_producto
        FROM detalle_ventas dv
        INNER JOIN productos p ON dv.id_producto = p.id_producto
        WHERE dv.id_venta = :id
    ");
    $stmt->execute([':id' => $id_venta]);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($productos)) {
        throw new Exception("No se encontraron productos en esta venta.");
    }

    // Revertir stock y registrar movimiento
    foreach ($productos as $producto) {
        $stmt = $db->prepare("
            UPDATE productos 
            SET stock_actual = stock_actual + :cantidad
            WHERE id_producto = :id
        ");
        $stmt->execute([
            ':cantidad' => $producto['cantidad'],
            ':id' => $producto['id_producto']
        ]);

        // Registrar movimiento de entrada
        $stmt = $db->prepare("
            INSERT INTO movimientos_bodega (id_producto, tipo_movimiento, cantidad, descripcion, id_usuario)
            VALUES (:producto, 'Entrada', :cantidad, :descripcion, :usuario)
        ");
        $stmt->execute([
            ':producto' => $producto['id_producto'],
            ':cantidad' => $producto['cantidad'],
            ':descripcion' => "Anulación de venta ID #$id_venta",
            ':usuario' => $_SESSION['id_usuario'] ?? 1
        ]);
    }

    // Cambiar el estado de la venta
    $stmt = $db->prepare("UPDATE ventas SET estado = 'Anulada' WHERE id_venta = :id");
    $stmt->execute([':id' => $id_venta]);

    $db->commit();
    header("Location: index.php?page=ventas");
    exit;

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Error al anular venta: " . $e->getMessage());
    header("Location: index.php?page=ventas&error=anulacion");
    exit;
}