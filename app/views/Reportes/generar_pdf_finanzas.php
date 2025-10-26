<?php
// --- Cargar Dompdf ---
require_once __DIR__ . '/../../libs/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// --- Incluir conexión y controlador ---
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../controllers/FinanzaController.php';

// Función para obtener nombre del mes en español
function obtenerMesEspanol($fecha) {
    $meses = [
        'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
        'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
        'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
        'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
    ];
    $mesIngles = date('F', strtotime($fecha));
    return $meses[$mesIngles] ?? $mesIngles;
}

try {
    // --- Capturar fecha y hora EXACTA de generación ---
    date_default_timezone_set('America/Bogota');
    $fechaGeneracion = date('d/m/Y h:i:s A');
    
    // --- Obtener datos del reporte ---
    $tipo_reporte = $_GET['tipo'] ?? 'finanzas';
    $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
    $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

    // Crear instancia del controlador
    $finanzaController = new FinanzaController($db);
    $resumen = $finanzaController->getResumenFinanciero();
    $ingresosVsEgresos = $finanzaController->getIngresosVsEgresos(12);

    // Obtener datos del período seleccionado
    $queryVentasPeriodo = "
        SELECT 
            codigo_venta,
            fecha_venta,
            total_venta,
            metodo_pago,
            estado,
            nombre_cliente
        FROM ventas 
        LEFT JOIN clientes ON ventas.id_cliente = clientes.id_cliente
        WHERE fecha_venta BETWEEN :fecha_inicio AND :fecha_fin
        ORDER BY fecha_venta DESC
    ";
    $stmtVentas = $db->prepare($queryVentasPeriodo);
    $stmtVentas->execute([
        ':fecha_inicio' => $fecha_inicio,
        ':fecha_fin' => $fecha_fin
    ]);
    $ventasPeriodo = $stmtVentas->fetchAll(PDO::FETCH_ASSOC);

    $queryComprasPeriodo = "
        SELECT 
            codigo_compra,
            fecha_compra,
            total_compra,
            estado,
            nombre_proveedor
        FROM compras 
        LEFT JOIN proveedores ON compras.id_proveedor = proveedores.id_proveedor
        WHERE fecha_compra BETWEEN :fecha_inicio AND :fecha_fin
        ORDER BY fecha_compra DESC
    ";
    $stmtCompras = $db->prepare($queryComprasPeriodo);
    $stmtCompras->execute([
        ':fecha_inicio' => $fecha_inicio,
        ':fecha_fin' => $fecha_fin
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
        ':fecha_inicio' => $fecha_inicio,
        ':fecha_fin' => $fecha_fin
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

    // --- Capturar contenido HTML ---
    ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Financiero - Stock Nexus</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 11px; 
            margin: 25px; 
            color: #000;
            position: relative;
            min-height: 100vh;
            padding-bottom: 60px;
        }
        h1, h2, h3 { text-align: center; color: #003366; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: center; }
        th { background-color: #003366; color: white; font-weight: bold; }
        .resumen { background: #f4f4f4; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .text-green { color: #28a745; }
        .text-red { color: #dc3545; }
        .text-blue { color: #007bff; }
        .text-warning { color: #ffc107; }
        .text-info { color: #17a2b8; }
        .text-purple { color: #6f42c1; }
        .page-break { page-break-before: always; }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .estadisticas-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin: 15px 0;
        }
        .estadistica-item {
            background: white;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .estadistica-valor {
            font-size: 16px;
            font-weight: bold;
            margin: 3px 0;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
<div class="container">
    <h1>Stock Nexus - Reporte Financiero</h1>
    <h3>Resumen Financiero del Período</h3>
    <p style="text-align:center;color:gray;">Fecha de reporte: <?= $fechaGeneracion ?></p>
    <p style="text-align:center;color:gray;">
        Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?>
    </p>
    <hr>

    <!-- Estadísticas Resumen -->
    <div class="estadisticas-grid">
        <div class="estadistica-item">
            <div>Ingresos Totales</div>
            <div class="estadistica-valor text-green">$<?= number_format($ingresosPeriodo, 2) ?></div>
            <small>Ventas Pagadas</small>
        </div>
        <div class="estadistica-item">
            <div>Egresos Totales</div>
            <div class="estadistica-valor text-red">$<?= number_format($egresosPeriodo, 2) ?></div>
            <small>Compras Pagadas</small>
        </div>
        <div class="estadistica-item">
            <div>Utilidad Neta</div>
            <div class="estadistica-valor text-blue">$<?= number_format($utilidadPeriodo, 2) ?></div>
            <small>Ingresos - Egresos</small>
        </div>
        <div class="estadistica-item">
            <div>Margen Utilidad</div>
            <div class="estadistica-valor text-purple"><?= $ingresosPeriodo > 0 ? number_format(($utilidadPeriodo/$ingresosPeriodo)*100, 1) : 0 ?>%</div>
            <small>Rentabilidad</small>
        </div>
    </div>

    <!-- Detalle de Ventas -->
    <h3>Detalle de Ventas (<?= count($ventasPeriodo) ?> registros)</h3>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Método Pago</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($ventasPeriodo as $venta): ?>
            <tr>
                <td><?= $venta['codigo_venta'] ?></td>
                <td><?= date('d/m/Y', strtotime($venta['fecha_venta'])) ?></td>
                <td><?= $venta['nombre_cliente'] ?: 'Cliente General' ?></td>
                <td class="text-green">$<?= number_format($venta['total_venta'], 2) ?></td>
                <td><?= $venta['metodo_pago'] ?></td>
                <td>
                    <span class="badge badge-<?= $venta['estado'] == 'Pagada' ? 'success' : ($venta['estado'] == 'Pendiente' ? 'warning' : 'danger') ?>">
                        <?= $venta['estado'] ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($ventasPeriodo)): ?>
            <tr>
                <td colspan="6" class="text-center text-muted">No hay ventas en este período.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Detalle de Compras -->
    <h3>Detalle de Compras (<?= count($comprasPeriodo) ?> registros)</h3>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($comprasPeriodo as $compra): ?>
            <tr>
                <td><?= $compra['codigo_compra'] ?></td>
                <td><?= date('d/m/Y', strtotime($compra['fecha_compra'])) ?></td>
                <td><?= $compra['nombre_proveedor'] ?: 'Proveedor General' ?></td>
                <td class="text-red">$<?= number_format($compra['total_compra'], 2) ?></td>
                <td>
                    <span class="badge badge-<?= $compra['estado'] == 'Pagada' ? 'success' : ($compra['estado'] == 'Pendiente' ? 'warning' : 'danger') ?>">
                        <?= $compra['estado'] ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($comprasPeriodo)): ?>
            <tr>
                <td colspan="5" class="text-center text-muted">No hay compras en este período.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Métodos de Pago -->
    <h3>Distribución por Métodos de Pago</h3>
    <table>
        <thead>
            <tr>
                <th>Método de Pago</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($metodosPago as $metodo): ?>
            <tr>
                <td><?= $metodo['metodo_pago'] ?></td>
                <td class="text-center"><?= $metodo['cantidad'] ?></td>
                <td class="text-green">$<?= number_format($metodo['total'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($metodosPago)): ?>
            <tr>
                <td colspan="3" class="text-center text-muted">No hay datos de métodos de pago.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Resumen Final -->
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
        <h4 style="color: #003366; text-align: center;">Resumen Ejecutivo</h4>
        <div style="font-size: 10px; color: #333; line-height: 1.5;">
            <p>El reporte financiero del período <strong><?= date('d/m/Y', strtotime($fecha_inicio)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?></strong> muestra:</p>
            <ul>
                <li><strong>$<?= number_format($ingresosPeriodo, 2) ?></strong> en ingresos por ventas pagadas</li>
                <li><strong>$<?= number_format($egresosPeriodo, 2) ?></strong> en egresos por compras pagadas</li>
                <li><strong>$<?= number_format($utilidadPeriodo, 2) ?></strong> en utilidad neta (<?= $ingresosPeriodo > 0 ? number_format(($utilidadPeriodo/$ingresosPeriodo)*100, 1) : 0 ?>% de margen)</li>
                <li><strong><?= $ventasPagadasPeriodo ?> ventas pagadas</strong> de <?= count($ventasPeriodo) ?> totales</li>
                <li><strong><?= $ventasPendientesPeriodo ?> ventas pendientes</strong> de pago</li>
                <li><strong><?= $ventasAnuladasPeriodo ?> ventas anuladas</strong></li>
                <li><strong><?= count($comprasPeriodo) ?> compras</strong> registradas</li>
            </ul>
        </div>
    </div>

    <!-- Footer profesional -->
    <div class="footer">
        Stock Nexus © <?= date('Y') ?> — Reporte Financiero generado el <?= $fechaGeneracion ?>
    </div>
</div>
</body>
</html>
<?php
    // --- CAPTURAR el contenido del buffer ---
    $html = ob_get_clean();
    
    // --- Generar PDF ---
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    // --- Limpiar cualquier salida previa ---
    if (ob_get_length()) ob_clean();
    
    // --- Enviar PDF ---
    $dompdf->stream("Reporte Financiero ".date('d-m-Y').".pdf", ["Attachment" => true]);
    exit;

} catch (Exception $e) {
    // Manejar errores gracefuly
    die("Error al generar el PDF: " . $e->getMessage());
}
?>