<?php
// app/views/finanzas/finanzas.php
$finanzaController = new FinanzaController($db);
$resumen = $finanzaController->getResumenFinanciero();
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-chart-line me-2"></i>Panel Financiero</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="index.php?page=pagos" class="btn btn-neon">Pagos</a>
                <a href="index.php?page=balance" class="btn btn-neon">Balance</a>
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

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-white">Acciones Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="index.php?page=crear_pago&tipo=Ingreso" class="btn btn-success w-100">
                                <i class="fas fa-plus-circle me-2"></i>Registrar Ingreso
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="index.php?page=crear_pago&tipo=Egreso" class="btn btn-danger w-100">
                                <i class="fas fa-minus-circle me-2"></i>Registrar Egreso
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="index.php?page=pagos" class="btn btn-info w-100">
                                <i class="fas fa-list me-2"></i>Ver Todos los Pagos
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="index.php?page=balance" class="btn btn-warning w-100">
                                <i class="fas fa-balance-scale me-2"></i>Ver Balance
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-white">Resumen por Método de Pago</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center text-white">
                    <canvas id="metodoPagoChart" width="400" height="200"></canvas>
                </div>
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