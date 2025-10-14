<?php
// app/views/ventas/marcar_venta_pagada.php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../app/controllers/VentaController.php';

// Validar si viene el ID de la venta
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?page=ventas");
    exit;
}

$id_venta = intval($_GET['id']);

try {
    $ventaController = new VentaController($db);

    // Cambiar el estado a "Pagada"
    $ventaController->actualizarEstado($id_venta, 'Pagada');

    // Redirigir de nuevo al listado de ventas
    header("Location: index.php?page=ventas");
    exit;
    
} catch (Exception $e) {
    error_log("Error al marcar venta como pagada: " . $e->getMessage());
    header("Location: index.php?page=ventas");
    exit;
}
?>