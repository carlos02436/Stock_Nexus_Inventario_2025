<?php
// --- Incluir conexión y controlador ---
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../controllers/ReporteController.php';

// Configurar zona horaria
date_default_timezone_set('America/Bogota');

// VERIFICAR QUE NO HAYA SALIDA ANTES DE LOS HEADERS
if (ob_get_level()) ob_end_clean();

try {
    // --- Obtener datos del reporte ---
    $tipo_reporte = $_GET['tipo'] ?? 'inventario';
    $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
    $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

    $reporteController = new ReporteController($db);
    $productos = $reporteController->generarReporteInventario();
    $productosMasVendidos = $reporteController->getProductosMasVendidos(10);

    // Calcular estadísticas del inventario
    $productosActivos = array_filter($productos, fn($p) => true);
    $totalProductos = count($productosActivos);
    $valorTotalInventario = array_sum(array_column($productosActivos, 'valor_inventario'));
    $productosBajoStock = count(array_filter($productosActivos, fn($p) => $p['estado_stock'] == 'BAJO'));
    $productosMedioStock = count(array_filter($productosActivos, fn($p) => $p['estado_stock'] == 'MEDIO'));
    $productosNormalStock = count(array_filter($productosActivos, fn($p) => $p['estado_stock'] == 'NORMAL'));
    $productosSinStock = count(array_filter($productosActivos, fn($p) => $p['stock_actual'] <= 0));

    // Fecha actual
    $fecha_actual = date('d-m-Y');
    $fecha_hora_actual = date('d/m/Y h:i A');

    // LIMPIAR CUALQUIER SALIDA PREVIA
    ob_clean();

    // Configurar headers para FORZAR descarga
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Reporte de Inventario ' . $fecha_actual . '.xls"');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Expires: 0');

    // Iniciar buffer de salida
    ob_start();
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Reporte de Inventario - Stock Nexus</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        td { font-family: Arial, sans-serif; font-size: 11px; padding: 4px; }
        th { font-family: Arial, sans-serif; font-size: 11px; font-weight: bold; padding: 6px; }
        .title { font-size: 18px; font-weight: bold; color: #003366; }
        .subtitle { font-size: 14px; font-weight: bold; color: #003366; }
        .header-bg { background-color: #003366; color: white; }
        .success-bg { background-color: #28a745; color: white; }
        .warning-bg { background-color: #ffc107; color: black; }
        .info-bg { background-color: #17a2b8; color: white; }
        .danger-bg { background-color: #dc3545; color: white; }
        .primary-bg { background-color: #007bff; color: white; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .border { border: 1px solid #ddd; }
    </style>
</head>
<body>

<!-- ENCABEZADO CON LOGO Y FILTROS -->
<table width="100%" cellpadding="5" cellspacing="0">
    <tr>
        <td width="80" style="vertical-align: middle;">
            <div style="background-color: #003366; color: white; width: 70px; height: 70px; text-align: center; line-height: 70px; font-weight: bold; font-size: 14px;">
                STOCK<br>NEXUS
            </div>
        </td>
        <td style="vertical-align: middle;">
            <div class="title">STOCK NEXUS</div>
            <div style="font-size: 16px; color: #666;">Reporte de Inventario</div>
        </td>
        <td width="250" style="vertical-align: top; text-align: right;">
            <table cellpadding="3" style="background-color: #f8f9fa; border: 1px solid #ddd;">
                <tr><td colspan="2" style="font-weight: bold; background-color: #003366; color: white; text-align: center;">INFORMACIÓN DEL REPORTE</td></tr>
                <tr><td><strong>Generado:</strong></td><td><?= $fecha_hora_actual ?></td></tr>
                <tr><td><strong>Total Productos:</strong></td><td><?= count($productos) ?> registros</td></tr>
                <tr><td><strong>Estado:</strong></td><td>Inventario Actual</td></tr>
            </table>
        </td>
    </tr>
</table>

<br>

<!-- ESTADÍSTICAS PRINCIPALES -->
<table width="100%" cellpadding="8" cellspacing="5">
    <tr>
        <td colspan="6" class="subtitle header-bg text-center">RESUMEN ESTADÍSTICO DEL INVENTARIO</td>
    </tr>
    <tr>
        <td width="16%" style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; text-align: center;">
            <div style="font-size: 12px; color: #666;">TOTAL PRODUCTOS</div>
            <div style="font-size: 20px; font-weight: bold; color: #007bff;"><?= $totalProductos ?></div>
            <div style="font-size: 10px; color: #999;">En inventario</div>
        </td>
        <td width="16%" style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; text-align: center;">
            <div style="font-size: 12px; color: #666;">VALOR INVENTARIO</div>
            <div style="font-size: 18px; font-weight: bold; color: #28a745;">$<?= number_format($valorTotalInventario, 2) ?></div>
            <div style="font-size: 10px; color: #999;">Valor total</div>
        </td>
        <td width="16%" style="background-color: #ffc107; color: black; padding: 8px; text-align: center;">
            <div style="font-size: 12px;">BAJO STOCK</div>
            <div style="font-size: 20px; font-weight: bold;"><?= $productosBajoStock ?></div>
            <div style="font-size: 10px;"><?= $totalProductos > 0 ? number_format(($productosBajoStock/$totalProductos)*100, 1) : 0 ?>%</div>
        </td>
        <td width="16%" style="background-color: #17a2b8; color: white; padding: 8px; text-align: center;">
            <div style="font-size: 12px;">MEDIO STOCK</div>
            <div style="font-size: 20px; font-weight: bold;"><?= $productosMedioStock ?></div>
            <div style="font-size: 10px;"><?= $totalProductos > 0 ? number_format(($productosMedioStock/$totalProductos)*100, 1) : 0 ?>%</div>
        </td>
        <td width="16%" style="background-color: #28a745; color: white; padding: 8px; text-align: center;">
            <div style="font-size: 12px;">NORMAL STOCK</div>
            <div style="font-size: 20px; font-weight: bold;"><?= $productosNormalStock ?></div>
            <div style="font-size: 10px;"><?= $totalProductos > 0 ? number_format(($productosNormalStock/$totalProductos)*100, 1) : 0 ?>%</div>
        </td>
        <td width="16%" style="background-color: #dc3545; color: white; padding: 8px; text-align: center;">
            <div style="font-size: 12px;">SIN STOCK</div>
            <div style="font-size: 20px; font-weight: bold;"><?= $productosSinStock ?></div>
            <div style="font-size: 10px;"><?= $totalProductos > 0 ? number_format(($productosSinStock/$totalProductos)*100, 1) : 0 ?>%</div>
        </td>
    </tr>
</table>

<br>

<!-- DETALLE DE INVENTARIO -->
<table width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid #ddd;">
    <tr>
        <td colspan="9" class="subtitle header-bg text-center">DETALLE DE PRODUCTOS EN INVENTARIO</td>
    </tr>
    <tr style="background-color: #17a2b8; color: white;">
        <th width="10%" class="text-center">CÓDIGO</th>
        <th width="20%" class="text-center">NOMBRE</th>
        <th width="15%" class="text-center">CATEGORÍA</th>
        <th width="10%" class="text-center">STOCK ACTUAL</th>
        <th width="10%" class="text-center">STOCK MÍNIMO</th>
        <th width="10%" class="text-center">PRECIO COMPRA</th>
        <th width="10%" class="text-center">PRECIO VENTA</th>
        <th width="10%" class="text-center">VALOR INVENTARIO</th>
        <th width="10%" class="text-center">ESTADO STOCK</th>
    </tr>
    <?php foreach ($productos as $producto): 
        // Determinar estilo según estado de stock
        $bgColor = '';
        $textColor = '';
        if ($producto['stock_actual'] <= 0) {
            $bgColor = '#f8d7da';
            $textColor = '#721c24';
            $estadoStock = 'SIN STOCK';
        } elseif ($producto['estado_stock'] == 'BAJO') {
            $bgColor = '#fff3cd';
            $textColor = '#856404';
            $estadoStock = 'BAJO';
        } elseif ($producto['estado_stock'] == 'MEDIO') {
            $bgColor = '#d1ecf1';
            $textColor = '#0c5460';
            $estadoStock = 'MEDIO';
        } else {
            $bgColor = '#d4edda';
            $textColor = '#155724';
            $estadoStock = 'NORMAL';
        }
    ?>
    <tr style="background-color: <?= $bgColor ?>;">
        <td class="text-center" style="border: 1px solid #ddd;"><?= $producto['codigo_producto'] ?></td>
        <td class="text-left" style="border: 1px solid #ddd;"><?= $producto['nombre_producto'] ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= $producto['nombre_categoria'] ?: 'Sin categoría' ?></td>
        <td class="text-center" style="border: 1px solid #ddd; font-weight: bold; color: <?= $textColor ?>;"><?= number_format($producto['stock_actual'], 2) ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= number_format($producto['stock_minimo'], 2) ?></td>
        <td class="text-right" style="border: 1px solid #ddd;">$<?= number_format($producto['precio_compra'], 2) ?></td>
        <td class="text-right" style="border: 1px solid #ddd; font-weight: bold; color: #28a745;">$<?= number_format($producto['precio_venta'], 2) ?></td>
        <td class="text-right" style="border: 1px solid #ddd; font-weight: bold; color: #007bff;">$<?= number_format($producto['valor_inventario'], 2) ?></td>
        <td class="text-center" style="border: 1px solid #ddd; font-weight: bold; color: <?= $textColor ?>;"><?= $estadoStock ?></td>
    </tr>
    <?php endforeach; ?>
    
    <?php if (empty($productos)): ?>
    <tr>
        <td colspan="9" class="text-center" style="border: 1px solid #ddd; padding: 20px; color: #666; font-style: italic;">
            No hay productos registrados en el inventario
        </td>
    </tr>
    <?php endif; ?>
</table>

<br>

<!-- PRODUCTOS MÁS VENDIDOS -->
<?php if (!empty($productosMasVendidos)): ?>
<table width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid #ddd;">
    <tr>
        <td colspan="4" class="subtitle header-bg text-center">PRODUCTOS MÁS VENDIDOS</td>
    </tr>
    <tr style="background-color: #28a745; color: white;">
        <th width="5%" class="text-center">#</th>
        <th width="60%" class="text-center">PRODUCTO</th>
        <th width="15%" class="text-center">CANTIDAD VENDIDA</th>
        <th width="20%" class="text-center">INGRESOS GENERADOS</th>
    </tr>
    <?php $topCount = 1; ?>
    <?php foreach ($productosMasVendidos as $producto): ?>
    <tr style="<?= $topCount <= 3 ? 'background-color: #e8f5e8;' : '' ?>">
        <td class="text-center" style="border: 1px solid #ddd;"><?= $topCount ?></td>
        <td class="text-left" style="border: 1px solid #ddd;"><?= $producto['nombre_producto'] ?></td>
        <td class="text-center" style="border: 1px solid #ddd; font-weight: bold; color: #28a745;"><?= $producto['total_vendido'] ?> unidades</td>
        <td class="text-right" style="border: 1px solid #ddd; font-weight: bold; color: #007bff;">$<?= number_format($producto['total_ingresos'], 2) ?></td>
    </tr>
    <?php $topCount++; ?>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<!-- RESUMEN POR CATEGORÍAS -->
<?php
// Calcular resumen por categorías
$categoriasCount = [];
$valorPorCategoria = [];
foreach ($productos as $producto) {
    $categoria = $producto['nombre_categoria'] ?: 'Sin categoría';
    if (!isset($categoriasCount[$categoria])) {
        $categoriasCount[$categoria] = 0;
        $valorPorCategoria[$categoria] = 0;
    }
    $categoriasCount[$categoria]++;
    $valorPorCategoria[$categoria] += $producto['valor_inventario'];
}
arsort($categoriasCount);
?>
<?php if (!empty($categoriasCount)): ?>
<br>
<table width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid #ddd;">
    <tr>
        <td colspan="4" class="subtitle header-bg text-center">RESUMEN POR CATEGORÍAS</td>
    </tr>
    <tr style="background-color: #6f42c1; color: white;">
        <th width="5%" class="text-center">#</th>
        <th width="55%" class="text-center">CATEGORÍA</th>
        <th width="20%" class="text-center">CANTIDAD PRODUCTOS</th>
        <th width="20%" class="text-center">VALOR INVENTARIO</th>
    </tr>
    <?php $catCount = 1; ?>
    <?php foreach ($categoriasCount as $categoria => $cantidad): ?>
    <tr>
        <td class="text-center" style="border: 1px solid #ddd;"><?= $catCount ?></td>
        <td class="text-left" style="border: 1px solid #ddd;"><?= htmlspecialchars($categoria) ?></td>
        <td class="text-center" style="border: 1px solid #ddd; font-weight: bold;"><?= $cantidad ?></td>
        <td class="text-right" style="border: 1px solid #ddd; font-weight: bold; color: #007bff;">$<?= number_format($valorPorCategoria[$categoria], 2) ?></td>
    </tr>
    <?php $catCount++; ?>
    <?php endforeach; ?>
</table>
<?php endif; ?>

    <!-- PIE DE PÁGINA -->
    <table width="100%" cellpadding="5" style="margin-top: 20px; border-top: 2px solid #003366;">
        <tr>
            <td class="text-center" style="color: #666; font-size: 10px;">
                Stock Nexus © <?= date('Y') ?> - Reporte de Inventario generado el <?= $fecha_hora_actual ?><br>
                Sistema de Gestión de Inventarios y Ventas
            </td>
        </tr>
    </table>

</body>
</html>
<?php
    // Enviar y limpiar buffer
    ob_end_flush();
    exit;

} catch (Exception $e) {
    // Limpiar todo en caso de error
    ob_end_clean();
    die("Error al generar el Excel: " . $e->getMessage());
}
?>