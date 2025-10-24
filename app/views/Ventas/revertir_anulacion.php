<?php
// app/views/ventas/revertir_anulacion.php

require_once __DIR__ . '/../../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Solo administradores pueden revertir
if (!isset($_SESSION['rol']) || stripos(trim($_SESSION['rol']), 'admin') === false) {
    header("Location: index.php?page=ventas");
    exit;
}

// ✅ Validar código de venta recibido
if (!isset($_GET['codigo']) || empty($_GET['codigo'])) {
    header("Location: index.php?page=ventas");
    exit;
}

$codigo_venta = trim($_GET['codigo']);

try {
    $database = new Database();
    $db = $database->getConnection();

    // 🔍 Buscar id_venta usando el código
    $stmt = $db->prepare("SELECT id_venta, estado FROM ventas WHERE codigo_venta = ?");
    $stmt->execute([$codigo_venta]);
    $venta = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si no existe o no está anulada → salir
    if (!$venta || $venta['estado'] !== 'Anulada') {
        header("Location: index.php?page=ventas");
        exit;
    }

    $id_venta = $venta['id_venta'];

    // ✅ Cambiar el estado de la venta a "Pendiente"
    $update = $db->prepare("UPDATE ventas SET estado = 'Pendiente' WHERE id_venta = ?");
    $update->execute([$id_venta]);

    // 🔁 (Opcional) Reajustar stock si se desea
    $detalle = $db->prepare("SELECT id_producto, cantidad FROM detalle_ventas WHERE id_venta = ?");
    $detalle->execute([$id_venta]);

    while ($item = $detalle->fetch(PDO::FETCH_ASSOC)) {
        // Al revertir una anulación, los productos vuelven a salir del inventario
        $restock = $db->prepare("UPDATE inventario SET stock = stock - ? WHERE id_producto = ?");
        $restock->execute([$item['cantidad'], $item['id_producto']]);
    }

    // 🔁 Volver al listado
    header("Location: index.php?page=ventas#historial_ventas");
    exit;

} catch (PDOException $e) {
    // En caso de error, redirigir sin mensajes
    header("Location: index.php?page=ventas#historial_ventas");
    exit;
}