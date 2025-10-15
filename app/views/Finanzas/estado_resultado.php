<?php
// app/views/finanzas/estado_resultado.php

// Calcular estado de resultados en tiempo real desde las tablas
try {
    // Obtener años disponibles
    $queryAnios = "SELECT DISTINCT YEAR(fecha_venta) as anio 
                   FROM ventas 
                   WHERE estado = 'Pagada'
                   UNION
                   SELECT DISTINCT YEAR(fecha_compra) as anio 
                   FROM compras 
                   WHERE estado = 'Pagada'
                   UNION
                   SELECT DISTINCT YEAR(fecha) as anio 
                   FROM gastos_operativos
                   ORDER BY anio DESC";
    $stmtAnios = $db->prepare($queryAnios);
    $stmtAnios->execute();
    $aniosDisponibles = $stmtAnios->fetchAll(PDO::FETCH_COLUMN, 0);
    
    if (empty($aniosDisponibles)) {
        $aniosDisponibles = [date('Y')];
    }
    
    // Calcular estado de resultados mensuales - CONSULTA MEJORADA
    $queryEstadoResultados = "
        SELECT 
            meses.fecha_periodo,
            COALESCE(ingresos.total_ingresos, 0) as total_ingresos,
            COALESCE(compras.total_compras, 0) as costo_ventas,
            COALESCE(gastos.total_gastos, 0) as gastos_operativos,
            COALESCE(ingresos.total_ingresos, 0) - COALESCE(compras.total_compras, 0) as utilidad_bruta,
            (COALESCE(ingresos.total_ingresos, 0) - COALESCE(compras.total_compras, 0)) - COALESCE(gastos.total_gastos, 0) as utilidad_neta
        FROM (
            SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL n MONTH), '%Y-%m-01') as fecha_periodo
            FROM (
                SELECT 0 as n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 
                UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 
                UNION SELECT 9 UNION SELECT 10 UNION SELECT 11
            ) numeros
        ) meses
        LEFT JOIN (
            SELECT 
                DATE_FORMAT(fecha_venta, '%Y-%m-01') as mes,
                SUM(total_venta) as total_ingresos
            FROM ventas 
            WHERE estado = 'Pagada'
            GROUP BY DATE_FORMAT(fecha_venta, '%Y-%m-01')
        ) ingresos ON meses.fecha_periodo = ingresos.mes
        LEFT JOIN (
            SELECT 
                DATE_FORMAT(fecha_compra, '%Y-%m-01') as mes,
                SUM(total_compra) as total_compras
            FROM compras 
            WHERE estado = 'Pagada'
            GROUP BY DATE_FORMAT(fecha_compra, '%Y-%m-01')
        ) compras ON meses.fecha_periodo = compras.mes
        LEFT JOIN (
            SELECT 
                DATE_FORMAT(fecha, '%Y-%m-01') as mes,
                SUM(valor) as total_gastos
            FROM gastos_operativos 
            GROUP BY DATE_FORMAT(fecha, '%Y-%m-01')
        ) gastos ON meses.fecha_periodo = gastos.mes
        ORDER BY meses.fecha_periodo DESC
        LIMIT 12
    ";
    
    $stmtEstado = $db->prepare($queryEstadoResultados);
    $stmtEstado->execute();
    $estadoResultados = $stmtEstado->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular estado actual (mes actual)
    $queryEstadoActual = "
        SELECT 
            'Estado Actual' as periodo,
            COALESCE((
                SELECT SUM(total_venta) 
                FROM ventas 
                WHERE estado = 'Pagada' 
                AND fecha_venta >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                AND fecha_venta <= CURDATE()
            ), 0) as total_ingresos,
            COALESCE((
                SELECT SUM(total_compra) 
                FROM compras 
                WHERE estado = 'Pagada'
                AND fecha_compra >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                AND fecha_compra <= CURDATE()
            ), 0) as costo_ventas,
            COALESCE((
                SELECT SUM(valor) 
                FROM gastos_operativos 
                WHERE fecha >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                AND fecha <= CURDATE()
            ), 0) as gastos_operativos,
            COALESCE((
                SELECT SUM(total_venta) 
                FROM ventas 
                WHERE estado = 'Pagada' 
                AND fecha_venta >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                AND fecha_venta <= CURDATE()
            ), 0) - COALESCE((
                SELECT SUM(total_compra) 
                FROM compras 
                WHERE estado = 'Pagada'
                AND fecha_compra >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                AND fecha_compra <= CURDATE()
            ), 0) as utilidad_bruta,
            (COALESCE((
                SELECT SUM(total_venta) 
                FROM ventas 
                WHERE estado = 'Pagada' 
                AND fecha_venta >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                AND fecha_venta <= CURDATE()
            ), 0) - COALESCE((
                SELECT SUM(total_compra) 
                FROM compras 
                WHERE estado = 'Pagada'
                AND fecha_compra >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                AND fecha_compra <= CURDATE()
            ), 0)) - COALESCE((
                SELECT SUM(valor) 
                FROM gastos_operativos 
                WHERE fecha >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                AND fecha <= CURDATE()
            ), 0) as utilidad_neta
    ";
    
    $stmtEstadoActual = $db->prepare($queryEstadoActual);
    $stmtEstadoActual->execute();
    $estadoActual = $stmtEstadoActual->fetch(PDO::FETCH_ASSOC);
    
    // Calcular totales acumulados para el PDF
    $queryTotalesAcumulados = "
        SELECT 
            COALESCE(SUM(total_ingresos), 0) as total_ingresos,
            COALESCE(SUM(costo_ventas), 0) as total_costo_ventas,
            COALESCE(SUM(gastos_operativos), 0) as total_gastos,
            COALESCE(SUM(utilidad_bruta), 0) as total_utilidad_bruta,
            COALESCE(SUM(utilidad_neta), 0) as total_utilidad_neta
        FROM ($queryEstadoResultados) as estado_totales
    ";
    
    $stmtTotales = $db->prepare($queryTotalesAcumulados);
    $stmtTotales->execute();
    $totalesAcumulados = $stmtTotales->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $aniosDisponibles = [date('Y')];
    $estadoResultados = [];
    $estadoActual = null;
    $totalesAcumulados = [
        'total_ingresos' => 0, 
        'total_costo_ventas' => 0, 
        'total_gastos' => 0, 
        'total_utilidad_bruta' => 0, 
        'total_utilidad_neta' => 0
    ];
    error_log("Error en estado_resultado.php: " . $e->getMessage());
}
?>
<div class="container-fluid px-4" style="margin-top: 180px; padding-bottom: 100px;">
    <!-- Header no imprimible -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom no-print">
        <h1 class="h2"><i class="fas fa-money-bill-wave me-2"></i>Estado de Resultados</h1>
        <div class="btn-toolbar mb-2 mb-md-2 gap-3">
            <a href="index.php?page=finanzas" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Finanzas
            </a>
            <a href="index.php?page=generar_pdf_estado_resultado" class="btn btn-neon">
                <i class="fas fa-file-pdf me-2"></i>Descargar PDF
            </a>
        </div>
    </div>

    <?php if ($estadoActual && ($estadoActual['total_ingresos'] > 0 || $estadoActual['costo_ventas'] > 0)): ?>
    <!-- Estado actual - solo pantalla -->
    <div class="d-flex row mb-4 mx-2 no-print">
        <div class="col-md-3 mb-3">
            <div class="card text-white h-100">
                <div class="card-body">
                    <div class="text-center">
                        <h5>Ingresos Totales</h5>
                        <h3>$<?= number_format($estadoActual['total_ingresos'], 2) ?></h3>
                        <small>Mes Actual (<?= date('F Y') ?>)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white h-100">
                <div class="card-body">
                    <div class="text-center">
                        <h5>Utilidad Bruta</h5>
                        <h3>$<?= number_format($estadoActual['utilidad_bruta'], 2) ?></h3>
                        <small>Mes Actual (<?= date('F Y') ?>)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white h-100">
                <div class="card-body">
                    <div class="text-center">
                        <h5>Gastos Operativos</h5>
                        <h3>$<?= number_format($estadoActual['gastos_operativos'], 2) ?></h3>
                        <small>Mes Actual (<?= date('F Y') ?>)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white h-100">
                <div class="card-body">
                    <div class="text-center">
                        <h5>Utilidad Neta</h5>
                        <h3>$<?= number_format($estadoActual['utilidad_neta'], 2) ?></h3>
                        <small>Mes Actual (<?= date('F Y') ?>)</small>
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
                    <div class="row g-3 align-items-end justify-content-center">
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
                            <i class="fas fa-chart-bar me-2"></i>Estado de Resultados (Últimos 12 Meses)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="tablaEstadoResultados">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Periodo</th>
                                        <th>Ingresos Totales</th>
                                        <th>Costo de Ventas</th>
                                        <th>Utilidad Bruta</th>
                                        <th>Gastos Operativos</th>
                                        <th>Utilidad Neta</th>
                                        <th>Margen Neto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($estadoResultados)): ?>
                                        <?php foreach ($estadoResultados as $estado): ?>
                                        <?php 
                                        $margenNeto = $estado['total_ingresos'] > 0 ? 
                                            ($estado['utilidad_neta'] / $estado['total_ingresos']) * 100 : 0;
                                        $fecha = date('M/Y', strtotime($estado['fecha_periodo']));
                                        $anio = date('Y', strtotime($estado['fecha_periodo']));
                                        $mes = date('F', strtotime($estado['fecha_periodo']));
                                        $mesEspanol = obtenerMesEspanol($mes);
                                        ?>
                                        <tr data-anio="<?= $anio ?>" data-mes="<?= $mes ?>" data-mes-es="<?= $mesEspanol ?>">
                                            <td class="fw-bold"><?= $fecha ?></td>
                                            <td class="text-success fw-bold">$<?= number_format($estado['total_ingresos'], 2) ?></td>
                                            <td class="text-danger">$<?= number_format($estado['costo_ventas'], 2) ?></td>
                                            <td class="text-warning fw-bold">$<?= number_format($estado['utilidad_bruta'], 2) ?></td>
                                            <td class="text-info">$<?= number_format($estado['gastos_operativos'], 2) ?></td>
                                            <td class="text-primary fw-bold">$<?= number_format($estado['utilidad_neta'], 2) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $margenNeto >= 20 ? 'success' : ($margenNeto >= 10 ? 'warning' : 'danger') ?> fs-6">
                                                    <?= number_format($margenNeto, 1) ?>%
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="fas fa-info-circle me-2"></i>No hay datos de estado de resultados disponibles
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

        <?php if (!empty($estadoResultados)): ?>
        <div class="row mx-2">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>Gráfico de Tendencia - Estado de Resultados
                        </h5>
                    </div>
                    <div class="card-body" style="background: rgba(0,0,0,0.05);">
                        <div class="chart-container" style="position: relative; height: 400px;">
                            <canvas id="estadoResultadosChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Totales Acumulados -->
    <div class="row mx-2 mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calculator me-2"></i>Totales Acumulados (12 Meses)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 mb-3">
                            <h6 class="text-white">Total Ingresos</h6>
                            <h4 class="text-success">$<?= number_format($totalesAcumulados['total_ingresos'], 2) ?></h4>
                        </div>
                        <div class="col-md-2 mb-3">
                            <h6 class="text-white">Total Costo Ventas</h6>
                            <h4 class="text-danger">$<?= number_format($totalesAcumulados['total_costo_ventas'], 2) ?></h4>
                        </div>
                        <div class="col-md-2 mb-3">
                            <h6 class="text-white">Utilidad Bruta</h6>
                            <h4 class="text-warning">$<?= number_format($totalesAcumulados['total_utilidad_bruta'], 2) ?></h4>
                        </div>
                        <div class="col-md-2 mb-3">
                            <h6 class="text-white">Total Gastos</h6>
                            <h4 class="text-info">$<?= number_format($totalesAcumulados['total_gastos'], 2) ?></h4>
                        </div>
                        <div class="col-md-2 mb-3">
                            <h6 class="text-white">Utilidad Neta</h6>
                            <h4 class="text-primary">$<?= number_format($totalesAcumulados['total_utilidad_neta'], 2) ?></h4>
                        </div>
                        <div class="col-md-2 mb-3">
                            <h6 class="text-white">Margen Neto Prom.</h6>
                            <h4 class="<?= $totalesAcumulados['total_ingresos'] > 0 ? ($totalesAcumulados['total_utilidad_neta'] / $totalesAcumulados['total_ingresos'] * 100 >= 20 ? 'text-success' : ($totalesAcumulados['total_utilidad_neta'] / $totalesAcumulados['total_ingresos'] * 100 >= 10 ? 'text-warning' : 'text-danger')) : 'text-muted' ?>">
                                <?= $totalesAcumulados['total_ingresos'] > 0 ? number_format($totalesAcumulados['total_utilidad_neta'] / $totalesAcumulados['total_ingresos'] * 100, 1) : '0.0' ?>%
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Función para obtener el nombre del mes en español
function obtenerMesEspanol($mesEspañol) {
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
    return $meses[$mesEspañol] ?? $mesEspañol;
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Variables globales para los gráficos
let estadoResultadosChart;

document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($estadoResultados)): ?>
    inicializarGraficoEstadoResultados();
    <?php endif; ?>
    inicializarFiltrosEstadoResultados();
});

