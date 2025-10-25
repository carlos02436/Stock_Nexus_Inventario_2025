<?php
// --- Cargar Dompdf ---
require_once __DIR__ . '/../../libs/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// --- Incluir conexión y controlador ---
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../controllers/ReporteController.php';

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
    $tipo_reporte = $_GET['tipo'] ?? 'ventas';
    $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
    $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

    $reporteController = new ReporteController($db);
    
    if ($tipo_reporte === 'ventas') {
        $ventas = $reporteController->generarReporteVentas($fecha_inicio, $fecha_fin);
        $productosMasVendidos = $reporteController->getProductosMasVendidos(10);

        // Calcular estadísticas - SOLO VENTAS ACTIVAS (no anuladas)
        $ventasActivas = array_filter($ventas, fn($v) => $v['estado'] != 'Anulada');
        $totalVentas = count($ventasActivas);
        $ingresosTotales = array_sum(array_column($ventasActivas, 'total_venta'));
        $ventasPagadas = count(array_filter($ventasActivas, fn($v) => $v['estado'] == 'Pagada'));
        $ventasPendientes = count(array_filter($ventasActivas, fn($v) => $v['estado'] == 'Pendiente'));
        $ventasAnuladas = count(array_filter($ventas, fn($v) => $v['estado'] == 'Anulada'));

        // --- PREPARAR DATOS PARA EL GRÁFICO ---
        $labels = [];
        $ventasPorDia = [];
        $ingresosPorDia = [];

        // Agrupar ventas por día
        $ventasPorFecha = [];
        foreach ($ventasActivas as $venta) {
            $fecha = date('Y-m-d', strtotime($venta['fecha_venta']));
            if (!isset($ventasPorFecha[$fecha])) {
                $ventasPorFecha[$fecha] = [
                    'cantidad' => 0,
                    'ingresos' => 0
                ];
            }
            $ventasPorFecha[$fecha]['cantidad']++;
            $ventasPorFecha[$fecha]['ingresos'] += $venta['total_venta'];
        }

        // Ordenar por fecha
        ksort($ventasPorFecha);

        foreach ($ventasPorFecha as $fecha => $datos) {
            $labels[] = date('d/m', strtotime($fecha));
            $ventasPorDia[] = $datos['cantidad'];
            $ingresosPorDia[] = $datos['ingresos'];
        }

        // --- Crear imagen del gráfico ---
        $chartConfig = [
            "type" => "line",
            "data" => [
                "labels" => $labels,
                "datasets" => [
                    [
                        "label" => "Número de Ventas", 
                        "data" => $ventasPorDia, 
                        "borderColor" => "#4e73df", 
                        "fill" => false,
                        "yAxisID" => "y"
                    ],
                    [
                        "label" => "Ingresos ($)", 
                        "data" => $ingresosPorDia, 
                        "borderColor" => "#1cc88a", 
                        "fill" => false,
                        "yAxisID" => "y1"
                    ]
                ]
            ],
            "options" => [
                "plugins" => ["legend" => ["position" => "top"]],
                "scales" => [
                    "y" => [
                        "type" => "linear",
                        "display" => true,
                        "position" => "left",
                        "title" => ["display" => true, "text" => "Número de Ventas"]
                    ],
                    "y1" => [
                        "type" => "linear",
                        "display" => true,
                        "position" => "right",
                        "title" => ["display" => true, "text" => "Ingresos ($)"],
                        "grid" => ["drawOnChartArea" => false]
                    ]
                ]
            ]
        ];

        $chartUrl = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartConfig));
        $chartData = @file_get_contents($chartUrl);
        
        if ($chartData === false) {
            $chartBase64 = ''; // Imagen vacía si hay error
        } else {
            $chartBase64 = 'data:image/png;base64,' . base64_encode($chartData);
        }
    }

    // --- Capturar contenido HTML ---
    ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas - Stock Nexus</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px; 
            margin: 30px; 
            color: #000;
            position: relative;
            min-height: 100vh;
            padding-bottom: 60px;
        }
        h1, h2, h3 { text-align: center; color: #003366; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: center; }
        th { background-color: #003366; color: white; font-weight: bold; }
        .resumen { background: #f4f4f4; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .text-green { color: #28a745; }
        .text-red { color: #dc3545; }
        .text-blue { color: #007bff; }
        .text-warning { color: #ffc107; }
        .page-break { page-break-before: always; }
        .total-acumulado { 
            background: #e8f4fd; 
            border-left: 4px solid #007bff; 
            padding: 10px; 
            margin: 10px 0; 
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .footer-content {
            max-width: 100%;
            margin: 0 auto;
        }
        .estadisticas-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 15px 0;
        }
        .estadistica-item {
            background: white;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .estadistica-valor {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-secondary { background: #e2e3e5; color: #383d41; }
    </style>
</head>
<body>
<div class="container">
    <h1>Stock Nexus - Reporte de Ventas</h1>
    <h3>Resumen de Ventas</h3>
    <p style="text-align:center;color:gray;">Fecha de reporte: <?= $fechaGeneracion ?></p>
    <p style="text-align:center;color:gray;">
        Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?>
    </p>
    <hr>

    <!-- Estadísticas Resumen -->
    <div class="estadisticas-grid">
        <div class="estadistica-item">
            <div>Total Ventas</div>
            <div class="estadistica-valor text-blue"><?= $totalVentas ?></div>
            <small>(Excluyendo anuladas)</small>
        </div>
        <div class="estadistica-item">
            <div>Ingresos Totales</div>
            <div class="estadistica-valor text-green">$<?= number_format($ingresosTotales, 2) ?></div>
            <small>(Excluyendo anuladas)</small>
        </div>
        <div class="estadistica-item">
            <div>Ventas Pagadas</div>
            <div class="estadistica-valor text-green"><?= $ventasPagadas ?></div>
        </div>
        <div class="estadistica-item">
            <div>Ventas Pendientes</div>
            <div class="estadistica-valor text-warning"><?= $ventasPendientes ?></div>
        </div>
    </div>

    <!-- Detalle de Ventas -->
    <h3>Detalle de Ventas</h3>
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
        <?php foreach ($ventas as $venta): 
            $colorEstado = $venta['estado'] == 'Pagada' ? 'text-green' : 
                          ($venta['estado'] == 'Pendiente' ? 'text-warning' : 'text-red');
        ?>
            <tr>
                <td><?= $venta['codigo_venta'] ?></td>
                <td><?= date('d/m/Y', strtotime($venta['fecha_venta'])) ?></td>
                <td><?= $venta['nombre_cliente'] ?: 'Cliente General' ?></td>
                <td class="text-green">$<?= number_format($venta['total_venta'], 2) ?></td>
                <td><?= $venta['metodo_pago'] ?></td>
                <td class="<?= $colorEstado ?>">
                    <?= $venta['estado'] ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($ventas)): ?>
            <tr>
                <td colspan="6" class="text-center text-muted">No hay ventas registradas en este período.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Productos Más Vendidos -->
    <h3>Productos Más Vendidos</h3>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad Vendida</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($productosMasVendidos as $producto): ?>
            <tr>
                <td><?= $producto['nombre_producto'] ?></td>
                <td class="text-blue"><?= $producto['total_vendido'] ?> unidades</td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($productosMasVendidos)): ?>
            <tr>
                <td colspan="2" class="text-center text-muted">No hay productos registrados.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Gráfico de Tendencia -->
    <?php if (!empty($chartBase64)): ?>
    <div style="page-break-before: always;">
        <h3>Tendencia de Ventas</h3>
        <p style="text-align:center;color:gray;">Evolución de Ventas e Ingresos</p>
        <div style="text-align:center; margin-top:30px;">
            <img src="<?= $chartBase64 ?>" style="width:100%; max-width:650px;">
        </div>
    </div>
    <?php endif; ?>

    <!-- Resumen Final -->
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
        <h4 style="color: #003366; text-align: center;">Resumen Ejecutivo</h4>
        <div style="font-size: 11px; color: #333; line-height: 1.5;">
            <p>El reporte de ventas del período <strong><?= date('d/m/Y', strtotime($fecha_inicio)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?></strong> muestra:</p>
            <ul>
                <li><strong><?= $totalVentas ?> ventas</strong> activas procesadas</li>
                <li><strong>$<?= number_format($ingresosTotales, 2) ?></strong> en ingresos totales</li>
                <li><strong><?= $ventasPagadas ?> ventas pagadas</strong> (<?= $totalVentas > 0 ? number_format(($ventasPagadas/$totalVentas)*100, 1) : 0 ?>%)</li>
                <li><strong><?= $ventasPendientes ?> ventas pendientes</strong> de pago</li>
                <li><strong><?= $ventasAnuladas ?> ventas anuladas</strong> excluidas del análisis</li>
            </ul>
            <?php if ($totalVentas > 0): ?>
            <p>El promedio de venta por transacción es de <strong>$<?= number_format($ingresosTotales/$totalVentas, 2) ?></strong>.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer profesional -->
    <div class="footer">
        <div class="footer-content">
            Stock Nexus © <?= date('Y') ?> — Reporte de Ventas generado el <?= $fechaGeneracion ?>
        </div>
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
    $dompdf->stream("Reporte de Ventas ".date('d-m-Y').".pdf", ["Attachment" => true]);
    exit;

} catch (Exception $e) {
    // Manejar errores gracefuly
    die("Error al generar el PDF: " . $e->getMessage());
}
?>