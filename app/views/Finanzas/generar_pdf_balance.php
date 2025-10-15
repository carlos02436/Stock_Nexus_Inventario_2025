<?php
// --- Cargar Dompdf ---
require_once __DIR__ . '/../../libs/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// --- Incluir conexión ---
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
    // --- Capturar fecha y hora EXACTA de generación ---
    date_default_timezone_set('America/Bogota');
    $fechaGeneracion = date('d/m/Y h:i:s A');
    
    // --- Obtener conexión a la base de datos ---
    // Asumiendo que $db ya está creada en database.php

    // --- Verificar si se enviaron fechas para filtrar ---
    $filtroPorFecha = false;
    $fechaInicio = null;
    $fechaFin = null;

    if (isset($_REQUEST['fecha_inicio']) && isset($_REQUEST['fecha_fin'])) {
        $fechaInicio = $_REQUEST['fecha_inicio'];
        $fechaFin = $_REQUEST['fecha_fin'];
        $filtroPorFecha = true;
    }

    // --- Calcular balances mensuales reales ---
    if ($filtroPorFecha) {
        // Filtro por rango de fechas
        $queryBalances = "
            SELECT 
                DATE_FORMAT(fecha, '%Y-%m-01') as fecha_balance,
                COALESCE(ingresos.total_ingresos, 0) as total_ingresos,
                COALESCE(compras.total_compras, 0) + COALESCE(gastos.total_gastos, 0) as total_egresos,
                COALESCE(ingresos.total_ingresos, 0) - (COALESCE(compras.total_compras, 0) + COALESCE(gastos.total_gastos, 0)) as utilidad
            FROM (
                SELECT DATE_FORMAT( fecha, '%Y-%m-01' ) as fecha
                FROM (
                    SELECT DATE_ADD(?, INTERVAL (a.a + (10 * b.a)) MONTH) as fecha
                    FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                    CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                ) a
                WHERE fecha <= ?
            ) meses
            LEFT JOIN (
                SELECT 
                    DATE_FORMAT(fecha_venta, '%Y-%m-01') as mes,
                    SUM(total_venta) as total_ingresos
                FROM ventas 
                WHERE estado = 'Pagada'
                GROUP BY DATE_FORMAT(fecha_venta, '%Y-%m-01')
            ) ingresos ON meses.fecha = ingresos.mes
            LEFT JOIN (
                SELECT 
                    DATE_FORMAT(fecha_compra, '%Y-%m-01') as mes,
                    SUM(total_compra) as total_compras
                FROM compras 
                WHERE estado = 'Pagada'
                GROUP BY DATE_FORMAT(fecha_compra, '%Y-%m-01')
            ) compras ON meses.fecha = compras.mes
            LEFT JOIN (
                SELECT 
                    DATE_FORMAT(fecha, '%Y-%m-01') as mes,
                    SUM(valor) as total_gastos
                FROM gastos_operativos 
                GROUP BY DATE_FORMAT(fecha, '%Y-%m-01')
            ) gastos ON meses.fecha = gastos.mes
            WHERE (ingresos.total_ingresos IS NOT NULL OR compras.total_compras IS NOT NULL OR gastos.total_gastos IS NOT NULL)
            ORDER BY meses.fecha ASC
        ";

        $stmtBalances = $db->prepare($queryBalances);
        $stmtBalances->execute([$fechaInicio, $fechaFin]);
        $balances = $stmtBalances->fetchAll(PDO::FETCH_ASSOC);

        // Calcular totales acumulados para el rango
        $queryTotales = "
            SELECT 
                COALESCE(SUM(ingresos.total_ingresos), 0) as total_ingresos,
                COALESCE(SUM(compras.total_compras), 0) + COALESCE(SUM(gastos.total_gastos), 0) as total_egresos,
                COALESCE(SUM(ingresos.total_ingresos), 0) - (COALESCE(SUM(compras.total_compras), 0) + COALESCE(SUM(gastos.total_gastos), 0)) as utilidad_neta
            FROM (
                SELECT DATE_FORMAT( fecha, '%Y-%m-01' ) as fecha
                FROM (
                    SELECT DATE_ADD(?, INTERVAL (a.a + (10 * b.a)) MONTH) as fecha
                    FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                    CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                ) a
                WHERE fecha <= ?
            ) meses
            LEFT JOIN (
                SELECT 
                    DATE_FORMAT(fecha_venta, '%Y-%m-01') as mes,
                    SUM(total_venta) as total_ingresos
                FROM ventas 
                WHERE estado = 'Pagada'
                GROUP BY DATE_FORMAT(fecha_venta, '%Y-%m-01')
            ) ingresos ON meses.fecha = ingresos.mes
            LEFT JOIN (
                SELECT 
                    DATE_FORMAT(fecha_compra, '%Y-%m-01') as mes,
                    SUM(total_compra) as total_compras
                FROM compras 
                WHERE estado = 'Pagada'
                GROUP BY DATE_FORMAT(fecha_compra, '%Y-%m-01')
            ) compras ON meses.fecha = compras.mes
            LEFT JOIN (
                SELECT 
                    DATE_FORMAT(fecha, '%Y-%m-01') as mes,
                    SUM(valor) as total_gastos
                FROM gastos_operativos 
                GROUP BY DATE_FORMAT(fecha, '%Y-%m-01')
            ) gastos ON meses.fecha = gastos.mes
        ";

        $stmtTotales = $db->prepare($queryTotales);
        $stmtTotales->execute([$fechaInicio, $fechaFin]);
        $totalesAcumulados = $stmtTotales->fetch(PDO::FETCH_ASSOC);

    } else {
        // Últimos 12 meses (comportamiento por defecto)
        $queryBalances = "
            SELECT 
                DATE_FORMAT(fecha, '%Y-%m-01') as fecha_balance,
                COALESCE(ingresos.total_ingresos, 0) as total_ingresos,
                COALESCE(compras.total_compras, 0) + COALESCE(gastos.total_gastos, 0) as total_egresos,
                COALESCE(ingresos.total_ingresos, 0) - (COALESCE(compras.total_compras, 0) + COALESCE(gastos.total_gastos, 0)) as utilidad
            FROM (
                SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL n MONTH), '%Y-%m-01') as fecha
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
            ) ingresos ON meses.fecha = ingresos.mes
            LEFT JOIN (
                SELECT 
                    DATE_FORMAT(fecha_compra, '%Y-%m-01') as mes,
                    SUM(total_compra) as total_compras
                FROM compras 
                WHERE estado = 'Pagada'
                GROUP BY DATE_FORMAT(fecha_compra, '%Y-%m-01')
            ) compras ON meses.fecha = compras.mes
            LEFT JOIN (
                SELECT 
                    DATE_FORMAT(fecha, '%Y-%m-01') as mes,
                    SUM(valor) as total_gastos
                FROM gastos_operativos 
                GROUP BY DATE_FORMAT(fecha, '%Y-%m-01')
            ) gastos ON meses.fecha = gastos.mes
            WHERE (ingresos.total_ingresos IS NOT NULL OR compras.total_compras IS NOT NULL OR gastos.total_gastos IS NOT NULL)
            ORDER BY meses.fecha ASC
        ";

        $stmtBalances = $db->prepare($queryBalances);
        $stmtBalances->execute();
        $balances = $stmtBalances->fetchAll(PDO::FETCH_ASSOC);

        // Calcular totales acumulados para los últimos 12 meses
        $queryTotales = "
            SELECT 
                COALESCE(SUM(ingresos.total_ingresos), 0) as total_ingresos,
                COALESCE(SUM(compras.total_compras), 0) + COALESCE(SUM(gastos.total_gastos), 0) as total_egresos,
                COALESCE(SUM(ingresos.total_ingresos), 0) - (COALESCE(SUM(compras.total_compras), 0) + COALESCE(SUM(gastos.total_gastos), 0)) as utilidad_neta
            FROM (
                SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL n MONTH), '%Y-%m-01') as fecha
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
            ) ingresos ON meses.fecha = ingresos.mes
            LEFT JOIN (
                SELECT 
                    DATE_FORMAT(fecha_compra, '%Y-%m-01') as mes,
                    SUM(total_compra) as total_compras
                FROM compras 
                WHERE estado = 'Pagada'
                GROUP BY DATE_FORMAT(fecha_compra, '%Y-%m-01')
            ) compras ON meses.fecha = compras.mes
            LEFT JOIN (
                SELECT 
                    DATE_FORMAT(fecha, '%Y-%m-01') as mes,
                    SUM(valor) as total_gastos
                FROM gastos_operativos 
                GROUP BY DATE_FORMAT(fecha, '%Y-%m-01')
            ) gastos ON meses.fecha = gastos.mes
        ";

        $stmtTotales = $db->prepare($queryTotales);
        $stmtTotales->execute();
        $totalesAcumulados = $stmtTotales->fetch(PDO::FETCH_ASSOC);
    }

    // ANÁLISIS DINÁMICO BASADO EN DATOS REALES
    $analisis = [
        'positivos' => [],
        'mejoras' => []
    ];

    // Calcular métricas para el análisis
    $margenPromedio = $totalesAcumulados['total_ingresos'] > 0 ? 
        ($totalesAcumulados['utilidad_neta'] / $totalesAcumulados['total_ingresos']) * 100 : 0;
    
    $relacion = $totalesAcumulados['total_egresos'] > 0 ? 
        ($totalesAcumulados['total_ingresos'] / $totalesAcumulados['total_egresos']) : 0;

    // Análisis de tendencia mensual
    $mesesConUtilidad = 0;
    $mesesConPerdida = 0;
    $totalMeses = count($balances);
    $tendenciaIngresos = 'estable';
    $tendenciaUtilidad = 'estable';
    
    if ($totalMeses >= 2) {
        $primerMes = $balances[0];
        $ultimoMes = $balances[count($balances) - 1];
        
        $crecimientoIngresos = $primerMes['total_ingresos'] > 0 ? 
            (($ultimoMes['total_ingresos'] - $primerMes['total_ingresos']) / $primerMes['total_ingresos']) * 100 : 0;
        
        $crecimientoUtilidad = $primerMes['utilidad'] != 0 ? 
            (($ultimoMes['utilidad'] - $primerMes['utilidad']) / abs($primerMes['utilidad'])) * 100 : 0;
        
        $tendenciaIngresos = $crecimientoIngresos > 5 ? 'creciente' : ($crecimientoIngresos < -5 ? 'decreciente' : 'estable');
        $tendenciaUtilidad = $crecimientoUtilidad > 5 ? 'creciente' : ($crecimientoUtilidad < -5 ? 'decreciente' : 'estable');
    }

    foreach ($balances as $balance) {
        if ($balance['utilidad'] > 0) {
            $mesesConUtilidad++;
        } else {
            $mesesConPerdida++;
        }
    }

    // GENERAR ANÁLISIS POSITIVO BASADO EN DATOS REALES
    if ($margenPromedio > 15) {
        $analisis['positivos'][] = "Margen neto excelente del " . number_format($margenPromedio, 1) . "%";
    } elseif ($margenPromedio > 0) {
        $analisis['positivos'][] = "Margen neto positivo del " . number_format($margenPromedio, 1) . "%";
    }

    if ($relacion >= 1.5) {
        $analisis['positivos'][] = "Relación ingresos/egresos saludable (" . number_format($relacion, 2) . ":1)";
    }

    if ($mesesConUtilidad > ($totalMeses * 0.7)) {
        $analisis['positivos'][] = "Rentabilidad consistente en " . $mesesConUtilidad . " de " . $totalMeses . " meses";
    }

    if ($tendenciaIngresos === 'creciente') {
        $analisis['positivos'][] = "Tendencia creciente en ingresos";
    }

    if ($tendenciaUtilidad === 'creciente') {
        $analisis['positivos'][] = "Tendencia creciente en utilidades";
    }

    // GENERAR ÁREAS DE MEJORA BASADO EN DATOS REALES
    if ($margenPromedio < 0) {
        $analisis['mejoras'][] = "Pérdida neta acumulada - revisar estructura de costos";
    } elseif ($margenPromedio < 10) {
        $analisis['mejoras'][] = "Margen neto bajo - optimizar rentabilidad";
    }

    if ($relacion < 1.2) {
        $analisis['mejoras'][] = "Relación ingresos/egresos crítica (" . number_format($relacion, 2) . ":1)";
    }

    if ($mesesConPerdida > ($totalMeses * 0.4)) {
        $analisis['mejoras'][] = "Inestabilidad en " . $mesesConPerdida . " meses - mejorar consistencia";
    }

    if ($tendenciaIngresos === 'decreciente') {
        $analisis['mejoras'][] = "Tendencia decreciente en ingresos";
    }

    if ($tendenciaUtilidad === 'decreciente') {
        $analisis['mejoras'][] = "Tendencia decreciente en utilidades";
    }

    // Si no hay datos suficientes, mostrar mensajes por defecto
    if (empty($analisis['positivos']) && $totalesAcumulados['total_ingresos'] > 0) {
        $analisis['positivos'][] = "Sistema generando reportes correctamente";
        $analisis['positivos'][] = "Datos financieros registrados en el sistema";
    }

    if (empty($analisis['mejoras']) && $totalesAcumulados['total_ingresos'] > 0) {
        $analisis['mejoras'][] = "Mantener el monitoreo regular del desempeño";
        $analisis['mejoras'][] = "Continuar con las estrategias actuales";
    }

    // Si no hay datos en absoluto
    if ($totalesAcumulados['total_ingresos'] == 0) {
        $analisis['positivos'][] = "Sistema operativo y listo para registrar transacciones";
        $analisis['mejoras'][] = "Iniciar registro de ventas y compras para generar reportes";
        $analisis['mejoras'][] = "Capturar datos financieros para análisis detallado";
    }

    // --- PREPARAR DATOS PARA EL GRÁFICO ---
    $labels = [];
    $ingresos = [];
    $egresos = [];
    $utilidades = [];

    foreach (array_reverse($balances) as $balance) {
        $mesEspanol = obtenerMesEspanol($balance['fecha_balance']);
        $año = date('Y', strtotime($balance['fecha_balance']));
        $labels[] = $mesEspanol . ' ' . $año;
        $ingresos[] = $balance['total_ingresos'];
        $egresos[] = $balance['total_egresos'];
        $utilidades[] = $balance['utilidad'];
    }

    // --- Crear imagen del gráfico con manejo de errores ---
    $chartConfig = [
        "type" => "line",
        "data" => [
            "labels" => $labels,
            "datasets" => [
                ["label" => "Ingresos", "data" => $ingresos, "borderColor" => "#1cc88a", "fill" => false],
                ["label" => "Egresos", "data" => $egresos, "borderColor" => "#e74a3b", "fill" => false],
                ["label" => "Utilidad", "data" => $utilidades, "borderColor" => "#4e73df", "fill" => false]
            ]
        ],
        "options" => [
            "plugins" => ["legend" => ["position" => "top"]],
            "scales" => ["y" => ["beginAtZero" => true]]
        ]
    ];

    $chartUrl = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartConfig));
    $chartData = @file_get_contents($chartUrl);
    
    if ($chartData === false) {
        $chartBase64 = ''; // Imagen vacía si hay error
    } else {
        $chartBase64 = 'data:image/png;base64,' . base64_encode($chartData);
    }

    // --- Capturar contenido HTML ---
    ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Balance General - Stock Nexus</title>
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
        .interpretacion {
            margin-top: 30px; 
            padding: 15px; 
            background: #f8f9fa; 
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .interpretacion h4 {
            color: #003366;
            margin-bottom: 15px;
            text-align: center;
        }
        .interpretacion-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 15px;
        }
        .indicadores-positivos h5 {
            color: #28a745; 
            margin-bottom: 8px;
        }
        .areas-mejora h5 {
            color: #dc3545; 
            margin-bottom: 8px;
        }
        .interpretacion ul {
            font-size: 11px; 
            color: #333;
            margin: 0;
            padding-left: 0;
            list-style-type: none;
        }
        .interpretacion li {
            margin-bottom: 8px;
            line-height: 1.4;
        }
        .analisis-item {
            font-size: 11px;
            line-height: 1.4;
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
    <h1>Stock Nexus - Balance General</h1>
    <h3>Resumen Financiero</h3>
    <p style="text-align:center;color:gray;">Fecha de reporte: <?= $fechaGeneracion ?></p>
    <p style="text-align:center;color:gray;">
        <?php if ($filtroPorFecha): ?>
            Período: <?= date('d/m/Y', strtotime($fechaInicio)) ?> - <?= date('d/m/Y', strtotime($fechaFin)) ?>
        <?php else: ?>
            Últimos 12 meses
        <?php endif; ?>
    </p>
    <hr>

    <div class="resumen">
        <div class="total-acumulado">
            <h3>Balance Total Actual (<?php echo $filtroPorFecha ? 'Período Personalizado' : 'Últimos 12 meses'; ?>)</h3>
            <p><strong>Total Ingresos:</strong> <span class="text-green">$<?= number_format($totalesAcumulados['total_ingresos'], 2) ?></span></p>
            <p><strong>Total Egresos:</strong> <span class="text-red">$<?= number_format($totalesAcumulados['total_egresos'], 2) ?></span></p>
            <p><strong>Utilidad Neta:</strong> <span class="text-blue">$<?= number_format($totalesAcumulados['utilidad_neta'], 2) ?></span></p>
        </div>
    </div>

    <h3>Historial de Balances (<?php echo $filtroPorFecha ? 'Período Personalizado' : 'Últimos 12 meses'; ?>)</h3>
    <table>
        <thead>
            <tr>
                <th>Periodo</th>
                <th>Ingresos</th>
                <th>Egresos</th>
                <th>Utilidad</th>
                <th>Margen</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($balances as $balance): 
            $margen = $balance['total_ingresos'] > 0 ? ($balance['utilidad'] / $balance['total_ingresos']) * 100 : 0;
            $mesEspanol = obtenerMesEspanol($balance['fecha_balance']);
            $año = date('Y', strtotime($balance['fecha_balance']));
        ?>
            <tr>
                <td><strong><?= $mesEspanol . ' ' . $año ?></strong></td>
                <td class="text-green">$<?= number_format($balance['total_ingresos'], 2) ?></td>
                <td class="text-red">$<?= number_format($balance['total_egresos'], 2) ?></td>
                <td class="text-blue">$<?= number_format($balance['utilidad'], 2) ?></td>
                <td><?= number_format($margen, 1) ?>%</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="page-break"></div>

    <h2>Gráfico de Tendencia</h2>
    <p style="text-align:center;color:gray;">Evolución Financiera (<?php echo $filtroPorFecha ? 'Período Personalizado' : 'Últimos 12 meses'; ?>)</p>
    <div style="text-align:center; margin-top:30px;">
        <?php if (!empty($chartBase64)): ?>
            <img src="<?= $chartBase64 ?>" style="width:100%; max-width:650px;">
        <?php else: ?>
            <p style="color: #dc3545; font-style: italic;">El gráfico no está disponible temporalmente.</p>
        <?php endif; ?>
    </div>

    <!-- Sección de Interpretación Dinámica -->
    <div class="interpretacion">
        <h4>Análisis del Balance General Basado en Datos Reales</h4>
        <div class="interpretacion-grid">
            <div class="indicadores-positivos">
                <h5>Aspectos Destacados</h5>
                <ul>
                    <?php foreach ($analisis['positivos'] as $positivo): ?>
                    <li class="analisis-item">✓ <?= htmlspecialchars($positivo) ?></li>
                    <?php endforeach; ?>
                    <?php if (empty($analisis['positivos'])): ?>
                    <li class="analisis-item">• Esperando datos para análisis</li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="areas-mejora">
                <h5>Oportunidades de Mejora</h5>
                <ul>
                    <?php foreach ($analisis['mejoras'] as $mejora): ?>
                    <li class="analisis-item">⚠ <?= htmlspecialchars($mejora) ?></li>
                    <?php endforeach; ?>
                    <?php if (empty($analisis['mejoras'])): ?>
                    <li class="analisis-item">• Sin áreas críticas identificadas</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <!-- Análisis de Rentabilidad -->
        <div style="margin-top: 20px; padding: 15px; background: white; border-radius: 5px; border: 1px solid #ddd;">
            <h5 style="color: #003366; margin-bottom: 10px; text-align: center;">Métricas Clave de Rentabilidad</h5>
            <div style="font-size: 11px; color: #333;">
                <p><strong>Margen Neto Promedio:</strong> 
                    <?php 
                    echo number_format($margenPromedio, 2) . '%';
                    ?>
                    <?php if ($margenPromedio >= 20): ?>
                        <span class="badge badge-success"> Excelente</span>
                    <?php elseif ($margenPromedio >= 10): ?>
                        <span class="badge badge-warning"> Bueno</span>
                    <?php else: ?>
                        <span class="badge badge-danger"> Necesita mejora</span>
                    <?php endif; ?>
                </p>
                <p><strong>Relación Ingresos/Egresos:</strong> 
                    <?php 
                    echo number_format($relacion, 2) . ':1';
                    ?>
                    <?php if ($relacion >= 1.5): ?>
                        <span class="badge badge-success"> Saludable</span>
                    <?php elseif ($relacion >= 1.2): ?>
                        <span class="badge badge-warning"> Aceptable</span>
                    <?php else: ?>
                        <span class="badge badge-danger"> Crítico</span>
                    <?php endif; ?>
                </p>
                <p><strong>Estabilidad Financiera:</strong> 
                    <?= $mesesConUtilidad ?> meses rentables de <?= $totalMeses ?> 
                    (<?= number_format(($mesesConUtilidad/$totalMeses)*100, 1) ?>%)
                </p>
                <?php if ($totalMeses >= 2): ?>
                <p><strong>Tendencia General:</strong> 
                    <?= ucfirst($tendenciaIngresos) ?> en ingresos, 
                    <?= ucfirst($tendenciaUtilidad) ?> en utilidades
                </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Resumen Ejecutivo -->
        <div style="margin-top: 15px; padding: 15px; background: #e8f4fd; border-radius: 5px;">
            <h5 style="color: #003366; margin-bottom: 8px; text-align: center;">Resumen Ejecutivo</h5>
            <div style="font-size: 11px; color: #333; line-height: 1.5;">
                <?php if ($totalesAcumulados['total_ingresos'] > 0): ?>
                    <p>El balance general analizado muestra 
                    <strong><?= $mesesConUtilidad ?> periodos con utilidad</strong> y 
                    <strong><?= $mesesConPerdida ?> periodos con pérdida</strong>. 
                    La relación ingresos/egresos es de <strong><?= number_format($relacion, 2) ?>:1</strong> 
                    con un margen neto promedio del <strong><?= number_format($margenPromedio, 2) ?>%</strong>.</p>
                    
                    <?php if ($tendenciaIngresos !== 'estable' || $tendenciaUtilidad !== 'estable'): ?>
                    <p>Se observa una tendencia <strong><?= $tendenciaIngresos ?></strong> en ingresos 
                    y <strong><?= $tendenciaUtilidad ?></strong> en utilidades durante el período analizado.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>No se han registrado transacciones financieras suficientes para realizar un análisis detallado. 
                    Se recomienda comenzar con el registro de ventas y compras para generar reportes significativos.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer profesional -->
    <div class="footer">
        <div class="footer-content">
            Stock Nexus © <?= date('Y') ?> — Generado el <?= $fechaGeneracion ?>
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
    $dompdf->stream("Reporte Balance General StockNexus.pdf", ["Attachment" => true]);
    exit;

} catch (Exception $e) {
    // Manejar errores gracefuly
    die("Error al generar el PDF: " . $e->getMessage());
}