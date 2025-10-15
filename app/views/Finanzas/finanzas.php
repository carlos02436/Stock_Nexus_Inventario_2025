<?php
// app/views/finanzas/finanzas.php

// Incluir el controlador
require_once __DIR__ . '/../../controllers/FinanzaController.php';

// Crear instancia del controlador
$finanzaController = new FinanzaController($db);
$resumen = $finanzaController->getResumenFinanciero();
$ingresosVsEgresos = $finanzaController->getIngresosVsEgresos(6);

// Obtener datos adicionales directamente desde la base de datos
try {
    // Métodos de pago del mes actual
    $queryMetodosPago = "
        SELECT 
            metodo_pago,
            COUNT(*) as cantidad,
            SUM(total_venta) as total
        FROM ventas 
        WHERE estado = 'Pagada' 
        AND MONTH(fecha_venta) = MONTH(CURRENT_DATE()) 
        AND YEAR(fecha_venta) = YEAR(CURRENT_DATE())
        GROUP BY metodo_pago
    ";
    $stmtMetodos = $db->query($queryMetodosPago);
    $metodosPago = $stmtMetodos->fetchAll(PDO::FETCH_ASSOC);

    // Ventas recientes
    $queryVentasRecientes = "
        SELECT 
            codigo_venta,
            fecha_venta,
            total_venta,
            metodo_pago,
            estado
        FROM ventas 
        ORDER BY fecha_venta DESC 
        LIMIT 5
    ";
    $stmtVentas = $db->query($queryVentasRecientes);
    $ventasRecientes = $stmtVentas->fetchAll(PDO::FETCH_ASSOC);

    // Compras recientes
    $queryComprasRecientes = "
        SELECT 
            codigo_compra,
            fecha_compra,
            total_compra,
            estado
        FROM compras 
        ORDER BY fecha_compra DESC 
        LIMIT 5
    ";
    $stmtCompras = $db->query($queryComprasRecientes);
    $comprasRecientes = $stmtCompras->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en finanzas.php: " . $e->getMessage());
    $metodosPago = [];
    $ventasRecientes = [];
    $comprasRecientes = [];
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-chart-line me-2"></i>Panel Financiero</h1>
        <div class="btn-toolbar mb-2 mb-md-2">
            <div class="d-flex gap-2">
                <a href="index.php?page=pagos" class="btn btn-warning rounded-3 px-3 py-2 text-center">
                    <i class="fas fa-credit-card me-2"></i>Pagos
                </a>
                <a href="index.php?page=estado_resultado" class="btn btn-success rounded-3 px-3 py-2 text-center">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Estado de Resultados
                </a>
                <a href="index.php?page=balance" class="btn btn-danger rounded-3 px-3 py-2 text-center">
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
                                Total Ventas Pagadas - <?= date('F Y') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-white-300"></i>
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
                                Total Compras Pagadas - <?= date('F Y') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck-loading fa-2x text-white-300"></i>
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
                                Ingresos - Egresos - <?= date('F Y') ?>
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
    
    <!-- Gráficos y Tablas -->
    <div class="row">
        <!-- Gráfico de Ingresos vs Egresos -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100 shadow-lg border-0">
                <div class="card-header">
                    <h5 class="text-center m-2 fw-bold text-white py-2">Evolución de Ingresos vs Egresos (Últimos 6 Meses)</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center rounded-3">
                    <canvas id="ingresosEgresosChart" style="width: 100%; height: 350px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Métodos de Pago -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100 shadow-lg border-0">
                <div class="card-header">
                    <h5 class="text-center m-2 fw-bold text-white py-2">Métodos de Pago (Ventas del Mes)</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center rounded-3">
                    <canvas id="metodosPagoChart" style="width: 100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tablas de Movimientos Recientes -->
    <div class="row mt-4">
        <!-- Ventas Recientes -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Ventas Recientes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Método</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ventasRecientes as $venta): ?>
                                <tr>
                                    <td><small><?= htmlspecialchars($venta['codigo_venta']) ?></small></td>
                                    <td><small><?= date('d/m/Y', strtotime($venta['fecha_venta'])) ?></small></td>
                                    <td class="text-success"><small><strong>$<?= number_format($venta['total_venta'], 2) ?></strong></small></td>
                                    <td><span class="badge bg-info"><?= htmlspecialchars($venta['metodo_pago']) ?></span></td>
                                    <td>
                                        <span class="badge bg-<?= $venta['estado'] == 'Pagada' ? 'success' : ($venta['estado'] == 'Pendiente' ? 'warning' : 'danger') ?>">
                                            <?= htmlspecialchars($venta['estado']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($ventasRecientes)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle me-2"></i>No hay ventas registradas
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compras Recientes -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-truck-loading me-2"></i>Compras Recientes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comprasRecientes as $compra): ?>
                                <tr>
                                    <td><small><?= htmlspecialchars($compra['codigo_compra']) ?></small></td>
                                    <td><small><?= date('d/m/Y', strtotime($compra['fecha_compra'])) ?></small></td>
                                    <td class="text-danger"><small><strong>$<?= number_format($compra['total_compra'], 2) ?></strong></small></td>
                                    <td>
                                        <span class="badge bg-<?= $compra['estado'] == 'Pagada' ? 'success' : ($compra['estado'] == 'Pendiente' ? 'warning' : 'danger') ?>">
                                            <?= htmlspecialchars($compra['estado']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($comprasRecientes)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle me-2"></i>No hay compras registradas
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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
                        <i class="fas fa-info-circle me-2"></i>Información del Panel Financiero
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-white">
                            <h6>Ingresos</h6>
                            <p class="small text-white mb-0">
                                Suma total de todas las ventas con estado "Pagada" durante el mes actual.
                            </p>
                        </div>
                        <div class="col-md-4 text-white">
                            <h6>Egresos</h6>
                            <p class="small text-white mb-0">
                                Suma total de todas las compras con estado "Pagada" durante el mes actual.
                            </p>
                        </div>
                        <div class="col-md-4 text-white">
                            <h6>Utilidad</h6>
                            <p class="small text-white mb-0">
                                Diferencia entre ingresos y egresos. Representa la ganancia neta del mes.
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
        const [año, mes] = item.mes.split('-');
        const nombreMes = obtenerNombreMes(parseInt(mes));
        labels.push(`${nombreMes} ${año}`);
        ingresosData.push(parseFloat(item.ingresos) || 0);
        egresosData.push(parseFloat(item.egresos) || 0);
    });

    // Gráfico de Ingresos vs Egresos
    const ctxLine = document.getElementById('ingresosEgresosChart').getContext('2d');
    const ingresosEgresosChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: labels.reverse(), // Ordenar cronológicamente
            datasets: [
                {
                    label: 'Ingresos (Ventas)',
                    data: ingresosData.reverse(),
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Egresos (Compras)',
                    data: egresosData.reverse(),
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

    // Gráfico de métodos de pago
    const metodosPagoData = <?= json_encode($metodosPago) ?>;
    const labelsMetodos = metodosPagoData.map(item => item.metodo_pago);
    const dataMetodos = metodosPagoData.map(item => parseFloat(item.total));
    
    // Si no hay datos, mostrar un mensaje
    if (dataMetodos.length === 0) {
        labelsMetodos.push('Sin datos');
        dataMetodos.push(100);
    }

    const ctxDoughnut = document.getElementById('metodosPagoChart').getContext('2d');
    const metodosPagoChart = new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: labelsMetodos,
            datasets: [{
                data: dataMetodos,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
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