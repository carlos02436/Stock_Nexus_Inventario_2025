<?php
date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../../libs/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

require_once __DIR__ . '/../../../config/database.php';

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
    $fechaGeneracion = date('d/m/Y h:i:s a');
    
    // Calcular estado de resultados para el PDF
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

    // ANÁLISIS DINÁMICO BASADO EN DATOS REALES
    $analisis = [
        'positivos' => [],
        'mejoras' => []
    ];

    // Calcular métricas para el análisis
    $margenNeto = $totalesAcumulados['total_ingresos'] > 0 ? 
        ($totalesAcumulados['total_utilidad_neta'] / $totalesAcumulados['total_ingresos']) * 100 : 0;
    
    $margenBruto = $totalesAcumulados['total_ingresos'] > 0 ? 
        ($totalesAcumulados['total_utilidad_bruta'] / $totalesAcumulados['total_ingresos']) * 100 : 0;
    
    $relacionGastos = $totalesAcumulados['total_ingresos'] > 0 ? 
        ($totalesAcumulados['total_gastos'] / $totalesAcumulados['total_ingresos']) * 100 : 0;

    // Análisis de tendencia mensual
    $mesesConUtilidad = 0;
    $mesesConPerdida = 0;
    $totalMeses = count($estadoResultados);
    
    foreach ($estadoResultados as $mes) {
        if ($mes['utilidad_neta'] > 0) {
            $mesesConUtilidad++;
        } else {
            $mesesConPerdida++;
        }
    }

    // Análisis de crecimiento (comparar últimos 3 meses con los 3 anteriores)
    if (count($estadoResultados) >= 6) {
        $ingresosUltimos3Meses = 0;
        $ingresos3MesesAnteriores = 0;
        
        for ($i = 0; $i < 3; $i++) {
            $ingresosUltimos3Meses += $estadoResultados[$i]['total_ingresos'];
            $ingresos3MesesAnteriores += $estadoResultados[$i + 3]['total_ingresos'];
        }
        
        $crecimientoIngresos = $ingresos3MesesAnteriores > 0 ? 
            (($ingresosUltimos3Meses - $ingresos3MesesAnteriores) / $ingresos3MesesAnteriores) * 100 : 0;
    } else {
        $crecimientoIngresos = 0;
    }

    // GENERAR ANÁLISIS POSITIVO BASADO EN DATOS REALES
    if ($margenNeto > 15) {
        $analisis['positivos'][] = "Margen neto excelente del " . number_format($margenNeto, 1) . "%";
    } elseif ($margenNeto > 0) {
        $analisis['positivos'][] = "Margen neto positivo del " . number_format($margenNeto, 1) . "%";
    }

    if ($mesesConUtilidad > ($totalMeses * 0.7)) {
        $analisis['positivos'][] = "Rentabilidad consistente en " . $mesesConUtilidad . " de " . $totalMeses . " meses";
    }

    if ($crecimientoIngresos > 10) {
        $analisis['positivos'][] = "Crecimiento sólido en ingresos del " . number_format($crecimientoIngresos, 1) . "%";
    } elseif ($crecimientoIngresos > 0) {
        $analisis['positivos'][] = "Crecimiento moderado en ingresos del " . number_format($crecimientoIngresos, 1) . "%";
    }

    if ($margenBruto > 40) {
        $analisis['positivos'][] = "Margen bruto saludable del " . number_format($margenBruto, 1) . "%";
    }

    if ($relacionGastos < 30) {
        $analisis['positivos'][] = "Control eficiente de gastos operativos";
    }

    // GENERAR ÁREAS DE MEJORA BASADO EN DATOS REALES
    if ($margenNeto < 0) {
        $analisis['mejoras'][] = "Pérdida neta acumulada - revisar estructura de costos";
    } elseif ($margenNeto < 10) {
        $analisis['mejoras'][] = "Margen neto bajo - optimizar rentabilidad";
    }

    if ($mesesConPerdida > ($totalMeses * 0.4)) {
        $analisis['mejoras'][] = "Inestabilidad en " . $mesesConPerdida . " meses - mejorar consistencia";
    }

    if ($crecimientoIngresos < 0) {
        $analisis['mejoras'][] = "Decrecimiento en ingresos del " . number_format(abs($crecimientoIngresos), 1) . "%";
    }

    if ($margenBruto < 25) {
        $analisis['mejoras'][] = "Margen bruto bajo - revisar precios y costos de venta";
    }

    if ($relacionGastos > 50) {
        $analisis['mejoras'][] = "Gastos operativos elevados - implementar medidas de eficiencia";
    }

    // Si no hay datos suficientes, mostrar mensajes por defecto
    if (empty($analisis['positivos']) && $totalesAcumulados['total_ingresos'] > 0) {
        $analisis['positivos'][] = "Operaciones registradas correctamente en el sistema";
        $analisis['positivos'][] = "Base de datos funcionando adecuadamente";
    }

    if (empty($analisis['mejoras']) && $totalesAcumulados['total_ingresos'] > 0) {
        $analisis['mejoras'][] = "Mantener el buen desempeño actual";
        $analisis['mejoras'][] = "Continuar con el monitoreo regular";
    }

    // Si no hay datos en absoluto
    if ($totalesAcumulados['total_ingresos'] == 0) {
        $analisis['positivos'][] = "Sistema operativo y listo para registrar transacciones";
        $analisis['mejoras'][] = "Iniciar registro de ventas y compras para generar reportes";
        $analisis['mejoras'][] = "Capturar datos financieros para análisis detallado";
    }
    
    // Preparar datos para el gráfico
    $labels = [];
    $ingresos = [];
    $utilidadesBrutas = [];
    $utilidadesNetas = [];

    foreach (array_reverse($estadoResultados) as $estado) {
        $mesEspanol = obtenerMesEspanol($estado['fecha_periodo']);
        $año = date('Y', strtotime($estado['fecha_periodo']));
        $labels[] = $mesEspanol . ' ' . $año;
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
        .analisis-item {
            margin-bottom: 8px;
            font-size: 11px;
            line-height: 1.4;
        }
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
            $margenNetoMes = $estado['total_ingresos'] > 0 ? ($estado['utilidad_neta'] / $estado['total_ingresos']) * 100 : 0;
            $margenClass = $margenNetoMes >= 20 ? 'badge-success' : ($margenNetoMes >= 10 ? 'badge-warning' : 'badge-danger');
            $mesEspanol = obtenerMesEspanol($estado['fecha_periodo']);
            $año = date('Y', strtotime($estado['fecha_periodo']));
        ?>
            <tr>
                <td><strong><?= $mesEspanol . ' ' . $año ?></strong></td>
                <td class="text-green">$<?= number_format($estado['total_ingresos'], 2) ?></td>
                <td class="text-red">$<?= number_format($estado['costo_ventas'], 2) ?></td>
                <td class="text-orange"><strong>$<?= number_format($estado['utilidad_bruta'], 2) ?></strong></td>
                <td class="text-purple">$<?= number_format($estado['gastos_operativos'], 2) ?></td>
                <td class="text-blue"><strong>$<?= number_format($estado['utilidad_neta'], 2) ?></strong></td>
                <td><span class="badge <?= $margenClass ?>"><?= number_format($margenNetoMes, 2) ?>%</span></td>
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
    </div>

    <!-- Análisis Adicional Dinámico -->
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
        <h4 style="text-align: center; color: #003366;">Análisis de Rentabilidad Basado en Datos Reales</h4>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-top: 15px;">
            <div>
                <h5 style="color: #28a745; margin-bottom: 8px;">Aspectos Destacados</h5>
                <ul style="font-size: 11px; color: #333; list-style-type: none; padding-left: 0;">
                    <?php foreach ($analisis['positivos'] as $positivo): ?>
                    <li class="analisis-item">✓ <?= htmlspecialchars($positivo) ?></li>
                    <?php endforeach; ?>
                    <?php if (empty($analisis['positivos'])): ?>
                    <li class="analisis-item">• Esperando datos para análisis</li>
                    <?php endif; ?>
                </ul>
            </div>
            <div>
                <h5 style="color: #dc3545; margin-bottom: 8px;">Oportunidades de Mejora</h5>
                <ul style="font-size: 11px; color: #333; list-style-type: none; padding-left: 0;">
                    <?php foreach ($analisis['mejoras'] as $mejora): ?>
                    <li class="analisis-item">⚠ <?= htmlspecialchars($mejora) ?></li>
                    <?php endforeach; ?>
                    <?php if (empty($analisis['mejoras'])): ?>
                    <li class="analisis-item">• Sin áreas críticas identificadas</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Resumen Ejecutivo -->
        <div style="margin-top: 20px; padding: 15px; background: white; border-radius: 5px; border: 1px solid #ddd;">
            <h5 style="color: #003366; margin-bottom: 10px; text-align: center;">Resumen Ejecutivo</h5>
            <div style="font-size: 11px; color: #333; line-height: 1.5;">
                <?php if ($totalesAcumulados['total_ingresos'] > 0): ?>
                    <p>El análisis de los últimos 12 meses muestra 
                    <strong><?= $mesesConUtilidad ?> meses con utilidad</strong> y 
                    <strong><?= $mesesConPerdida ?> meses con pérdida</strong>. 
                    El margen neto promedio es del <strong><?= number_format($margenNeto, 2) ?>%</strong>.</p>
                    
                    <?php if ($crecimientoIngresos != 0): ?>
                    <p>La tendencia de ingresos muestra 
                    <strong><?= $crecimientoIngresos > 0 ? 'crecimiento' : 'decrecimiento' ?> del <?= number_format(abs($crecimientoIngresos), 1) ?>%</strong> 
                    en los últimos 3 meses comparado con el trimestre anterior.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>No se han registrado transacciones financieras suficientes para realizar un análisis detallado. 
                    Se recomienda comenzar con el registro de ventas y compras para generar reportes significativos.</p>
                <?php endif; ?>
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
    $dompdf->stream("Reporte Estado Resultados StockNexus.pdf", ["Attachment" => true]);
    exit;

} catch (Exception $e) {
    die("Error al generar el PDF: " . $e->getMessage());
}