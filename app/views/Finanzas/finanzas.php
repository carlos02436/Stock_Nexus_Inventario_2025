<?php
// app/views/finanzas/finanzas.php

// Incluir el controlador
require_once __DIR__ . '/../../controllers/FinanzaController.php';

// Crear instancia del controlador
$finanzaController = new FinanzaController($db);
$resumen = $finanzaController->getResumenFinanciero();

// Obtener datos adicionales para el gráfico
$ingresosVsEgresos = $finanzaController->getIngresosVsEgresos(6); // Últimos 6 meses
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-chart-line me-2"></i>Panel Financiero</h1>
        <div class="btn-toolbar mb-2 mb-md-2">
            <div class="d-flex gap-2">
                <a href="index.php?page=pagos" class="btn btn-warning rounded-3 px-3 py-2 w-60 text-center">
                    <i class="fas fa-credit-card me-2"></i>Pagos
                </a>
                <a href="index.php?page=estado_resultado" class="btn btn-success rounded-3 px-3 py-2 w-60 text-center">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Estado de Resultados
                </a>
                <a href="index.php?page=balance" class="btn btn-danger rounded-3 px-3 py-2 w-60 text-center">
                    <i class="fas fa-balance-scale me-2"></i>Balance General
                </a>
            </div>
        </div>
    </div>

    <!-- Resumen Financiero -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2" style="border-left: 4px solid #4e73df !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                <i class="fas fa-dollar-sign me-1"></i>Ingresos del Mes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                $<?= number_format($resumen['ingresos_mes'], 2) ?>
                            </div>
                            <div class="text-xs text-white mt-1">
                                <?= date('F Y') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-white-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2" style="border-left: 4px solid #e74a3b !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                <i class="fas fa-money-bill-wave me-1"></i>Egresos del Mes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                $<?= number_format($resumen['egresos_mes'], 2) ?>
                            </div>
                            <div class="text-xs text-white mt-1">
                                <?= date('F Y') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-white-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2" style="border-left: 4px solid #1cc88a !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                <i class="fas fa-chart-line me-1"></i>Utilidad del Mes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                $<?= number_format($resumen['utilidad_mes'], 2) ?>
                            </div>
                            <div class="text-xs text-white mt-1">
                                <?= date('F Y') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-white-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gráficos -->
    <div class="row">
        <!-- Gráfico de Ingresos vs Egresos -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100 shadow-lg border-0" style="min-height: 500px;">
                <div class="card-header">
                    <h5 class="text-center m-2 fw-bold text-white py-2">Evolución de Ingresos vs Egresos (Últimos 6 Meses)</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center rounded-3">
                    <canvas id="ingresosEgresosChart" style="width: 100%; max-width: 850px; height: 420px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Métodos de Pago -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100 shadow-lg border-0">
                <div class="card-header">
                    <h5 class="text-center m-2 fw-bold text-white py-2">Distribución por Tipo de Pago</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center rounded-3">
                    <canvas id="tipoPagoChart" style="width: 100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Información del Panel
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Ingresos</h6>
                            <p class="small text-muted mb-0">
                                Total de pagos registrados como "Ingreso" durante el mes actual.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Egresos</h6>
                            <p class="small text-muted mb-0">
                                Total de pagos registrados como "Egreso" durante el mes actual.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para el gráfico de ingresos vs egresos
    const datosMensuales = <?= json_encode($ingresosVsEgresos) ?>;
    
    // Preparar datos para el gráfico de líneas
    const labels = [];
    const ingresosData = [];
    const egresosData = [];
    
    datosMensuales.forEach(item => {
        const mes = obtenerNombreMes(item.mes);
        labels.push(`${mes} ${item.año}`);
        ingresosData.push(parseFloat(item.ingresos) || 0);
        egresosData.push(parseFloat(item.egresos) || 0);
    });

    // Gráfico de Ingresos vs Egresos
    const ctxLine = document.getElementById('ingresosEgresosChart').getContext('2d');
    const ingresosEgresosChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Ingresos',
                    data: ingresosData,
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Egresos',
                    data: egresosData,
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'white',
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'white'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: 'white',
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += '$' + context.parsed.y.toLocaleString();
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Gráfico de tipos de pago (doughnut)
    const ctxDoughnut = document.getElementById('tipoPagoChart').getContext('2d');
    const tipoPagoChart = new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: ['Ingresos', 'Egresos'],
            datasets: [{
                data: [
                    <?= $resumen['ingresos_mes'] ?>,
                    <?= $resumen['egresos_mes'] ?>
                ],
                backgroundColor: [
                    '#1cc88a',
                    '#e74a3b'
                ],
                borderWidth: 2,
                borderColor: '#2d3748'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'white',
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Función para obtener nombre del mes
    function obtenerNombreMes(numeroMes) {
        const meses = [
            'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
            'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
        ];
        return meses[numeroMes - 1] || '';
    }
});
</script>