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
        <div class="btn-toolbar mb-2 mb-md-2 gap-3">
            <a href="index.php?page=finanzas" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Finanzas
            </a>
            <a href="index.php?page=generar_pdf_balance" class="btn btn-neon">
                <i class="fas fa-file-pdf me-2"></i>Descargar PDF
            </a>
        </div>
    </div>

    <?php if ($balanceActual): ?>
    <!-- Balance actual - solo pantalla -->
    <div class="d-flex row mb-4 mx-2 no-print">
        <div class="col-md-4 mb-3">
            <div class="card text-white h-100">
                <div class="card-body">
                    <div class="text-center">
                        <h5>Total Ingresos</h5>
                        <h3>$<?= number_format($balanceActual['total_ingresos'], 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white h-100">
                <div class="card-body">
                    <div class="text-center">
                        <h5>Total Egresos</h5>
                        <h3>$<?= number_format($balanceActual['total_egresos'], 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white h-100">
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
                <div class="card-header text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-end justify-content-center text-center">
                        <div class="col-md-3">
                            <label class="form-label text-white">Filtrar por Año:</label>
                            <select class="form-select" id="filtroAnio">
                                <option value="todos">Todos los años</option>
                                <?php foreach ($aniosDisponibles as $anio): ?>
                                    <option value="<?= $anio ?>"><?= $anio ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-white">Buscar por mes:</label>
                            <input type="text" class="form-control" id="filtroMes" placeholder="Ej: Enero, Febrero, Marzo...">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-danger w-100 mt-4" onclick="limpiarFiltros()">
                                <i class="fas fa-undo me-1"></i>Limpiar
                            </button>
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

<!-- Incluir las librerías necesarias para PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Variables globales para los gráficos
let balanceChart;

document.addEventListener('DOMContentLoaded', function() {
    inicializarGrafico();
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
    const tbody = document.querySelector('#tablaBalances tbody');
    
    // Remover mensaje anterior si existe
    const mensajeAnterior = document.getElementById('mensajeNoResultados');
    if (mensajeAnterior) {
        mensajeAnterior.remove();
    }
    
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
    
    // Mostrar mensaje si no hay resultados
    if (filasVisibles === 0) {
        const mensaje = document.createElement('tr');
        mensaje.id = 'mensajeNoResultados';
        mensaje.innerHTML = `
            <td colspan="5" class="text-center py-4 text-muted">
                <i class="fas fa-search me-2"></i>No se encontraron resultados para los filtros aplicados
            </td>
        `;
        tbody.appendChild(mensaje);
    }
}

function limpiarFiltros() {
    document.getElementById('filtroAnio').value = 'todos';
    document.getElementById('filtroMes').value = '';
    filtrarTabla();
}

async function generarPDF() {
    // Mostrar loading
    const btnOriginal = document.querySelector('.btn-neon');
    const originalHTML = btnOriginal.innerHTML;
    btnOriginal.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando PDF...';
    btnOriginal.disabled = true;

    try {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        
        // Página 1: Resumen y tabla
        await agregarPaginaResumen(pdf);
        
        // Página 2: Gráfico
        await agregarPaginaGrafico(pdf);
        
        // Descargar el PDF
        pdf.save(`Balance_General_StockNexus_${new Date().toISOString().split('T')[0]}.pdf`);
        
    } catch (error) {
        console.error('Error generando PDF:', error);
        alert('Error al generar el PDF. Por favor, intente nuevamente.');
    } finally {
        // Restaurar botón
        btnOriginal.innerHTML = originalHTML;
        btnOriginal.disabled = false;
    }
}

async function agregarPaginaResumen(pdf) {
    // Título
    pdf.setFontSize(20);
    pdf.setTextColor(0, 0, 0);
    pdf.text('Stock Nexus - Balance General', 105, 20, { align: 'center' });
    
    pdf.setFontSize(14);
    pdf.text('Resumen Financiero', 105, 30, { align: 'center' });
    
    pdf.setFontSize(10);
    pdf.setTextColor(128, 128, 128);
    pdf.text(`Fecha de reporte: ${new Date().toLocaleDateString()}`, 105, 37, { align: 'center' });
    
    // Línea separadora
    pdf.setDrawColor(0, 0, 0);
    pdf.line(20, 42, 190, 42);
    
    // Resumen de balance actual
    if (<?= $balanceActual ? 'true' : 'false' ?>) {
        pdf.setFontSize(12);
        pdf.setTextColor(0, 0, 0);
        pdf.text('RESUMEN BALANCE ACTUAL', 20, 55);
        
        // Cuadro de resumen
        pdf.setFillColor(240, 240, 240);
        pdf.rect(20, 60, 170, 25, 'F');
        pdf.setDrawColor(0, 0, 0);
        pdf.rect(20, 60, 170, 25);
        
        // Datos del resumen
        const ingresos = <?= $balanceActual['total_ingresos'] ?? 0 ?>;
        const egresos = <?= $balanceActual['total_egresos'] ?? 0 ?>;
        const utilidad = <?= $balanceActual['utilidad'] ?? 0 ?>;
        
        pdf.setFontSize(10);
        pdf.text('Total Ingresos:', 30, 70);
        pdf.setTextColor(0, 128, 0);
        pdf.text(`$${ingresos.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, 70, 70);
        
        pdf.setTextColor(0, 0, 0);
        pdf.text('Total Egresos:', 30, 77);
        pdf.setTextColor(255, 0, 0);
        pdf.text(`$${egresos.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, 70, 77);
        
        pdf.setTextColor(0, 0, 0);
        pdf.text('Utilidad Neta:', 30, 84);
        pdf.setTextColor(0, 0, 255);
        pdf.text(`$${utilidad.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, 70, 84);
    }
    
    // Tabla de historial
    pdf.setFontSize(12);
    pdf.setTextColor(0, 0, 0);
    pdf.text('HISTORIAL DE BALANCES (ÚLTIMOS 12 MESES)', 20, 105);
    
    // Encabezados de tabla
    pdf.setFillColor(52, 58, 64);
    pdf.rect(20, 110, 170, 8, 'F');
    pdf.setTextColor(255, 255, 255);
    pdf.setFontSize(9);
    pdf.text('Fecha', 25, 116);
    pdf.text('Ingresos', 60, 116);
    pdf.text('Egresos', 95, 116);
    pdf.text('Utilidad', 130, 116);
    pdf.text('Margen', 165, 116);
    
    // Datos de la tabla
    let yPos = 122;
    pdf.setTextColor(0, 0, 0);
    
    <?php foreach ($balances as $index => $balance): ?>
    <?php 
    $margen = $balance['total_ingresos'] > 0 ? 
        ($balance['utilidad'] / $balance['total_ingresos']) * 100 : 0;
    $fecha = date('m/Y', strtotime($balance['fecha_balance']));
    ?>
    if (yPos > 270) {
        pdf.addPage();
        yPos = 20;
    }
    
    pdf.setFontSize(8);
    pdf.text('<?= $fecha ?>', 25, yPos);
    pdf.setTextColor(0, 128, 0);
    pdf.text('$<?= number_format($balance['total_ingresos'], 2) ?>', 60, yPos);
    pdf.setTextColor(255, 0, 0);
    pdf.text('$<?= number_format($balance['total_egresos'], 2) ?>', 95, yPos);
    pdf.setTextColor(0, 0, 255);
    pdf.text('$<?= number_format($balance['utilidad'], 2) ?>', 130, yPos);
    pdf.setTextColor(0, 0, 0);
    pdf.text('<?= number_format($margen, 1) ?>%', 165, yPos);
    
    yPos += 6;
    <?php endforeach; ?>
}

async function agregarPaginaGrafico(pdf) {
    pdf.addPage();
    
    // Título
    pdf.setFontSize(20);
    pdf.setTextColor(0, 0, 0);
    pdf.text('Stock Nexus - Balance General', 105, 20, { align: 'center' });
    
    pdf.setFontSize(14);
    pdf.text('Gráfico de Tendencia', 105, 30, { align: 'center' });
    
    pdf.setFontSize(10);
    pdf.setTextColor(128, 128, 128);
    pdf.text(`Fecha de reporte: ${new Date().toLocaleDateString()}`, 105, 37, { align: 'center' });
    
    // Línea separadora
    pdf.setDrawColor(0, 0, 0);
    pdf.line(20, 42, 190, 42);
    
    // Título del gráfico
    pdf.setFontSize(12);
    pdf.setTextColor(0, 0, 0);
    pdf.text('EVOLUCIÓN FINANCIERA (ÚLTIMOS 12 MESES)', 20, 55);
    
    // Crear un canvas temporal para el gráfico
    const canvas = document.createElement('canvas');
    canvas.width = 800;
    canvas.height = 400;
    const ctx = canvas.getContext('2d');
    
    // Crear gráfico temporal
    const tempChart = new Chart(ctx, {
        type: 'line',
        data: getChartData(),
        options: {
            ...getChartOptions('black'),
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: 'black',
                        font: {
                            size: 14
                        }
                    }
                }
            }
        }
    });
    
    // Esperar a que el gráfico se renderice
    await new Promise(resolve => setTimeout(resolve, 500));
    
    // Convertir canvas a imagen
    const chartImage = canvas.toDataURL('image/png');
    
    // Agregar imagen al PDF
    pdf.addImage(chartImage, 'PNG', 20, 65, 170, 80);
    
    // Limpiar
    tempChart.destroy();
}
</script>