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
    $tipo_reporte = $_GET['tipo'] ?? 'ventas';
    $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
    $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

    $reporteController = new ReporteController($db);
    $ventas = $reporteController->generarReporteVentas($fecha_inicio, $fecha_fin);
    $productosMasVendidos = $reporteController->getProductosMasVendidos(10);

    // Calcular estadísticas
    $ventasActivas = array_filter($ventas, fn($v) => $v['estado'] != 'Anulada');
    $totalVentas = count($ventasActivas);
    $ingresosTotales = array_sum(array_column($ventasActivas, 'total_venta'));
    $ventasPagadas = count(array_filter($ventasActivas, fn($v) => $v['estado'] == 'Pagada'));
    $ventasPendientes = count(array_filter($ventasActivas, fn($v) => $v['estado'] == 'Pendiente'));
    $ventasAnuladas = count(array_filter($ventas, fn($v) => $v['estado'] == 'Anulada'));
    $promedioVenta = $totalVentas > 0 ? $ingresosTotales / $totalVentas : 0;

    // Fecha actual
    $fecha_actual = date('d-m-Y');
    $fecha_hora_actual = date('d/m/Y h:i A');

    // LIMPIAR CUALQUIER SALIDA PREVIA
    ob_clean();

    // Configurar headers para FORZAR descarga
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Reporte de Ventas ' . $fecha_actual . '.xls"');
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
    <title>Reporte de Ventas - Stock Nexus</title>
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
            <div style="font-size: 16px; color: #666;">Reporte de Ventas</div>
        </td>
        <td width="250" style="vertical-align: top; text-align: right;">
            <table cellpadding="3" style="background-color: #f8f9fa; border: 1px solid #ddd;">
                <tr><td colspan="2" style="font-weight: bold; background-color: #003366; color: white; text-align: center;">FILTROS APLICADOS</td></tr>
                <tr><td><strong>Período:</strong></td><td><?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></td></tr>
                <tr><td><strong>Generado:</strong></td><td><?= $fecha_hora_actual ?></td></tr>
                <tr><td><strong>Total Registros:</strong></td><td><?= count($ventas) ?> ventas</td></tr>
            </table>
        </td>
    </tr>
</table>

<br>

<!-- ESTADÍSTICAS PRINCIPALES -->
<table width="100%" cellpadding="8" cellspacing="5">
    <tr>
        <td colspan="6" class="subtitle header-bg text-center">RESUMEN ESTADÍSTICO</td>
    </tr>
    <tr>
        <td width="16%" style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; text-align: center;">
            <div style="font-size: 12px; color: #666;">TOTAL VENTAS</div>
            <div style="font-size: 20px; font-weight: bold; color: #007bff;"><?= $totalVentas ?></div>
            <div style="font-size: 10px; color: #999;">Activas</div>
        </td>
        <td width="16%" style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; text-align: center;">
            <div style="font-size: 12px; color: #666;">INGRESOS TOTALES</div>
            <div style="font-size: 18px; font-weight: bold; color: #28a745;">$<?= number_format($ingresosTotales, 2) ?></div>
            <div style="font-size: 10px; color: #999;">Excl. anuladas</div>
        </td>
        <td width="16%" style="background-color: #28a745; color: white; padding: 8px; text-align: center;">
            <div style="font-size: 12px;">VENTAS PAGADAS</div>
            <div style="font-size: 20px; font-weight: bold;"><?= $ventasPagadas ?></div>
            <div style="font-size: 10px;"><?= $totalVentas > 0 ? number_format(($ventasPagadas/$totalVentas)*100, 1) : 0 ?>%</div>
        </td>
        <td width="16%" style="background-color: #ffc107; color: black; padding: 8px; text-align: center;">
            <div style="font-size: 12px;">VENTAS PENDIENTES</div>
            <div style="font-size: 20px; font-weight: bold;"><?= $ventasPendientes ?></div>
            <div style="font-size: 10px;"><?= $totalVentas > 0 ? number_format(($ventasPendientes/$totalVentas)*100, 1) : 0 ?>%</div>
        </td>
        <td width="16%" style="background-color: #dc3545; color: white; padding: 8px; text-align: center;">
            <div style="font-size: 12px;">VENTAS ANULADAS</div>
            <div style="font-size: 20px; font-weight: bold;"><?= $ventasAnuladas ?></div>
            <div style="font-size: 10px;">Excluidas</div>
        </td>
        <td width="16%" style="background-color: #17a2b8; color: white; padding: 8px; text-align: center;">
            <div style="font-size: 12px;">PROMEDIO/VENTA</div>
            <div style="font-size: 18px; font-weight: bold;">$<?= number_format($promedioVenta, 2) ?></div>
            <div style="font-size: 10px;">Ticket promedio</div>
        </td>
    </tr>
