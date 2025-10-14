<?php
// app/views/compras/marcar_compra_pagada.php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../app/controllers/CompraController.php';

// ✅ Validar si viene el ID de la compra
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?page=compras");
    exit;
}

$id_compra = intval($_GET['id']);

try {
    $compraController = new CompraController($db);

    // ✅ Cambiar el estado de la compra a "Pagada"
    $compraController->actualizarEstado($id_compra, 'Pagada');

    // 🔁 Redirigir al listado de compras
    header("Location: index.php?page=compras");
    exit;
    
} catch (Exception $e) {
    error_log("Error al marcar compra como pagada: " . $e->getMessage());
    header("Location: index.php?page=compras");
    exit;
}
?>