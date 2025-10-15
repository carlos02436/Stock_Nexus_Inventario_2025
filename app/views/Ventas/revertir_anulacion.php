<?php
// app/views/ventas/revertir_anulacion.php

require_once __DIR__ . '/../../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar rol de administrador
if (!isset($_SESSION['rol']) || stripos(trim($_SESSION['rol']), 'admin') === false) {
    header("Location: index.php?page=ventas");
    exit;
}

// Validar que exista el código de venta
if (!isset($_GET['codigo']) || empty($_GET['codigo'])) {
    header("Location: index.php?page=ventas");
    exit;
}

$codigo_venta = trim($_GET['codigo']);

try {
    // ✅ Conexión correcta según tu estructura
    $database = new Database();
    $db = $database->getConnection();

    // Verificar que la venta exista y esté anulada
    $stmt = $db->prepare("SELECT estado FROM ventas WHERE codigo_venta = ?");
    $stmt->execute([$codigo_venta]);
    $venta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$venta || $venta['estado'] !== 'Anulada') {
        header("Location: index.php?page=ventas");
        exit;
    }

    // Cambiar el estado de la venta a "Pendiente"
    $update = $db->prepare("UPDATE ventas SET estado = 'Pendiente' WHERE codigo_venta = ?");
    $update->execute([$codigo_venta]);

    // 🔁 (Opcional) Restaurar stock si lo usas
    $detalle = $db->prepare("
        SELECT id_producto, cantidad 
        FROM detalle_ventas 
        WHERE codigo_venta = ?
    ");
    $detalle->execute([$codigo_venta]);

    while ($item = $detalle->fetch(PDO::FETCH_ASSOC)) {
        $restock = $db->prepare("
            UPDATE inventario 
            SET stock = stock - ? 
            WHERE id_producto = ?
        ");
        $restock->execute([$item['cantidad'], $item['id_producto']]);
    }

    // Redirigir de nuevo a la lista de ventas
    header("Location: index.php?page=ventas");
    exit;

} catch (PDOException $e) {
    // En caso de error, redirige sin mostrar mensajes (según tu preferencia)
    header("Location: index.php?page=ventas");
    exit;
}