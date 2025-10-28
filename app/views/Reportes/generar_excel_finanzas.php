<?php
// --- Incluir conexión y controlador ---
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../controllers/FinanzaController.php';

// Configurar zona horaria
date_default_timezone_set('America/Bogota');

// VERIFICAR QUE NO HAYA SALIDA ANTES DE LOS HEADERS
if (ob_get_level()) ob_end_clean();

try {
    // --- Obtener datos del reporte ---
    $tipo_reporte = $_GET['tipo'] ?? 'finanzas';
    $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
    $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

    // Crear instancia del controlador
    $finanzaController = new FinanzaController($db);
    $resumen = $finanzaController->getResumenFinanciero();
    $ingresosVsEgresos = $finanzaController->getIngresosVsEgresos(12);

    // Obtener datos del período seleccionado - CORREGIDO: usar alias de tablas
    $queryVentasPeriodo = "
        SELECT 
            v.codigo_venta,
            v.fecha_venta,
            v.total_venta,
            v.metodo_pago,
            v.estado,
            COALESCE(c.nombre_cliente, 'Cliente General') as nombre_cliente
        FROM ventas v
        LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
        WHERE v.fecha_venta BETWEEN :fecha_inicio AND :fecha_fin
        ORDER BY v.fecha_venta DESC
    ";
    $stmtVentas = $db->prepare($queryVentasPeriodo);
    $stmtVentas->execute([
        ':fecha_inicio' => $fecha_inicio . ' 00:00:00',
        ':fecha_fin' => $fecha_fin . ' 23:59:59'
    ]);
    $ventasPeriodo = $stmtVentas->fetchAll(PDO::FETCH_ASSOC);

    // CORREGIDO: usar alias de tablas
    $queryComprasPeriodo = "
        SELECT 
            c.codigo_compra,
            c.fecha_compra,
            c.total_compra,
            c.estado,
            COALESCE(p.nombre_proveedor, 'Proveedor General') as nombre_proveedor
        FROM compras c
        LEFT JOIN proveedores p ON c.id_proveedor = p.id_proveedor
        WHERE c.fecha_compra BETWEEN :fecha_inicio AND :fecha_fin
        ORDER BY c.fecha_compra DESC
    ";
    $stmtCompras = $db->prepare($queryComprasPeriodo);
    $stmtCompras->execute([
        ':fecha_inicio' => $fecha_inicio . ' 00:00:00',
        ':fecha_fin' => $fecha_fin . ' 23:59:59'
    ]);
    $comprasPeriodo = $stmtCompras->fetchAll(PDO::FETCH_ASSOC);

    // Métodos de pago del período
    $queryMetodosPago = "
        SELECT 
            metodo_pago,
            COUNT(*) as cantidad,
            SUM(total_venta) as total
        FROM ventas 
        WHERE estado = 'Pagada' 
        AND fecha_venta BETWEEN :fecha_inicio AND :fecha_fin
        GROUP BY metodo_pago
        ORDER BY total DESC
    ";
    $stmtMetodos = $db->prepare($queryMetodosPago);
    $stmtMetodos->execute([
        ':fecha_inicio' => $fecha_inicio . ' 00:00:00',
        ':fecha_fin' => $fecha_fin . ' 23:59:59'
    ]);
    $metodosPago = $stmtMetodos->fetchAll(PDO::FETCH_ASSOC);

    // Calcular estadísticas del período
    $ingresosPeriodo = array_sum(array_column(
        array_filter($ventasPeriodo, fn($v) => $v['estado'] == 'Pagada'), 
        'total_venta'
    ));
    
    $egresosPeriodo = array_sum(array_column(
        array_filter($comprasPeriodo, fn($c) => $c['estado'] == 'Pagada'), 
        'total_compra'
    ));
    
    $utilidadPeriodo = $ingresosPeriodo - $egresosPeriodo;
    
    $ventasPagadasPeriodo = count(array_filter($ventasPeriodo, fn($v) => $v['estado'] == 'Pagada'));
    $ventasPendientesPeriodo = count(array_filter($ventasPeriodo, fn($v) => $v['estado'] == 'Pendiente'));
    $ventasAnuladasPeriodo = count(array_filter($ventasPeriodo, fn($v) => $v['estado'] == 'Anulada'));

    // Calcular margen de utilidad de forma segura
    $margenUtilidad = 0;
    if ($ingresosPeriodo > 0) {
        $margenUtilidad = ($utilidadPeriodo / $ingresosPeriodo) * 100;
    }

    // Fecha actual
    $fecha_actual = date('d-m-Y');
    $fecha_hora_actual = date('d/m/Y h:i A');

    // LIMPIAR CUALQUIER SALIDA PREVIA
    ob_clean();

    // Configurar headers para FORZAR descarga
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Reporte Financiero ' . $fecha_actual . '.xls"');
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
    <title>Reporte Financiero - Stock Nexus</title>
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
        .primary-bg { background-color: #4e73df; color: white; }
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
            <div style="font-size: 16px; color: #666;">Reporte Financiero</div>
        </td>
        <td width="250" style="vertical-align: top; text-align: right;">
            <table cellpadding="3" style="background-color: #f8f9fa; border: 1px solid #ddd;">
                <tr><td colspan="2" style="font-weight: bold; background-color: #003366; color: white; text-align: center;">FILTROS APLICADOS</td></tr>
                <tr><td><strong>Período:</strong></td><td><?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></td></tr>
                <tr><td><strong>Generado:</strong></td><td><?= $fecha_hora_actual ?></td></tr>
                <tr><td><strong>Total Registros:</strong></td><td><?= count($ventasPeriodo) + count($comprasPeriodo) ?> movimientos</td></tr>
            </table>
        </td>
    </tr>
</table>

<br>

<!-- ESTADÍSTICAS PRINCIPALES -->
<table width="100%" cellpadding="8" cellspacing="5">
    <tr>
        <td colspan="4" class="subtitle header-bg text-center">RESUMEN FINANCIERO DEL PERÍODO</td>
    </tr>
    <tr>
        <td width="25%" style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; text-align: center;">
            <div style="font-size: 12px; color: #666;">INGRESOS TOTALES</div>
            <div style="font-size: 20px; font-weight: bold; color: #28a745;">$<?= number_format($ingresosPeriodo, 2) ?></div>
            <div style="font-size: 10px; color: #999;">Ventas Pagadas</div>
        </td>
        <td width="25%" style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; text-align: center;">
            <div style="font-size: 12px; color: #666;">EGRESOS TOTALES</div>
            <div style="font-size: 20px; font-weight: bold; color: #e74a3b;">$<?= number_format($egresosPeriodo, 2) ?></div>
            <div style="font-size: 10px; color: #999;">Compras Pagadas</div>
        </td>
        <td width="25%" style="background-color: #28a745; color: white; padding: 8px; text-align: center;">
            <div style="font-size: 12px;">UTILIDAD NETA</div>
            <div style="font-size: 20px; font-weight: bold;">$<?= number_format($utilidadPeriodo, 2) ?></div>
            <div style="font-size: 10px;">Ingresos - Egresos</div>
        </td>
        <td width="25%" style="background-color: #ffc107; color: black; padding: 8px; text-align: center;">
            <div style="font-size: 12px;">MARGEN DE UTILIDAD</div>
            <div style="font-size: 20px; font-weight: bold;"><?= number_format($margenUtilidad, 1) ?>%</div>
            <div style="font-size: 10px;">Rentabilidad</div>
        </td>
    </tr>
</table>

<br>

<!-- DETALLE DE VENTAS -->
<table width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid #ddd;">
    <tr>
        <td colspan="6" class="subtitle header-bg text-center">DETALLE DE VENTAS (<?= count($ventasPeriodo) ?> REGISTROS)</td>
    </tr>
    <tr style="background-color: #17a2b8; color: white;">
        <th width="12%" class="text-center">CÓDIGO</th>
        <th width="12%" class="text-center">FECHA</th>
        <th width="25%" class="text-center">CLIENTE</th>
        <th width="15%" class="text-center">TOTAL</th>
        <th width="18%" class="text-center">MÉTODO PAGO</th>
        <th width="18%" class="text-center">ESTADO</th>
    </tr>
    <?php foreach ($ventasPeriodo as $venta): ?>
    <tr style="<?= $venta['estado'] == 'Anulada' ? 'background-color: #f8d7da;' : ($venta['estado'] == 'Pendiente' ? 'background-color: #fff3cd;' : 'background-color: #d4edda;') ?>">
        <td class="text-center" style="border: 1px solid #ddd;"><?= htmlspecialchars($venta['codigo_venta']) ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= date('d/m/Y', strtotime($venta['fecha_venta'])) ?></td>
        <td class="text-left" style="border: 1px solid #ddd;"><?= htmlspecialchars($venta['nombre_cliente']) ?></td>
        <td class="text-right" style="border: 1px solid #ddd; font-weight: bold; color: #28a745;">$<?= number_format($venta['total_venta'], 2) ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= htmlspecialchars($venta['metodo_pago']) ?></td>
        <td class="text-center" style="border: 1px solid #ddd; color: <?= $venta['estado'] == 'Anulada' ? '#dc3545' : ($venta['estado'] == 'Pendiente' ? '#856404' : '#155724') ?>; font-weight: bold;"><?= htmlspecialchars($venta['estado']) ?></td>
    </tr>
    <?php endforeach; ?>
    
    <?php if (empty($ventasPeriodo)): ?>
    <tr>
        <td colspan="6" class="text-center" style="border: 1px solid #ddd; padding: 20px; color: #666; font-style: italic;">
            No hay ventas registradas en este período
        </td>
    </tr>
    <?php endif; ?>
</table>

<br>

<!-- DETALLE DE COMPRAS -->
<table width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid #ddd;">
    <tr>
        <td colspan="5" class="subtitle header-bg text-center">DETALLE DE COMPRAS (<?= count($comprasPeriodo) ?> REGISTROS)</td>
    </tr>
    <tr style="background-color: #6f42c1; color: white;">
        <th width="15%" class="text-center">CÓDIGO</th>
        <th width="15%" class="text-center">FECHA</th>
        <th width="30%" class="text-center">PROVEEDOR</th>
        <th width="20%" class="text-center">TOTAL</th>
        <th width="20%" class="text-center">ESTADO</th>
    </tr>
    <?php foreach ($comprasPeriodo as $compra): ?>
    <tr style="<?= $compra['estado'] == 'Anulada' ? 'background-color: #f8d7da;' : ($compra['estado'] == 'Pendiente' ? 'background-color: #fff3cd;' : 'background-color: #d4edda;') ?>">
        <td class="text-center" style="border: 1px solid #ddd;"><?= htmlspecialchars($compra['codigo_compra']) ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= date('d/m/Y', strtotime($compra['fecha_compra'])) ?></td>
        <td class="text-left" style="border: 1px solid #ddd;"><?= htmlspecialchars($compra['nombre_proveedor']) ?></td>
        <td class="text-right" style="border: 1px solid #ddd; font-weight: bold; color: #e74a3b;">$<?= number_format($compra['total_compra'], 2) ?></td>
        <td class="text-center" style="border: 1px solid #ddd; color: <?= $compra['estado'] == 'Anulada' ? '#dc3545' : ($compra['estado'] == 'Pendiente' ? '#856404' : '#155724') ?>; font-weight: bold;"><?= htmlspecialchars($compra['estado']) ?></td>
    </tr>
    <?php endforeach; ?>
    
    <?php if (empty($comprasPeriodo)): ?>
    <tr>
        <td colspan="5" class="text-center" style="border: 1px solid #ddd; padding: 20px; color: #666; font-style: italic;">
            No hay compras registradas en este período
        </td>
    </tr>
    <?php endif; ?>
</table>

<br>

<!-- MÉTODOS DE PAGO -->
<?php if (!empty($metodosPago)): ?>
<table width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid #ddd;">
    <tr>
        <td colspan="3" class="subtitle header-bg text-center">DISTRIBUCIÓN POR MÉTODOS DE PAGO</td>
    </tr>
    <tr style="background-color: #4e73df; color: white;">
        <th width="40%" class="text-center">MÉTODO DE PAGO</th>
        <th width="20%" class="text-center">CANTIDAD</th>
        <th width="40%" class="text-center">TOTAL</th>
    </tr>
    <?php foreach ($metodosPago as $metodo): ?>
    <tr>
        <td class="text-center" style="border: 1px solid #ddd;"><?= htmlspecialchars($metodo['metodo_pago']) ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= htmlspecialchars($metodo['cantidad']) ?></td>
        <td class="text-right" style="border: 1px solid #ddd; font-weight: bold; color: #28a745;">$<?= number_format($metodo['total'], 2) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<!-- RESUMEN ESTADÍSTICO -->
<br>
<table width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid #ddd;">
    <tr>
        <td colspan="4" class="subtitle header-bg text-center">RESUMEN ESTADÍSTICO</td>
    </tr>
    <tr style="background-color: #36b9cc; color: white;">
        <th width="25%" class="text-center">INDICADOR</th>
        <th width="25%" class="text-center">CANTIDAD</th>
        <th width="25%" class="text-center">PORCENTAJE</th>
        <th width="25%" class="text-center">VALOR</th>
    </tr>
    <tr>
        <td class="text-center" style="border: 1px solid #ddd;">Ventas Pagadas</td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= $ventasPagadasPeriodo ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= count($ventasPeriodo) > 0 ? number_format(($ventasPagadasPeriodo/count($ventasPeriodo))*100, 1) : 0 ?>%</td>
        <td class="text-right" style="border: 1px solid #ddd; color: #28a745;">$<?= number_format($ingresosPeriodo, 2) ?></td>
    </tr>
    <tr>
        <td class="text-center" style="border: 1px solid #ddd;">Ventas Pendientes</td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= $ventasPendientesPeriodo ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= count($ventasPeriodo) > 0 ? number_format(($ventasPendientesPeriodo/count($ventasPeriodo))*100, 1) : 0 ?>%</td>
        <td class="text-right" style="border: 1px solid #ddd; color: #ffc107;">-</td>
    </tr>
    <tr>
        <td class="text-center" style="border: 1px solid #ddd;">Ventas Anuladas</td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= $ventasAnuladasPeriodo ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= count($ventasPeriodo) > 0 ? number_format(($ventasAnuladasPeriodo/count($ventasPeriodo))*100, 1) : 0 ?>%</td>
        <td class="text-right" style="border: 1px solid #ddd; color: #dc3545;">-</td>
    </tr>
    <tr>
        <td class="text-center" style="border: 1px solid #ddd;">Total Compras</td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= count($comprasPeriodo) ?></td>
        <td class="text-center" style="border: 1px solid #ddd;">100%</td>
        <td class="text-right" style="border: 1px solid #ddd; color: #e74a3b;">$<?= number_format($egresosPeriodo, 2) ?></td>
    </tr>
</table>

    <!-- PIE DE PÁGINA -->
    <table width="100%" cellpadding="5" style="margin-top: 20px; border-top: 2px solid #003366;">
        <tr>
            <td class="text-center" style="color: #666; font-size: 10px;">
                Stock Nexus © <?= date('Y') ?> - Reporte Financiero generado el <?= $fecha_hora_actual ?><br>
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