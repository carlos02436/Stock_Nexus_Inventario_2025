<?php
// --- Incluir conexión y controlador ---
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../controllers/CompraController.php';

// Configurar zona horaria
date_default_timezone_set('America/Bogota');

// VERIFICAR QUE NO HAYA SALIDA ANTES DE LOS HEADERS
if (ob_get_level()) ob_end_clean();

try {
    // --- Obtener datos del reporte ---
    $tipo_reporte = $_GET['tipo'] ?? 'compras';
    $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
    $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

    // Crear instancia del controlador
    $compraController = new CompraController($db);
    $compras = $compraController->listar();

    // Filtrar compras por período seleccionado
    $comprasPeriodo = array_filter($compras, function($compra) use ($fecha_inicio, $fecha_fin) {
        $fechaCompra = date('Y-m-d', strtotime($compra['fecha_compra']));
        return $fechaCompra >= $fecha_inicio && $fechaCompra <= $fecha_fin;
    });

    // Calcular estadísticas del período
    $totalCompras = count($comprasPeriodo);
    $montoTotal = array_sum(array_column($comprasPeriodo, 'total_compra'));
    $comprasPagadas = count(array_filter($comprasPeriodo, fn($c) => $c['estado'] == 'Pagada'));
    $comprasPendientes = count(array_filter($comprasPeriodo, fn($c) => $c['estado'] == 'Pendiente'));
    $comprasAnuladas = count(array_filter($comprasPeriodo, fn($c) => $c['estado'] == 'Anulada'));
    $promedioCompra = $totalCompras > 0 ? $montoTotal / $totalCompras : 0;

    // Obtener compras por proveedor
    $comprasPorProveedor = [];
    foreach ($comprasPeriodo as $compra) {
        $proveedor = $compra['nombre_proveedor'];
        if (!isset($comprasPorProveedor[$proveedor])) {
            $comprasPorProveedor[$proveedor] = [
                'cantidad' => 0,
                'monto_total' => 0
            ];
        }
        $comprasPorProveedor[$proveedor]['cantidad']++;
        $comprasPorProveedor[$proveedor]['monto_total'] += $compra['total_compra'];
    }
    arsort($comprasPorProveedor);

    // Fecha actual
    $fecha_actual = date('d-m-Y');
    $fecha_hora_actual = date('d/m/Y h:i A');

    // LIMPIAR CUALQUIER SALIDA PREVIA
    ob_clean();

    // Configurar headers para FORZAR descarga
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Reporte de Compras ' . $fecha_actual . '.xls"');
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
    <title>Reporte de Compras - Stock Nexus</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        td { font-family: Arial, sans-serif; font-size: 11px; padding: 4px; }
        th { font-family: Arial, sans-serif; font-size: 11px; font-weight: bold; padding: 6px; }
        .title { font-size: 18px; font-weight: bold; color: #003366; }
        .subtitle { font-size: 14px; font-weight: bold; color: #003366; }
        .header-bg { background-color: #003366; color: white; }
        .success-bg { background-color: #28a745; color: white; }
        .warning-bg { background-color: #ffc107; color: black; }
        .danger-bg { background-color: #dc3545; color: white; }
        .primary-bg { background-color: #4e73df; color: white; }
        .info-bg { background-color: #17a2b8; color: white; }
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
            <div style="font-size: 16px; color: #666;">Reporte de Compras</div>
        </td>
        <td width="250" style="vertical-align: top; text-align: right;">
            <table cellpadding="3" style="background-color: #f8f9fa; border: 1px solid #ddd;">
                <tr><td colspan="2" style="font-weight: bold; background-color: #003366; color: white; text-align: center;">FILTROS APLICADOS</td></tr>
                <tr><td><strong>Período:</strong></td><td><?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></td></tr>
                <tr><td><strong>Generado:</strong></td><td><?= $fecha_hora_actual ?></td></tr>
                <tr><td><strong>Total Compras:</strong></td><td><?= $totalCompras ?> registros</td></tr>
            </table>
        </td>
    </tr>
</table>

<br>

<!-- ESTADÍSTICAS PRINCIPALES -->
<table width="100%" cellpadding="8" cellspacing="5">
    <tr>
        <td colspan="4" class="subtitle header-bg text-center">RESUMEN ESTADÍSTICO DEL PERÍODO</td>
    </tr>
    <tr>
        <td width="25%" style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; text-align: center;">
            <div style="font-size: 12px; color: #666;">TOTAL COMPRAS</div>
            <div style="font-size: 20px; font-weight: bold; color: #4e73df;"><?= $totalCompras ?></div>
            <div style="font-size: 10px; color: #999;">En el período</div>
        </td>
        <td width="25%" style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; text-align: center;">
            <div style="font-size: 12px; color: #666;">MONTO TOTAL</div>
            <div style="font-size: 20px; font-weight: bold; color: #e74a3b;">$<?= number_format($montoTotal, 2) ?></div>
            <div style="font-size: 10px; color: #999;">Inversión total</div>
        </td>
        <td width="25%" style="background-color: #28a745; color: white; padding: 8px; text-align: center;">
            <div style="font-size: 12px;">COMPRAS PAGADAS</div>
            <div style="font-size: 20px; font-weight: bold;"><?= $comprasPagadas ?></div>
            <div style="font-size: 10px;"><?= $totalCompras > 0 ? number_format(($comprasPagadas/$totalCompras)*100, 1) : 0 ?>%</div>
        </td>
        <td width="25%" style="background-color: #17a2b8; color: white; padding: 8px; text-align: center;">
            <div style="font-size: 12px;">PROMEDIO/COMPRA</div>
            <div style="font-size: 20px; font-weight: bold;">$<?= number_format($promedioCompra, 2) ?></div>
            <div style="font-size: 10px;">Valor promedio</div>
        </td>
    </tr>
</table>

<br>

<!-- DETALLE DE COMPRAS -->
<table width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid #ddd;">
    <tr>
        <td colspan="5" class="subtitle header-bg text-center">DETALLE DE COMPRAS (<?= $totalCompras ?> REGISTROS)</td>
    </tr>
    <tr style="background-color: #17a2b8; color: white;">
        <th width="15%" class="text-center">CÓDIGO</th>
        <th width="25%" class="text-center">PROVEEDOR</th>
        <th width="15%" class="text-center">FECHA</th>
        <th width="20%" class="text-center">TOTAL</th>
        <th width="25%" class="text-center">ESTADO</th>
    </tr>
    <?php foreach ($comprasPeriodo as $compra): ?>
    <tr style="<?= $compra['estado'] == 'Anulada' ? 'background-color: #f8d7da;' : ($compra['estado'] == 'Pendiente' ? 'background-color: #fff3cd;' : 'background-color: #d4edda;') ?>">
        <td class="text-center" style="border: 1px solid #ddd;"><?= $compra['codigo_compra'] ?></td>
        <td class="text-left" style="border: 1px solid #ddd;"><?= $compra['nombre_proveedor'] ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= date('d/m/Y', strtotime($compra['fecha_compra'])) ?></td>
        <td class="text-right" style="border: 1px solid #ddd; font-weight: bold; color: #e74a3b;">$<?= number_format($compra['total_compra'], 2) ?></td>
        <td class="text-center" style="border: 1px solid #ddd; color: <?= $compra['estado'] == 'Anulada' ? '#dc3545' : ($compra['estado'] == 'Pendiente' ? '#856404' : '#155724') ?>; font-weight: bold;"><?= $compra['estado'] ?></td>
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

<!-- COMPRAS POR PROVEEDOR -->
<?php if (!empty($comprasPorProveedor)): ?>
<table width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid #ddd;">
    <tr>
        <td colspan="3" class="subtitle header-bg text-center">COMPRAS POR PROVEEDOR</td>
    </tr>
    <tr style="background-color: #6f42c1; color: white;">
        <th width="50%" class="text-center">PROVEEDOR</th>
        <th width="20%" class="text-center">CANTIDAD</th>
        <th width="30%" class="text-center">TOTAL</th>
    </tr>
    <?php foreach ($comprasPorProveedor as $proveedor => $datos): ?>
    <tr>
        <td class="text-left" style="border: 1px solid #ddd;"><?= htmlspecialchars($proveedor) ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= $datos['cantidad'] ?></td>
        <td class="text-right" style="border: 1px solid #ddd; font-weight: bold; color: #e74a3b;">$<?= number_format($datos['monto_total'], 2) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<!-- RESUMEN POR ESTADO -->
<br>
<table width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid #ddd;">
    <tr>
        <td colspan="4" class="subtitle header-bg text-center">DISTRIBUCIÓN POR ESTADO</td>
    </tr>
    <tr style="background-color: #36b9cc; color: white;">
        <th width="40%" class="text-center">ESTADO</th>
        <th width="20%" class="text-center">CANTIDAD</th>
        <th width="20%" class="text-center">PORCENTAJE</th>
        <th width="20%" class="text-center">MONTO TOTAL</th>
    </tr>
    <tr>
        <td class="text-center" style="border: 1px solid #ddd; color: #155724; font-weight: bold;">PAGADA</td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= $comprasPagadas ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= $totalCompras > 0 ? number_format(($comprasPagadas/$totalCompras)*100, 1) : 0 ?>%</td>
        <td class="text-right" style="border: 1px solid #ddd; color: #28a745;">
            $<?= number_format(array_sum(array_column(
                array_filter($comprasPeriodo, fn($c) => $c['estado'] == 'Pagada'), 
                'total_compra'
            )), 2) ?>
        </td>
    </tr>
    <tr>
        <td class="text-center" style="border: 1px solid #ddd; color: #856404; font-weight: bold;">PENDIENTE</td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= $comprasPendientes ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= $totalCompras > 0 ? number_format(($comprasPendientes/$totalCompras)*100, 1) : 0 ?>%</td>
        <td class="text-right" style="border: 1px solid #ddd; color: #ffc107;">
            $<?= number_format(array_sum(array_column(
                array_filter($comprasPeriodo, fn($c) => $c['estado'] == 'Pendiente'), 
                'total_compra'
            )), 2) ?>
        </td>
    </tr>
    <tr>
        <td class="text-center" style="border: 1px solid #ddd; color: #721c24; font-weight: bold;">ANULADA</td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= $comprasAnuladas ?></td>
        <td class="text-center" style="border: 1px solid #ddd;"><?= $totalCompras > 0 ? number_format(($comprasAnuladas/$totalCompras)*100, 1) : 0 ?>%</td>
        <td class="text-right" style="border: 1px solid #ddd; color: #dc3545;">
            $<?= number_format(array_sum(array_column(
                array_filter($comprasPeriodo, fn($c) => $c['estado'] == 'Anulada'), 
                'total_compra'
            )), 2) ?>
        </td>
    </tr>
</table>

    <!-- PIE DE PÁGINA -->
    <table width="100%" cellpadding="5" style="margin-top: 20px; border-top: 2px solid #003366;">
        <tr>
            <td class="text-center" style="color: #666; font-size: 10px;">
                Stock Nexus © <?= date('Y') ?> - Reporte de Compras generado el <?= $fecha_hora_actual ?><br>
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