<?php if (!empty($estadoResultados)): ?>
function inicializarGraficoEstadoResultados() {
    const ctx = document.getElementById('estadoResultadosChart').getContext('2d');
    estadoResultadosChart = new Chart(ctx, {
        type: 'line',
        data: getChartDataEstadoResultados(),
        options: getChartOptionsEstadoResultados('black')
    });
}

function getChartDataEstadoResultados() {
    return {
        labels: [<?= implode(',', array_map(function($e) { 
            return "'" . date('M/Y', strtotime($e['fecha_periodo'])) . "'"; 
        }, array_reverse($estadoResultados))) ?>],
        datasets: [
            {
                label: 'Ingresos Totales',
                data: [<?= implode(',', array_map(function($e) { 
                    return $e['total_ingresos']; 
                }, array_reverse($estadoResultados))) ?>],
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true
            },
            {
                label: 'Utilidad Bruta',
                data: [<?= implode(',', array_map(function($e) { 
                    return $e['utilidad_bruta']; 
                }, array_reverse($estadoResultados))) ?>],
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true
            },
            {
                label: 'Utilidad Neta',
                data: [<?= implode(',', array_map(function($e) { 
                    return $e['utilidad_neta']; 
                }, array_reverse($estadoResultados))) ?>],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true
            }
        ]
    };
}

