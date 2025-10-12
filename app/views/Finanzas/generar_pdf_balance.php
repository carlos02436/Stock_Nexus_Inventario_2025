<?php
// --- Cargar Dompdf ---
require_once __DIR__ . '/../../libs/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// --- Incluir conexión y modelo ---
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../app/models/BalanceGeneral.php';

try {
    // --- Capturar fecha y hora EXACTA de generación ---
    date_default_timezone_set('America/Bogota');

    // --- Capturar fecha y hora EXACTA de generación ---
    $fechaGeneracion = date('d/m/Y h:i:s A');
    
    // --- Instanciar modelo ---
    $balanceModel = new BalanceGeneral($db);

    // --- Obtener datos con filtros ---
    if (isset($_REQUEST['fecha_inicio']) && isset($_REQUEST['fecha_fin'])) {
        $fechaInicio = $_REQUEST['fecha_inicio'];
        $fechaFin = $_REQUEST['fecha_fin'];
        $balances = $balanceModel->listarPorMes();
        $totalesAcumulados = $balanceModel->obtenerBalancesMensuales($año);
    } else {
        // Por defecto, últimos 12 meses
        $balances = $balanceModel->listarBalances(12);
        $totalesAcumulados = $balanceModel->obtenerTotalesPorMes();
    }

    // --- PREPARAR DATOS PARA EL GRÁFICO ---
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
    </style>
</head>
<body>
<div class="container">
    <h1>Stock Nexus - Balance General</h1>
    <h3>Resumen Financiero</h3>
    <p style="text-align:center;color:gray;">Fecha de reporte: <?= $fechaGeneracion ?></p>
    <p style="text-align:center;color:gray;">
        <?php if (isset($fechaInicio) && isset($fechaFin)): ?>
            Período: <?= date('d/m/Y', strtotime($fechaInicio)) ?> - <?= date('d/m/Y', strtotime($fechaFin)) ?>
        <?php else: ?>
            Últimos 12 meses
        <?php endif; ?>
    </p>
    <hr>

    <div class="resumen">
        <div class="total-acumulado">
            <h3>Balance Total Actual (<?php echo (isset($fechaInicio) && isset($fechaFin)) ? 'Período Personalizado' : 'Últimos 12 meses'; ?>)</h3>
            <p><strong>Total Ingresos:</strong> <span class="text-green">$<?= number_format($totalesAcumulados['total_ingresos'], 2) ?></span></p>
            <p><strong>Total Egresos:</strong> <span class="text-red">$<?= number_format($totalesAcumulados['total_egresos'], 2) ?></span></p>
            <p><strong>Utilidad Neta:</strong> <span class="text-blue">$<?= number_format($totalesAcumulados['utilidad_neta'], 2) ?></span></p>
        </div>
    </div>

    <h3>Historial de Balances (<?php echo (isset($fechaInicio) && isset($fechaFin)) ? 'Período Personalizado' : 'Últimos 12 meses'; ?>)</h3>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Ingresos</th>
                <th>Egresos</th>
                <th>Utilidad</th>
                <th>Margen</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($balances as $balance): ?>
            <?php $margen = $balance['total_ingresos'] > 0 ? ($balance['utilidad'] / $balance['total_ingresos']) * 100 : 0; ?>
            <tr>
                <td><?= date('m/Y', strtotime($balance['fecha_balance'])) ?></td>
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
    <p style="text-align:center;color:gray;">Evolución Financiera (<?php echo (isset($fechaInicio) && isset($fechaFin)) ? 'Período Personalizado' : 'Últimos 12 meses'; ?>)</p>
    <div style="text-align:center; margin-top:30px;">
        <?php if (!empty($chartBase64)): ?>
            <img src="<?= $chartBase64 ?>" style="width:100%; max-width:650px;">
        <?php else: ?>
            <p style="color: #dc3545; font-style: italic;">El gráfico no está disponible temporalmente. Por favor, instale la extensión GD de PHP.</p>
        <?php endif; ?>
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
    $dompdf->stream("Reporte Balance General.pdf", ["Attachment" => true]);
    exit;

} catch (Exception $e) {
    // Manejar errores gracefuly
    die("Error al generar el PDF: " . $e->getMessage());
}