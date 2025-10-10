<?php
// app/views/finanzas/balance.php

// Primero cargar el modelo BalanceGeneral
$balanceModel = new BalanceGeneral($db);

// Verificar si el método existe, si no, crear una solución alternativa
if (!method_exists($balanceModel, 'obtenerAniosDisponibles')) {
    // Si el método no existe, obtener los años de los balances disponibles
    $balances = $balanceModel->listarBalances(60); // Obtener más balances para tener años
    $aniosDisponibles = [];
    
    foreach ($balances as $balance) {
        $anio = date('Y', strtotime($balance['fecha_balance']));
        if (!in_array($anio, $aniosDisponibles)) {
            $aniosDisponibles[] = $anio;
        }
    }
    
    // Si no hay años, usar el año actual
    if (empty($aniosDisponibles)) {
        $aniosDisponibles = [date('Y')];
    }
    
    // Ordenar años descendente
    rsort($aniosDisponibles);
    
} else {
    // Si el método existe, usarlo normalmente
    $balances = $balanceModel->listarBalances(12);
    $aniosDisponibles = $balanceModel->obtenerAniosDisponibles();
}

$balanceActual = $balanceModel->obtenerBalanceActual();
?>
<div class="container-fluid px-4" style="margin-top: 180px; padding-bottom: 100px;">
    <!-- Header no imprimible -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom no-print">
        <h1 class="h2"><i class="fas fa-balance-scale me-2"></i>Balance General</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button class="btn btn-neon" onclick="imprimirReporte()">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
        </div>
    </div>

    <?php if ($balanceActual): ?>
    <!-- Balance actual - solo pantalla -->
    <div class="d-flex row mb-4 mx-2 no-print">
        <div class="col-md-4 mb-3">
            <div class="card text-white h-100" style="background: linear-gradient(45deg, #4e73df, #224abe);">
                <div class="card-body">
                    <div class="text-center">
                        <h5>Total Ingresos</h5>
                        <h3>$<?= number_format($balanceActual['total_ingresos'], 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white h-100" style="background: linear-gradient(45deg, #e74a3b, #be2617);">
                <div class="card-body">
                    <div class="text-center">
                        <h5>Total Egresos</h5>
                        <h3>$<?= number_format($balanceActual['total_egresos'], 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white h-100" style="background: linear-gradient(45deg, #1cc88a, #13855c);">
                <div class="card-body">
                    <div class="text-center">
                        <h5>Utilidad Neta</h5>
                        <h3>$<?= number_format($balanceActual['utilidad'], 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filtros - no imprimible -->
    <div class="row mx-2 mb-4 no-print">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-3 align-items-end justify-content-center">
                        <div class="col-md-4">
                            <label class="form-label text-white">Filtrar por Año:</label>
                            <select class="form-select" id="filtroAnio">
                                <option value="todos">Todos los años</option>
                                <?php foreach ($aniosDisponibles as $anio): ?>
                                    <option value="<?= $anio ?>"><?= $anio ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-white">Buscar por mes:</label>
                            <input type="text" class="form-control" id="filtroMes" placeholder="Ej: Enero, Febrero, Marzo...">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-danger w-80" onclick="limpiarFiltros()">
                                <i class="fas fa-undo me-1"></i>Limpiar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 1 PARA IMPRESIÓN: Balance Actual + Tabla -->
    <div class="print-section" id="seccionTabla">
        <div class="container-fluid">
            <!-- Header para impresión -->
            <div class="text-center mb-4">
                <h2>Stock Nexus - Balance General</h2>
                <h4>Resumen Financiero</h4>
                <p class="text-muted">Fecha de reporte: <?= date('d/m/Y') ?></p>
                <hr>
            </div>

            <!-- Balance actual para impresión -->
            <?php if ($balanceActual): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h4 class="card-title mb-0 text-center">Resumen Balance Actual</h4>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <h5>Total Ingresos</h5>
                                    <h3 class="text-success">$<?= number_format($balanceActual['total_ingresos'], 2) ?></h3>
                                </div>
                                <div class="col-md-4">
                                    <h5>Total Egresos</h5>
                                    <h3 class="text-danger">$<?= number_format($balanceActual['total_egresos'], 2) ?></h3>
                                </div>
                                <div class="col-md-4">
                                    <h5>Utilidad Neta</h5>
                                    <h3 class="text-primary">$<?= number_format($balanceActual['utilidad'], 2) ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tabla para impresión -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Historial de Balances (Últimos 12 meses)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
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
                                        <?php 
                                        $margen = $balance['total_ingresos'] > 0 ? 
                                            ($balance['utilidad'] / $balance['total_ingresos']) * 100 : 0;
                                        $fecha = date('m/Y', strtotime($balance['fecha_balance']));
                                        ?>
                                        <tr>
                                            <td class="fw-bold"><?= $fecha ?></td>
                                            <td class="text-success fw-bold">$<?= number_format($balance['total_ingresos'], 2) ?></td>
                                            <td class="text-danger fw-bold">$<?= number_format($balance['total_egresos'], 2) ?></td>
                                            <td class="text-primary fw-bold">$<?= number_format($balance['utilidad'], 2) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $margen >= 20 ? 'success' : ($margen >= 10 ? 'warning' : 'danger') ?>">
                                                    <?= number_format($margen, 1) ?>%
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 2 PARA IMPRESIÓN: Gráfico -->
    <div class="print-section" id="seccionGrafico">
        <div class="container-fluid">
            <!-- Header para impresión -->
            <div class="text-center mb-4">
                <h2>Stock Nexus - Balance General</h2>
                <h4>Gráfico de Tendencia</h4>
                <p class="text-muted">Fecha de reporte: <?= date('d/m/Y') ?></p>
                <hr>
            </div>

            <!-- Gráfico para impresión -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-line me-2"></i>Evolución Financiera (Últimos 12 meses)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div style="position: relative; height: 500px;">
                                <canvas id="balanceChartPrint"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTENIDO PARA PANTALLA -->
    <div class="no-print">
        <div class="row mx-2 mb-4">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Historial de Balances
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="tablaBalances">
                                <thead class="table-dark">
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
                                    <?php 
                                    $margen = $balance['total_ingresos'] > 0 ? 
                                        ($balance['utilidad'] / $balance['total_ingresos']) * 100 : 0;
                                    $fecha = date('m/Y', strtotime($balance['fecha_balance']));
                                    $anio = date('Y', strtotime($balance['fecha_balance']));
                                    $mes = date('F', strtotime($balance['fecha_balance']));
                                    $mesEspanol = obtenerMesEspanol($mes);
                                    ?>
                                    <tr data-anio="<?= $anio ?>" data-mes="<?= $mes ?>" data-mes-es="<?= $mesEspanol ?>">
                                        <td class="fw-bold"><?= $fecha ?></td>
                                        <td class="text-success fw-bold">$<?= number_format($balance['total_ingresos'], 2) ?></td>
                                        <td class="text-danger fw-bold">$<?= number_format($balance['total_egresos'], 2) ?></td>
                                        <td class="text-primary fw-bold">$<?= number_format($balance['utilidad'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $margen >= 20 ? 'success' : ($margen >= 10 ? 'warning' : 'danger') ?> fs-6">
                                                <?= number_format($margen, 1) ?>%
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mx-2">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>Gráfico de Tendencia
                        </h5>
                    </div>
                    <div class="card-body text-white" style="background: rgba(0,0,0,0.5);">
                        <div class="chart-container" style="position: relative; height: 400px;">
                            <canvas id="balanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Función para obtener el nombre del mes en español
function obtenerMesEspanol($mesIngles) {
    $meses = [
        'January' => 'Enero',
        'February' => 'Febrero',
        'March' => 'Marzo',
        'April' => 'Abril',
        'May' => 'Mayo',
        'June' => 'Junio',
        'July' => 'Julio',
        'August' => 'Agosto',
        'September' => 'Septiembre',
        'October' => 'Octubre',
        'November' => 'Noviembre',
        'December' => 'Diciembre'
    ];
    return $meses[$mesIngles] ?? $mesIngles;
}
?>

<!-- Estilos para impresión -->
<style>
/* Ocultar secciones de impresión en pantalla */
.print-section {
    display: none;
}

/* Estilos para impresión */
@media print {
    /* Ocultar todo por defecto */
    body * {
        visibility: hidden;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Mostrar solo secciones de impresión */
    .print-section {
        display: block !important;
        visibility: visible !important;
        position: relative !important;
        page-break-after: always;
        width: 100% !important;
        height: auto !important;
    }
    
    /* No mostrar la última página en blanco */
    .print-section:last-child {
        page-break-after: auto;
    }
    
    /* Ocultar elementos no imprimibles */
    .no-print {
        display: none !important;
    }
    
    /* Estilos generales para impresión */
    body {
        background: white !important;
        color: black !important;
        font-size: 12pt;
        font-family: Arial, sans-serif;
    }
    
    .container-fluid {
        width: 100% !important;
        max-width: 100% !important;
        padding: 10px !important;
        margin: 0 !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        background: white !important;
        color: black !important;
        box-shadow: none !important;
        margin-bottom: 15px !important;
    }
    
    .card-header {
        background: #343a40 !important;
        color: white !important;
        border-bottom: 2px solid #000 !important;
        padding: 10px !important;
    }
    
    .card-body {
        padding: 15px !important;
    }
    
    .table {
        color: black !important;
        font-size: 10pt;
        width: 100% !important;
    }
    
    .table-bordered {
        border: 1px solid #000 !important;
    }
    
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #000 !important;
        padding: 6px !important;
    }
    
    .table-dark {
        background: #343a40 !important;
        color: white !important;
    }
    
    .text-success { color: #198754 !important; }
    .text-danger { color: #dc3545 !important; }
    .text-primary { color: #0d6efd !important; }
    
    .bg-success { 
        background-color: #198754 !important; 
        color: white !important;
    }
    .bg-warning { 
        background-color: #ffc107 !important; 
        color: black !important;
    }
    .bg-danger { 
        background-color: #dc3545 !important; 
        color: white !important;
    }
    
    /* Asegurar que el gráfico se imprima */
    canvas {
        max-width: 100% !important;
        height: 400px !important;
        display: block !important;
    }
    
    /* Mejorar espaciado */
    h1, h2, h3, h4, h5 {
        color: black !important;
        margin: 10px 0 !important;
    }
    
    hr {
        border-color: #000 !important;
        margin: 15px 0 !important;
    }
    
    .text-muted {
        color: #6c757d !important;
    }
}

/* Estilo para elementos que no se imprimen */
.no-print {
    display: block;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Variables globales para los gráficos
let balanceChart;
let balanceChartPrint;

document.addEventListener('DOMContentLoaded', function() {
    inicializarGrafico();
    inicializarGraficoImpresion();
    inicializarFiltros();
});

function inicializarGrafico() {
    const ctx = document.getElementById('balanceChart').getContext('2d');
    balanceChart = new Chart(ctx, {
        type: 'line',
        data: getChartData(),
        options: getChartOptions('white')
    });
}

function inicializarGraficoImpresion() {
    const ctx = document.getElementById('balanceChartPrint').getContext('2d');
    balanceChartPrint = new Chart(ctx, {
        type: 'line',
        data: getChartData(),
        options: getChartOptions('black')
    });
}

function getChartData() {
    return {
        labels: [<?= implode(',', array_map(function($b) { 
            return "'" . date('m/Y', strtotime($b['fecha_balance'])) . "'"; 
        }, array_reverse($balances))) ?>],
        datasets: [
            {
                label: 'Ingresos',
                data: [<?= implode(',', array_map(function($b) { 
                    return $b['total_ingresos']; 
                }, array_reverse($balances))) ?>],
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true
            },
            {
                label: 'Egresos',
                data: [<?= implode(',', array_map(function($b) { 
                    return $b['total_egresos']; 
                }, array_reverse($balances))) ?>],
                borderColor: '#e74a3b',
                backgroundColor: 'rgba(231, 74, 59, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true
            },
            {
                label: 'Utilidad',
                data: [<?= implode(',', array_map(function($b) { 
                    return $b['utilidad']; 
                }, array_reverse($balances))) ?>],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true
            }
        ]
    };
}

function getChartOptions(textColor) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: `rgba(0, 0, 0, ${textColor === 'white' ? '0.1' : '0.2'})`
                },
                ticks: {
                    color: textColor,
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            },
            x: {
                grid: {
                    color: `rgba(0, 0, 0, ${textColor === 'white' ? '0.1' : '0.2'})`
                },
                ticks: {
                    color: textColor
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    color: textColor,
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
    };
}

function inicializarFiltros() {
    document.getElementById('filtroMes').addEventListener('input', filtrarTabla);
    document.getElementById('filtroAnio').addEventListener('change', filtrarTabla);
    filtrarTabla();
}

function filtrarTabla() {
    const filtroAnio = document.getElementById('filtroAnio').value;
    const filtroMes = document.getElementById('filtroMes').value.toLowerCase();
    const filas = document.querySelectorAll('#tablaBalances tbody tr');
    
    let filasVisibles = 0;
    
    filas.forEach(fila => {
        const anio = fila.getAttribute('data-anio');
        const mes = fila.getAttribute('data-mes').toLowerCase();
        const mesEspanol = fila.getAttribute('data-mes-es').toLowerCase();
        
        const coincideAnio = filtroAnio === 'todos' || anio === filtroAnio;
        const coincideMes = filtroMes === '' || 
                           mes.includes(filtroMes) || 
                           mesEspanol.includes(filtroMes) ||
                           anio.includes(filtroMes);
        
        if (coincideAnio && coincideMes) {
            fila.style.display = '';
            filasVisibles++;
        } else {
            fila.style.display = 'none';
        }
    });
    
    const mensajeNoResultados = document.getElementById('mensajeNoResultados');
    if (filasVisibles === 0) {
        if (!mensajeNoResultados) {
            const mensaje = document.createElement('tr');
            mensaje.id = 'mensajeNoResultados';
            mensaje.innerHTML = `
                <td colspan="5" class="text-center py-4 text-muted">
                    <i class="fas fa-search me-2"></i>No se encontraron resultados para los filtros aplicados
                </td>
            `;
            document.querySelector('#tablaBalances tbody').appendChild(mensaje);
        }
    } else if (mensajeNoResultados) {
        mensajeNoResultados.remove();
    }
}

function limpiarFiltros() {
    document.getElementById('filtroAnio').value = 'todos';
    document.getElementById('filtroMes').value = '';
    filtrarTabla();
}

function imprimirReporte() {
    // Limpiar filtros temporalmente para imprimir todo
    const filtroAnio = document.getElementById('filtroAnio').value;
    const filtroMes = document.getElementById('filtroMes').value;
    
    document.getElementById('filtroAnio').value = 'todos';
    document.getElementById('filtroMes').value = '';
    filtrarTabla();
    
    // Esperar un momento para que se actualice la tabla
    setTimeout(() => {
        window.print();
        
        // Restaurar filtros después de imprimir
        setTimeout(() => {
            document.getElementById('filtroAnio').value = filtroAnio;
            document.getElementById('filtroMes').value = filtroMes;
            filtrarTabla();
        }, 100);
    }, 100);
}
</script>