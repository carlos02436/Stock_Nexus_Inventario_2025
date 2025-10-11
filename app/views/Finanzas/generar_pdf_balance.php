<?php
// --- Cargar Dompdf (manejo de rutas robusto) ---
$autoloadPath1 = __DIR__ . '/../../../vendor/autoload.php';
$autoloadPath2 = __DIR__ . '/../../vendor/autoload.php';
$autoloadPath3 = __DIR__ . '/vendor/autoload.php';

if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} elseif (file_exists($autoloadPath2)) {
    require_once $autoloadPath2;
} elseif (file_exists($autoloadPath3)) {
    require_once $autoloadPath3;
} else {
    die("⚠️ Error: No se encontró el archivo autoload.php. 
         Ejecuta en la raíz del proyecto: composer require dompdf/dompdf");
}

// --- Usar Dompdf ---
use Dompdf\Dompdf;

// --- Incluir conexión y modelo ---
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../app/models/BalanceGeneral.php';

// --- Instanciar modelo ---
$balanceModel = new BalanceGeneral($db);

// --- Obtener datos ---
$balances = $balanceModel->listarBalances(12);
$balanceActual = $balanceModel->obtenerBalanceActual();

// --- Preparar datos para el gráfico ---
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

// --- Crear imagen del gráfico ---
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
$chartData = file_get_contents($chartUrl);
$chartBase64 = 'data:image/png;base64,' . base64_encode($chartData);

// --- Comienza buffer ---
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Balance General - Stock Nexus</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 30px; color: #000; }
        h1, h2, h3 { text-align: center; color: #003366; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: center; }
        th { background-color: #003366; color: white; font-weight: bold; }
        .resumen { background: #f4f4f4; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .text-green { color: #28a745; }
        .text-red { color: #dc3545; }
        .text-blue { color: #007bff; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

<h1>Stock Nexus - Balance General</h1>
<h3>Resumen Financiero</h3>
<p style="text-align:center;color:gray;">Fecha de reporte: <?= date('d/m/Y') ?></p>
<hr>

<div class="resumen">
    <h3>Balance Actual</h3>
    <p><strong>Total Ingresos:</strong> <span class="text-green">$<?= number_format($balanceActual['total_ingresos'], 2) ?></span></p>
    <p><strong>Total Egresos:</strong> <span class="text-red">$<?= number_format($balanceActual['total_egresos'], 2) ?></span></p>
    <p><strong>Utilidad Neta:</strong> <span class="text-blue">$<?= number_format($balanceActual['utilidad'], 2) ?></span></p>
</div>

<h3>Historial de Balances (Últimos 12 meses)</h3>
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
<p style="text-align:center;color:gray;">Evolución Financiera (Últimos 12 meses)</p>
<div style="text-align:center; margin-top:30px;">
    <img src="<?= $chartBase64 ?>" style="width:100%; max-width:650px;">
</div>

</body>
</html>
<?php
$html = ob_get_clean();

// --- Crear PDF ---
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Balance_General.pdf", ["Attachment" => true]);
exit;
?>