function getChartOptionsEstadoResultados(textColor) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: `rgba(255, 255, 255, 0.1)`
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
                    color: `rgba(0, 0, 0, 0.1)`
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
                    label: {
                        color: 'white'
                    },
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
<?php endif; ?>

function inicializarFiltrosEstadoResultados() {
    const filtroMes = document.getElementById('filtroMes');
    const filtroAnio = document.getElementById('filtroAnio');
    
    if (filtroMes) filtroMes.addEventListener('input', filtrarTablaEstadoResultados);
    if (filtroAnio) filtroAnio.addEventListener('change', filtrarTablaEstadoResultados);
    filtrarTablaEstadoResultados();
}

function filtrarTablaEstadoResultados() {
    const filtroAnio = document.getElementById('filtroAnio').value;
    const filtroMes = document.getElementById('filtroMes').value.toLowerCase();
    const filas = document.querySelectorAll('#tablaEstadoResultados tbody tr');
    const tbody = document.querySelector('#tablaEstadoResultados tbody');
    
    // Remover mensaje anterior si existe
    const mensajeAnterior = document.getElementById('mensajeNoResultados');
    if (mensajeAnterior) {
        mensajeAnterior.remove();
    }
    
    let filasVisibles = 0;
    
    filas.forEach(fila => {
        // Saltar la fila de mensaje de no datos
        if (fila.cells.length === 1) return;
        
        const anio = fila.getAttribute('data-anio');
        const mes = fila.getAttribute('data-mes')?.toLowerCase() || '';
        const mesEspanol = fila.getAttribute('data-mes-es')?.toLowerCase() || '';
        
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
    if (filasVisibles === 0 && filas.length > 0) {
        const mensaje = document.createElement('tr');
        mensaje.id = 'mensajeNoResultados';
        mensaje.innerHTML = `
            <td colspan="7" class="text-center py-4 text-muted">
                <i class="fas fa-search me-2"></i>No se encontraron resultados para los filtros aplicados
            </td>
        `;
        tbody.appendChild(mensaje);
    }
}

function limpiarFiltros() {
    document.getElementById('filtroAnio').value = 'todos';
    document.getElementById('filtroMes').value = '';
    filtrarTablaEstadoResultados();
}
</script>