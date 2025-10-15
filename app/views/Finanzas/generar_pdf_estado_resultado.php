<?php
date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../../libs/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

require_once __DIR__ . '/../../../config/database.php';

try {
    $fechaGeneracion = date('d/m/Y h:i:s a');
    
    // Calcular estado de resultados para el PDF - misma lógica que la vista
    $queryEstadoResultados = "
        SELECT 
            meses.fecha_periodo,
            COALESCE(ingresos.total_ingresos, 0) as total_ingresos,
            COALESCE(compras.total_compras, 0) as costo_ventas,
            COALESCE(gastos.total_gastos, 0) as gastos_operativos,
            COALESCE(ingresos.total_ingresos, 0) - COALESCE(compras.total_compras, 0) as utilidad_bruta,
            (COALESCE(ingresos.total_ingresos, 0) - COALESCE(compras.total_compras, 0)) - COALESCE(gastos.total_gastos, 0) as utilidad_neta
        FROM (
            SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL n MONTH), '%Y-%m-01') as fecha_periodo
            FROM (
                SELECT 0 as n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 
                UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 
                UNION SELECT 9 UNION SELECT 10 UNION SELECT 11
            ) numeros
        ) meses
        LEFT JOIN (
            SELECT 
                DATE_FORMAT(fecha_venta, '%Y-%m-01') as mes,
                SUM(total_venta) as total_ingresos
            FROM ventas 
            WHERE estado = 'Pagada'
            GROUP BY DATE_FORMAT(fecha_venta, '%Y-%m-01')
        ) ingresos ON meses.fecha_periodo = ingresos.mes
        LEFT JOIN (
            SELECT 
                DATE_FORMAT(fecha_compra, '%Y-%m-01') as mes,
                SUM(total_compra) as total_compras
            FROM compras 
            WHERE estado = 'Pagada'
            GROUP BY DATE_FORMAT(fecha_compra, '%Y-%m-01')
        ) compras ON meses.fecha_periodo = compras.mes
        LEFT JOIN (
            SELECT 
                DATE_FORMAT(fecha, '%Y-%m-01') as mes,
                SUM(valor) as total_gastos
            FROM gastos_operativos 
            GROUP BY DATE_FORMAT(fecha, '%Y-%m-01')
        ) gastos ON meses.fecha_periodo = gastos.mes
        ORDER BY meses.fecha_periodo DESC
        LIMIT 12
    ";
    
    $stmtEstado = $db->prepare($queryEstadoResultados);
    $stmtEstado->execute();
    $estadoResultados = $stmtEstado->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular totales acumulados
    $queryTotalesAcumulados = "
        SELECT 
            COALESCE(SUM(total_ingresos), 0) as total_ingresos,
            COALESCE(SUM(costo_ventas), 0) as total_costo_ventas,
            COALESCE(SUM(gastos_operativos), 0) as total_gastos,
            COALESCE(SUM(utilidad_bruta), 0) as total_utilidad_bruta,
            COALESCE(SUM(utilidad_neta), 0) as total_utilidad_neta
        FROM ($queryEstadoResultados) as estado_totales
    ";
    
    $stmtTotales = $db->prepare($queryTotalesAcumulados);
    $stmtTotales->execute();
    $totalesAcumulados = $stmtTotales->fetch(PDO::FETCH_ASSOC);
    
    // Preparar datos para el gráfico
    $labels = [];
    $ingresos = [];
    $utilidadesBrutas = [];
    $utilidadesNetas = [];

    foreach (array_reverse($estadoResultados) as $estado) {
        $labels[] = date('m/Y', strtotime($estado['fecha_periodo']));
        $ingresos[] = $estado['total_ingresos'];
        $utilidadesBrutas[] = $estado['utilidad_bruta'];
        $utilidadesNetas[] = $estado['utilidad_neta'];
    }

    // --- Gráfico para Estado de Resultados
    $chartConfig = [
        "type" => "line",
        "data" => [
            "labels" => $labels,
            "datasets" => [
                ["label" => "Ingresos Totales", "data" => $ingresos, "borderColor" => "#00ff88", "fill" => false, "tension" => 0.4],
                ["label" => "Utilidad Bruta", "data" => $utilidadesBrutas, "borderColor" => "#ffb84d", "fill" => false, "tension" => 0.4],
                ["label" => "Utilidad Neta", "data" => $utilidadesNetas, "borderColor" => "#4e9bff", "fill" => false, "tension" => 0.4]
            ]
        ],
        "options" => [
            "plugins" => ["legend" => ["position" => "top"]],
            "scales" => ["y" => ["beginAtZero" => true]]
        ]
    ];

    $chartUrl = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartConfig));
    $chartData = @file_get_contents($chartUrl);
    $chartBase64 = $chartData ? 'data:image/png;base64,' . base64_encode($chartData) : '';

    ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Financiero - Estado de Resultados</title>
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
        .text-orange { color: #fd7e14; }
        .text-purple { color: #6f42c1; }
        .page-break { page-break-before: always; }
        .total-acumulado { 
            background: #e8f4fd; 
            border-left: 4px solid #007bff; 
            padding: 15px; 
            margin: 15px 0; 
            border-radius: 5px;
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
        .metricas-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin: 15px 0;
        }
        .metrica {
            background: white;
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .metrica h4 {
            margin: 0 0 5px 0;
            font-size: 11px;
            color: #666;
        }
        .metrica .valor {
            font-size: 14px;
            font-weight: bold;
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
    </style>
</head>
<body>
<div class="container">
    <h1>Stock Nexus - Estado de Resultados</h1>
    <h3>Análisis de Rentabilidad</h3>
    <p style="text-align:center;color:gray;">Fecha de reporte: <?= $fechaGeneracion ?></p>
    <hr>

    <!-- Totales Acumulados -->
    <div class="resumen">
        <div class="total-acumulado">
            <h3>Resumen Acumulado (Últimos 12 Meses)</h3>
            <div class="metricas-container">
                <div class="metrica">
                    <h4>Total Ingresos</h4>
                    <div class="valor text-green">$<?= number_format($totalesAcumulados['total_ingresos'], 2) ?></div>
                </div>
                <div class="metrica">
                    <h4>Costo de Ventas</h4>
                    <div class="valor text-red">$<?= number_format($totalesAcumulados['total_costo_ventas'], 2) ?></div>
                </div>
                <div class="metrica">
                    <h4>Utilidad Bruta</h4>
                    <div class="valor text-orange">$<?= number_format($totalesAcumulados['total_utilidad_bruta'], 2) ?></div>
                </div>
                <div class="metrica">
                    <h4>Gastos Operativos</h4>
                    <div class="valor text-purple">$<?= number_format($totalesAcumulados['total_gastos'], 2) ?></div>
                </div>
                <div class="metrica">
                    <h4>Utilidad Neta</h4>
                    <div class="valor text-blue">$<?= number_format($totalesAcumulados['total_utilidad_neta'], 2) ?></div>
                </div>
            </div>
            <?php 
            $margenNeto = $totalesAcumulados['total_ingresos'] > 0 ? 
                ($totalesAcumulados['total_utilidad_neta'] / $totalesAcumulados['total_ingresos']) * 100 : 0;
            $margenClass = $margenNeto >= 20 ? 'badge-success' : ($margenNeto >= 10 ? 'badge-warning' : 'badge-danger');
            ?>
            <div style="text-align: center; margin-top: 10px;">
                <strong>Margen Neto Promedio: </strong>
                <span class="badge <?= $margenClass ?>"><?= number_format($margenNeto, 2) ?>%</span>
            </div>
        </div>
    </div>

    <!-- Tabla Detallada -->
    <h3>Estado de Resultados Mensual</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Periodo</th>
                <th>Ingresos Totales</th>
                <th>Costo de Ventas</th>
                <th>Utilidad Bruta</th>
                <th>Gastos Operativos</th>
                <th>Utilidad Neta</th>
                <th>Margen Neto</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($estadoResultados as $estado): 
            $margenNeto = $estado['total_ingresos'] > 0 ? ($estado['utilidad_neta'] / $estado['total_ingresos']) * 100 : 0;
            $margenClass = $margenNeto >= 20 ? 'badge-success' : ($margenNeto >= 10 ? 'badge-warning' : 'badge-danger');
        ?>
            <tr>
                <td><strong><?= date('M/Y', strtotime($estado['fecha_periodo'])) ?></strong></td>
                <td class="text-green">$<?= number_format($estado['total_ingresos'], 2) ?></td>
                <td class="text-red">$<?= number_format($estado['costo_ventas'], 2) ?></td>
                <td class="text-orange"><strong>$<?= number_format($estado['utilidad_bruta'], 2) ?></strong></td>
                <td class="text-purple">$<?= number_format($estado['gastos_operativos'], 2) ?></td>
                <td class="text-blue"><strong>$<?= number_format($estado['utilidad_neta'], 2) ?></strong></td>
                <td><span class="badge <?= $margenClass ?>"><?= number_format($margenNeto, 2) ?>%</span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="page-break"></div>

<div class="container">
    <div class="grafico">
        <h2>Evolución del Estado de Resultados (Últimos 12 Meses)</h2>
        <?php if (!empty($chartBase64)): ?>
            <img src="<?= $chartBase64 ?>" style="width:100%; max-width:650px; display: block; margin: 0 auto;">
        <?php else: ?>
            <p style="color:#dc3545;font-style:italic;text-align:center;">No se pudo generar el gráfico.</p>
        <?php endif; ?>
        
        <!-- Leyenda del Gráfico -->
        <div style="margin-top: 20px; text-align: center;">
            <h4>Interpretación del Gráfico</h4>
            <p style="font-size: 11px; color: #666;">
                <strong>Ingresos Totales:</strong> Ventas brutas del periodo<br>
                <strong>Utilidad Bruta:</strong> Ingresos - Costo de Ventas<br>
                <strong>Utilidad Neta:</strong> Utilidad Bruta - Gastos Operativos
            </p>
        </div>
    </div>

    <!-- Análisis Adicional -->
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
        <h4 style="text-align: center; color: #003366;">Análisis de Rentabilidad</h4>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-top: 15px;">
            <div>
                <h5 style="color: #28a745; margin-bottom: 8px;">Indicadores Positivos</h5>
                <ul style="font-size: 11px; color: #333;">
                    <li>Utilidad Bruta consistente</li>
                    <li>Margen neto superior al 15%</li>
                    <li>Crecimiento en ingresos</li>
                    <li>Control de gastos operativos</li>
                </ul>
            </div>
            <div>
                <h5 style="color: #dc3545; margin-bottom: 8px;">Áreas de Mejora</h5>
                <ul style="font-size: 11px; color: #333;">
                    <li>Reducción del costo de ventas</li>
                    <li>Optimización de gastos</li>
                    <li>Mejora de márgenes</li>
                    <li>Eficiencia operativa</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    Stock Nexus © <?= date('Y') ?> — Estado de Resultados Generado el <?= $fechaGeneracion ?>
</div>

</body>
</html>
<?php
    $html = ob_get_clean();

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    if (ob_get_length()) ob_clean();
    $dompdf->stream("Estado_Resultados_StockNexus.pdf", ["Attachment" => true]);
    exit;

} catch (Exception $e) {
    die("Error al generar el PDF: " . $e->getMessage());
}