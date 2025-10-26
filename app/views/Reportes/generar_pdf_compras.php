<?php
// --- Cargar Dompdf ---
require_once __DIR__ . '/../../libs/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// --- Incluir conexión y controlador ---
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../controllers/CompraController.php';

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

    // --- Capturar contenido HTML ---
    ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Compras - Stock Nexus</title>
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
    </style>
</head>
<body>
<div class="container">
    <h1>Stock Nexus - Reporte de Compras</h1>
    <h3>Resumen de Compras del Período</h3>
    <p style="text-align:center;color:gray;">Fecha de reporte: <?= $fechaGeneracion ?></p>
    <p style="text-align:center;color:gray;">
        Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?>
    </p>
    <hr>

    <!-- Estadísticas Resumen -->
    <div class="estadisticas-grid">
        <div class="estadistica-item">
            <div>Total Compras</div>
            <div class="estadistica-valor text-blue"><?= $totalCompras ?></div>
            <small>En el período</small>
        </div>
        <div class="estadistica-item">
            <div>Monto Total</div>
            <div class="estadistica-valor text-red">$<?= number_format($montoTotal, 2) ?></div>
            <small>Inversión total</small>
        </div>
        <div class="estadistica-item">
            <div>Compras Pagadas</div>
            <div class="estadistica-valor text-green"><?= $comprasPagadas ?></div>
            <small><?= $totalCompras > 0 ? number_format(($comprasPagadas/$totalCompras)*100, 1) : 0 ?>%</small>
        </div>
        <div class="estadistica-item">
            <div>Promedio/Compra</div>
            <div class="estadistica-valor text-purple">$<?= number_format($promedioCompra, 2) ?></div>
            <small>Valor promedio</small>
        </div>
    </div>

    <!-- Detalle de Compras -->
    <h3>Detalle de Compras (<?= $totalCompras ?> registros)</h3>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Proveedor</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($comprasPeriodo as $compra): ?>
            <tr>
                <td><?= $compra['codigo_compra'] ?></td>
                <td><?= $compra['nombre_proveedor'] ?></td>
                <td><?= date('d/m/Y', strtotime($compra['fecha_compra'])) ?></td>
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

    <div class="page-break"></div>

    <!-- Compras por Proveedor -->
    <h3>Compras por Proveedor</h3>
    <table>
        <thead>
            <tr>
                <th>Proveedor</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($comprasPorProveedor as $proveedor => $datos): ?>
            <tr>
                <td><?= htmlspecialchars($proveedor) ?></td>
                <td class="text-center"><?= $datos['cantidad'] ?></td>
                <td class="text-red">$<?= number_format($datos['monto_total'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($comprasPorProveedor)): ?>
            <tr>
                <td colspan="3" class="text-center text-muted">No hay compras por proveedor.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Distribución por Estado -->
    <h3>Distribución por Estado</h3>
    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th>Cantidad</th>
                <th>Porcentaje</th>
                <th>Monto Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="badge badge-success">Pagada</span></td>
                <td class="text-center"><?= $comprasPagadas ?></td>
                <td class="text-center"><?= $totalCompras > 0 ? number_format(($comprasPagadas/$totalCompras)*100, 1) : 0 ?>%</td>
                <td class="text-red">$<?= number_format(array_sum(array_column(
                    array_filter($comprasPeriodo, fn($c) => $c['estado'] == 'Pagada'), 
                    'total_compra'
                )), 2) ?></td>
            </tr>
            <tr>
                <td><span class="badge badge-warning">Pendiente</span></td>
                <td class="text-center"><?= $comprasPendientes ?></td>
                <td class="text-center"><?= $totalCompras > 0 ? number_format(($comprasPendientes/$totalCompras)*100, 1) : 0 ?>%</td>
                <td class="text-red">$<?= number_format(array_sum(array_column(
                    array_filter($comprasPeriodo, fn($c) => $c['estado'] == 'Pendiente'), 
                    'total_compra'
                )), 2) ?></td>
            </tr>
            <tr>
                <td><span class="badge badge-danger">Anulada</span></td>
                <td class="text-center"><?= $comprasAnuladas ?></td>
                <td class="text-center"><?= $totalCompras > 0 ? number_format(($comprasAnuladas/$totalCompras)*100, 1) : 0 ?>%</td>
                <td class="text-red">$<?= number_format(array_sum(array_column(
                    array_filter($comprasPeriodo, fn($c) => $c['estado'] == 'Anulada'), 
                    'total_compra'
                )), 2) ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Resumen Final -->
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
        <h4 style="color: #003366; text-align: center;">Resumen Ejecutivo</h4>
        <div style="font-size: 10px; color: #333; line-height: 1.5;">
            <p>El reporte de compras del período <strong><?= date('d/m/Y', strtotime($fecha_inicio)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?></strong> muestra:</p>
            <ul>
                <li><strong><?= $totalCompras ?> compras</strong> registradas</li>
                <li><strong>$<?= number_format($montoTotal, 2) ?></strong> en inversión total</li>
                <li><strong><?= $comprasPagadas ?> compras pagadas</strong> (<?= $totalCompras > 0 ? number_format(($comprasPagadas/$totalCompras)*100, 1) : 0 ?>%)</li>
                <li><strong><?= $comprasPendientes ?> compras pendientes</strong> de pago</li>
                <li><strong><?= $comprasAnuladas ?> compras anuladas</strong></li>
                <li><strong>$<?= number_format($promedioCompra, 2) ?></strong> de promedio por compra</li>
                <li><strong><?= count($comprasPorProveedor) ?> proveedores</strong> diferentes</li>
            </ul>
        </div>
    </div>

    <!-- Footer profesional -->
    <div class="footer">
        Stock Nexus © <?= date('Y') ?> — Reporte de Compras generado el <?= $fechaGeneracion ?>
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
    $dompdf->stream("Reporte de Compras ".date('d-m-Y').".pdf", ["Attachment" => true]);
    exit;

} catch (Exception $e) {
    // Manejar errores gracefuly
    die("Error al generar el PDF: " . $e->getMessage());
}
?>