</table>

<br>

<!-- DETALLE DE VENTAS -->
<table width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid #ddd;">
    <tr>
        <td colspan="7" class="subtitle header-bg text-center">DETALLE DE VENTAS</td>
    </tr>
    <tr style="background-color: #17a2b8; color: white;">
        <th width="12%" class="text-center">CÓDIGO</th>
        <th width="12%" class="text-center">FECHA</th>
        <th width="25%" class="text-center">CLIENTE</th>
        <th width="12%" class="text-center">TOTAL</th>
        <th width="15%" class="text-center">MÉTODO PAGO</th>
        <th width="12%" class="text-center">ESTADO</th>
        <th width="12%" class="text-center">TIPO</th>
    </tr>
    <?php foreach ($ventas as $venta): ?>
    <tr style="<?= $venta['estado'] == 'Anulada' ? 'background-color: #f8d7da;' : ($venta['estado'] == 'Pendiente' ? 'background-color: #fff3cd;' : 'background-color: #d4edda;') ?>">
        <td class="text-center" style="border: 1px solid #ddd;"><?= $venta['codigo_venta'] ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= date('d/m/Y', strtotime($venta['fecha_venta'])) ?></td>
        <td class="text-left" style="border: 1px solid #ddd;"><?= $venta['nombre_cliente'] ?: 'Cliente General' ?></td>
        <td class="text-right" style="border: 1px solid #ddd; font-weight: bold;">$<?= number_format($venta['total_venta'], 2) ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= $venta['metodo_pago'] ?></td>
        <td class="text-center" style="border: 1px solid #ddd; color: <?= $venta['estado'] == 'Anulada' ? '#dc3545' : ($venta['estado'] == 'Pendiente' ? '#856404' : '#155724') ?>; font-weight: bold;"><?= $venta['estado'] ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= $venta['estado'] != 'Anulada' ? 'Activa' : 'Anulada' ?></td>
    </tr>
    <?php endforeach; ?>
    
    <?php if (empty($ventas)): ?>
    <tr>
        <td colspan="7" class="text-center" style="border: 1px solid #ddd; padding: 20px; color: #666; font-style: italic;">
            No hay ventas registradas en este período
        </td>
    </tr>
    <?php endif; ?>
</table>

<br>

<!-- PRODUCTOS MÁS VENDIDOS -->
<?php if (!empty($productosMasVendidos)): ?>
<table width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid #ddd;">
    <tr>
        <td colspan="3" class="subtitle header-bg text-center">PRODUCTOS MÁS VENDIDOS</td>
    </tr>
    <tr style="background-color: #28a745; color: white;">
        <th width="5%" class="text-center">#</th>
        <th width="70%" class="text-center">PRODUCTO</th>
        <th width="25%" class="text-center">CANTIDAD VENDIDA</th>
    </tr>
    <?php $topCount = 1; ?>
    <?php foreach ($productosMasVendidos as $producto): ?>
    <tr style="<?= $topCount <= 3 ? 'background-color: #e8f5e8;' : '' ?>">
        <td class="text-center" style="border: 1px solid #ddd;"><?= $topCount ?></td>
        <td class="text-left" style="border: 1px solid #ddd;"><?= $producto['nombre_producto'] ?></td>
        <td class="text-center" style="border: 1px solid #ddd; font-weight: bold; color: #28a745;"><?= $producto['total_vendido'] ?> unidades</td>
    </tr>
    <?php $topCount++; ?>
    <?php endforeach; ?>
</table>
<?php endif; ?>

    <!-- PIE DE PÁGINA -->
    <table width="100%" cellpadding="5" style="margin-top: 20px; border-top: 2px solid #003366;">
        <tr>
            <td class="text-center" style="color: #666; font-size: 10px;">
                Stock Nexus © <?= date('Y') ?> - Reporte generado el <?= $fecha_hora_actual ?><br>
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