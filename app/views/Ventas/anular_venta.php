<?php
// app/views/ventas/anular_venta.php

require_once __DIR__ . '/../../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar que se envíe el id_venta
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?page=ventas");
    exit;
}

$id_venta = intval($_GET['id']);

try {
    $database = new Database();
    $db = $database->getConnection();

    // Verificar que la venta exista
    $stmt = $db->prepare("SELECT id_venta, estado FROM ventas WHERE id_venta = ?");
    $stmt->execute([$id_venta]);
    $venta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$venta) {
        header("Location: index.php?page=ventas");
        exit;
    }

    // Si ya está anulada, no hacer nada
    if ($venta['estado'] === 'Anulada') {
        header("Location: index.php?page=ventas");
        exit;
    }

    // Cambiar estado a "Anulada"
    $update = $db->prepare("UPDATE ventas SET estado = 'Anulada' WHERE id_venta = ?");
    $update->execute([$id_venta]);

    // 🔁 Devolver stock al inventario
    $detalle = $db->prepare("
        SELECT id_producto, cantidad 
        FROM detalle_ventas 
        WHERE id_venta = ?
    ");
    $detalle->execute([$id_venta]);

    while ($item = $detalle->fetch(PDO::FETCH_ASSOC)) {
        $restock = $db->prepare("
            UPDATE productos 
            SET stock_actual = stock_actual + :cantidad
            WHERE id_producto = :id_producto
        ");
        $restock->execute([
            ':cantidad' => $item['cantidad'],
            ':id_producto' => $item['id_producto']
        ]);

        // Registrar movimiento de bodega
        $movimiento = $db->prepare("
            INSERT INTO movimientos_bodega (id_producto, tipo_movimiento, cantidad, descripcion, id_usuario)
            VALUES (:id_producto, 'Entrada', :cantidad, :descripcion, :id_usuario)
        ");
        $movimiento->execute([
            ':id_producto' => $item['id_producto'],
            ':cantidad' => $item['cantidad'],
            ':descripcion' => 'Reversión por anulación de venta ID ' . $id_venta,
            ':id_usuario' => $_SESSION['id_usuario'] ?? null
        ]);
    }

    // Redirigir de nuevo
    header("Location: index.php?page=ventas");
    exit;

} catch (PDOException $e) {
    error_log("Error al anular venta: " . $e->getMessage());
    header("Location: index.php?page=ventas");
    exit;
}