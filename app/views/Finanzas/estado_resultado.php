<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../app/models/BalanceGeneral.php';

// Instanciar modelo
$balanceModel = new BalanceGeneral($db);

// Obtener datos
$balances = $balanceModel->listarBalances(12);
$totalesAcumulados = $balanceModel->obtenerTotalesPorMes($mes);

// Preparar datos para el gráfico
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

// Crear URL para el gráfico
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
?>
<body>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-money-bill-wave me-2"></i>Estado de Resultados</h1>
        <div class="btn-toolbar mb-2 mb-md-2">
            <div class="d-flex gap-2">
                <a href="index.php?page=finanzas" class="btn btn-secondary rounded-3 px-3 py-2">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Finanzas
                </a>
                <a href="index.php?page=generar_pdf_estado_resultado" class="btn btn-neon rounded-3 px-3 py-2">
                    <i class="fas fa-file-pdf me-2"></i>Descargar PDF
                </a>
            </div>
        </div>
    </div>

        <!-- Tarjetas de Resumen -->
        <div class="d-flex row mb-4 mx-2 no-print">
                <div class="col-md-4 mb-3">
                    <div class="card text-white h-100">
                        <div class="card-body">
                            <div class="text-center">
                                <h5 class="card-title">Total Ingresos</h5>
                                <h3 class="text-white"><strong>$<?= number_format($totalesAcumulados['total_ingresos'], 2) ?></strong></h3>
                                <small class="text-white">Acumulado total</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card text-white h-100">
                        <div class="card-body">
                            <div class="text-center">
                                <h5 class="card-title">Total Egresos</h5>
                                <h3 class="text-white"><strong>$<?= number_format($totalesAcumulados['total_egresos'], 2) ?></strong></h3>
                                <small class="text-white">Acumulado total</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card text-white h-100">
                        <div class="card-body">
                            <div class="text-center">
                                <h5 class="card-title">Utilidad Neta</h5>
                                <h3 class="text-white"><strong>$<?= number_format($totalesAcumulados['utilidad_neta'], 2) ?></strong></h3>
                                <small class="text-white">Resultado neto</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico -->
            <div class="card mb-4">
                <div class="card-header text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Tendencia Financiera (Últimos 12 meses)
                    </h5>
                </div>
                <div class="card-body text-center">
                    <img src="<?= $chartUrl ?>" alt="Gráfico de Tendencia" class="img-fluid" style="max-width: 800px;">
                </div>
            </div>

            <!-- Tabla de Estado de Resultados -->
            <div class="card">
                <div class="card-header text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>Historial de Balances (Últimos 12 meses)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Total Ingresos</th>
                                    <th>Total Egresos</th>
                                    <th>Utilidad Neta</th>
                                    <th>Margen %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($balances as $balance): ?>
                                <?php 
                                $margen = $balance['total_ingresos'] > 0 ? ($balance['utilidad'] / $balance['total_ingresos']) * 100 : 0;
                                $margenClass = $margen >= 0 ? 'text-success' : 'text-danger';
                                ?>
                                <tr>
                                    <td><strong><?= date('m/Y', strtotime($balance['fecha_balance'])) ?></strong></td>
                                    <td class="text-success">$<?= number_format($balance['total_ingresos'], 2) ?></td>
                                    <td class="text-danger">$<?= number_format($balance['total_egresos'], 2) ?></td>
                                    <td><strong class="text-info">$<?= number_format($balance['utilidad'], 2) ?></strong></td>
                                    <td>
                                        <span class="<?= $margenClass ?> fw-bold">
                                            <?= number_format($margen, 2) ?>%
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Información del Reporte</h6>
                        <p class="mb-0">
                            Este reporte muestra el estado de resultados de los últimos 12 meses. 
                            Los totales acumulados representan la suma de todos los registros en el sistema.
                        </p>
                    </div>
                </div>
            </div>
        </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>