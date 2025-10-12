<?php
// app/views/finanzas/finanzas.php
$finanzaController = new FinanzaController($db);
$resumen = $finanzaController->getResumenFinanciero();
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-chart-line me-2"></i>Panel Financiero</h1>
        <div class="btn-toolbar mb-2 mb-md-2">
            <div class="d-flex gap-2">
                <a href="index.php?page=pagos" class="btn btn-warning rounded-3 px-3 py-2 w-60 text-center">
                    Pagos
                </a>
                <a href="index.php?page=estado_resultado" class="btn btn-success rounded-3 px-3 py-2 w-60 text-center">
                    Estado de Resultados
                </a>
                <a href="index.php?page=balance" class="btn btn-danger rounded-3 px-3 py-2 w-60 text-center">
                    Balance General
                </a>
            </div>
        </div>
    </div>

    <!-- Resumen Financiero -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                Ingresos del Mes
                            </div>
                            <div class="h5 mb-0 font-weight-bold  text-white">
                                $<?= number_format($resumen['ingresos_mes'], 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x  text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                Egresos del Mes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                $<?= number_format($resumen['egresos_mes'], 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                Utilidad del Mes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                $<?= number_format($resumen['utilidad_mes'], 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
    <div class="col-lg-8 mb-5 mx-auto">
        <div class="card h-100 shadow-lg border-0" style="min-height: 500px;">
            <div class="card-header">
                <h5 class="text-center m-2 fw-bold text-white py-2">Resumen por Método de Pago</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center rounded-3">
                <canvas id="metodoPagoChart" style="width: 100%; max-width: 850px; height: 420px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de métodos de pago (ejemplo)
    const ctx = document.getElementById('metodoPagoChart').getContext('2d');
    const metodoPagoChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Efectivo', 'Tarjeta', 'Transferencia', 'Crédito'],
            datasets: [{
                data: [40, 25, 20, 15],
                backgroundColor: [
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#ffffff' // Color blanco para las etiquetas
                    }
                }
            }
        }
    });
});
</script>