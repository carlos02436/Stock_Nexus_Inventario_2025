<?php
date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../../libs/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../app/models/BalanceGeneral.php';

try {
    $fechaGeneracion = date('d/m/Y h:i:s a');
    $balanceModel = new BalanceGeneral($db);
    $balances = $balanceModel->listarBalances(12);
    $totalesAcumulados = $balanceModel->obtenerTotalesPorMes($mes);

    $labels = [];
    $ingresos = [];
    $egresos = [];
    $utilidades = [];

    foreach (array_reverse($balances) as $balance) {
        $labels[] = date('m/Y', strtotime($balance['fecha_balance']));
        $ingresos[] = $balance['total_ingresos'];
        $egresos[] = $balance['total_egresos'];
        $utilidades[] = $balance['utilidad'];
    }

    // --- Gráfico
    $chartConfig = [
        "type" => "line",
        "data" => [
            "labels" => $labels,
            "datasets" => [
                ["label" => "Ingresos", "data" => $ingresos, "borderColor" => "#00ff88", "fill" => false, "tension" => 0.4],
                ["label" => "Egresos", "data" => $egresos, "borderColor" => "#ff5c5c", "fill" => false, "tension" => 0.4],
                ["label" => "Utilidad", "data" => $utilidades, "borderColor" => "#4e9bff", "fill" => false, "tension" => 0.4]
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
    </style>
</head>
<body>
<div class="container">
    <h1>Stock Nexus - Estado de Resultados</h1>
    <h3>Resumen Financiero</h3>
    <p style="text-align:center;color:gray;">Fecha de reporte: <?= $fechaGeneracion ?></p>
    <hr>

    <div class="resumen">
        <div class="total-acumulado">
            <h3>Balance Total Actual (Últimos 12 meses)</h3>
            <p><strong>Total Ingresos:</strong> <span class="text-green">$<?= number_format($totalesAcumulados['total_ingresos'], 2) ?></span></p>
            <p><strong>Total Egresos:</strong> <span class="text-red">$<?= number_format($totalesAcumulados['total_egresos'], 2) ?></span></p>
            <p><strong>Utilidad Neta:</strong> <span class="text-blue">$<?= number_format($totalesAcumulados['utilidad_neta'], 2) ?></span></p>
        </div>
    </div>

    <h3>Detalle Mensual</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Mes / Año</th>
                <th>Total Ingresos</th>
                <th>Total Egresos</th>
                <th>Utilidad</th>
                <th>Margen %</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($balances as $balance): 
            $margen = $balance['total_ingresos'] > 0 ? ($balance['utilidad'] / $balance['total_ingresos']) * 100 : 0;
        ?>
            <tr>
                <td><?= date('m/Y', strtotime($balance['fecha_balance'])) ?></td>
                <td class="text-green">$<?= number_format($balance['total_ingresos'], 2) ?></td>
                <td class="text-red">$<?= number_format($balance['total_egresos'], 2) ?></td>
                <td><strong>$<?= number_format($balance['utilidad'], 2) ?></strong></td>
                <td><?= number_format($margen, 2) ?>%</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="page-break"></div>

<div class="container">
    <div class="grafico">
        <h2>Evolución Financiera (Últimos 12 Meses)</h2>
        <?php if (!empty($chartBase64)): ?>
            <img src="<?= $chartBase64 ?>" style="width:100%; max-width:650px;">
        <?php else: ?>
            <p style="color:#dc3545;font-style:italic;">No se pudo generar el gráfico.</p>
        <?php endif; ?>
    </div>
</div>

<div class="footer">
    Stock Nexus © <?= date('Y') ?> — Generado el <?= $fechaGeneracion ?>
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
    $dompdf->stream("Reporte_Estado_Resultados.pdf", ["Attachment" => true]);
    exit;

} catch (Exception $e) {
    die("Error al generar el PDF: " . $e->getMessage());
}