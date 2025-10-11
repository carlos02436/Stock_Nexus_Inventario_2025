<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'libs/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include 'conexion.php';

// Obtener datos
$sql = "SELECT fecha_balance, total_ingresos, total_egresos, utilidad 
        FROM balance_general 
        ORDER BY fecha_balance DESC";
$resultado = $conn->query($sql);

// Calcular totales
$sql_total = "SELECT SUM(total_ingresos) as total_ing, 
                     SUM(total_egresos) as total_egr,
                     SUM(utilidad) as total_util 
              FROM balance_general";
$total = $conn->query($sql_total)->fetch_assoc();

// HTML para el PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estado de Resultados</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; background-color: #e9ecef; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Estado de Resultados - Stock Nexus</h1>
        <p>Generado: ' . date('d/m/Y H:i:s') . '</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Total Ingresos</th>
                <th>Total Egresos</th>
                <th>Utilidad Neta</th>
                <th>Margen %</th>
            </tr>
        </thead>
        <tbody>';

while($fila = $resultado->fetch_assoc()) {
    $margen = ($fila['utilidad'] / $fila['total_ingresos']) * 100;
    $html .= '
            <tr>
                <td>' . $fila['fecha_balance'] . '</td>
                <td class="text-right">$' . number_format($fila['total_ingresos'], 2) . '</td>
                <td class="text-right">$' . number_format($fila['total_egresos'], 2) . '</td>
                <td class="text-right">$' . number_format($fila['utilidad'], 2) . '</td>
                <td class="text-right">' . number_format($margen, 2) . '%</td>
            </tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="total">
        <h3>Totales Acumulados:</h3>
        <p>Total Ingresos: $' . number_format($total['total_ing'], 2) . '</p>
        <p>Total Egresos: $' . number_format($total['total_egr'], 2) . '</p>
        <p>Total Utilidad: $' . number_format($total['total_util'], 2) . '</p>
    </div>
</body>
</html>';

// Configurar DomPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output del PDF
$dompdf->stream('estado_resultados.pdf', array('Attachment' => true));

$conn->close();
?>