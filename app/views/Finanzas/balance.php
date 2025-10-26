<?php
// app/views/finanzas/balance.php

// Calcular balances en tiempo real desde las tablas de ventas, compras y gastos
try {
    // Obtener años disponibles de las ventas
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
    
    // Si no hay años, usar el año actual
    if (empty($aniosDisponibles)) {
        $aniosDisponibles = [date('Y')];
    }
    
    // Calcular balances mensuales reales - CONSULTA MEJORADA
    $queryBalances = "
        SELECT 
            meses.fecha_balance,
            COALESCE(ingresos.total_ingresos, 0) as total_ingresos,
            COALESCE(compras.total_compras, 0) + COALESCE(gastos.total_gastos, 0) as total_egresos,
            COALESCE(ingresos.total_ingresos, 0) - (COALESCE(compras.total_compras, 0) + COALESCE(gastos.total_gastos, 0)) as utilidad
        FROM (
            -- Generar los últimos 12 meses completos
            SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL n MONTH), '%Y-%m-01') as fecha_balance
            FROM (
                SELECT 0 as n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 
                UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 
                UNION SELECT 9 UNION SELECT 10 UNION SELECT 11
            ) numeros
        ) meses
        LEFT JOIN (
            -- Ingresos por mes (ventas pagadas + otros ingresos)
            SELECT 
                DATE_FORMAT(fecha_venta, '%Y-%m-01') as mes,
                SUM(total_venta) as total_ingresos
            FROM ventas 
            WHERE estado = 'Pagada'
            GROUP BY DATE_FORMAT(fecha_venta, '%Y-%m-01')
        ) ingresos ON meses.fecha_balance = ingresos.mes
        LEFT JOIN (
            -- Compras por mes (compras pagadas)
            SELECT 
                DATE_FORMAT(fecha_compra, '%Y-%m-01') as mes,
                SUM(total_compra) as total_compras
            FROM compras 
            WHERE estado = 'Pagada'
            GROUP BY DATE_FORMAT(fecha_compra, '%Y-%m-01')
        ) compras ON meses.fecha_balance = compras.mes
        LEFT JOIN (
            -- Gastos por mes
            SELECT 
                DATE_FORMAT(fecha, '%Y-%m-01') as mes,
                SUM(valor) as total_gastos
            FROM gastos_operativos 
            GROUP BY DATE_FORMAT(fecha, '%Y-%m-01')
        ) gastos ON meses.fecha_balance = gastos.mes
        ORDER BY meses.fecha_balance DESC
        LIMIT 12
    ";
    
    $stmtBalances = $db->prepare($queryBalances);
    $stmtBalances->execute();
    $balances = $stmtBalances->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular balance actual (mes actual)
    $queryBalanceActual = "
        SELECT 
            'Balance Actual' as periodo,
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
            ), 0) + COALESCE((
                SELECT SUM(valor) 
                FROM gastos_operativos 
                WHERE fecha >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                AND fecha <= CURDATE()
            ), 0) as total_egresos,
            COALESCE((
                SELECT SUM(total_venta) 
                FROM ventas 
                WHERE estado = 'Pagada' 
                AND fecha_venta >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                AND fecha_venta <= CURDATE()
            ), 0) - (
                COALESCE((
                    SELECT SUM(total_compra) 
                    FROM compras 
                    WHERE estado = 'Pagada'
                    AND fecha_compra >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                    AND fecha_compra <= CURDATE()
                ), 0) + COALESCE((
                    SELECT SUM(valor) 
                    FROM gastos_operativos 
                    WHERE fecha >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                    AND fecha <= CURDATE()
                ), 0)
            ) as utilidad
    ";
    
    $stmtBalanceActual = $db->prepare($queryBalanceActual);
    $stmtBalanceActual->execute();
    $balanceActual = $stmtBalanceActual->fetch(PDO::FETCH_ASSOC);
    
    // Calcular totales acumulados para el PDF
    $queryTotalesAcumulados = "
        SELECT 
            COALESCE(SUM(total_ingresos), 0) as total_ingresos,
            COALESCE(SUM(total_egresos), 0) as total_egresos,
            COALESCE(SUM(utilidad), 0) as utilidad_neta
        FROM ($queryBalances) as balances_totales
    ";
    
    $stmtTotales = $db->prepare($queryTotalesAcumulados);
    $stmtTotales->execute();
    $totalesAcumulados = $stmtTotales->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // En caso de error, inicializar variables vacías
    $aniosDisponibles = [date('Y')];
    $balances = [];
    $balanceActual = null;
    $totalesAcumulados = ['total_ingresos' => 0, 'total_egresos' => 0, 'utilidad_neta' => 0];
    error_log("Error en balance.php: " . $e->getMessage());
}
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

    <?php if ($balanceActual && ($balanceActual['total_ingresos'] > 0 || $balanceActual['total_egresos'] > 0)): ?>
    <!-- Balance actual - solo pantalla -->
    <div class="d-flex row mb-4 mx-2 no-print">
        <div class="col-md-4 mb-3">
            <div class="card text-white h-100" style="border-left: 4px solid #ffea00ff !important;">
                <div class="card-body">
                    <div class="text-center">
                        <h5>Total Ingresos</h5>
                        <h3>$<?= number_format($balanceActual['total_ingresos'], 2) ?></h3>
                        <small>Mes Actual (<?= date('F Y') ?>)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white h-100" style="border-left: 4px solid #0dff00ff !important;">
                <div class="card-body">
                    <div class="text-center">
                        <h5>Total Egresos</h5>
                        <h3>$<?= number_format($balanceActual['total_egresos'], 2) ?></h3>
                        <small>Mes Actual (<?= date('F Y') ?>)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white h-100" style="border-left: 4px solid #ff006aff !important;">
                <div class="card-body">
                    <div class="text-center">
                        <h5>Utilidad Neta</h5>
                        <h3>$<?= number_format($balanceActual['utilidad'], 2) ?></h3>
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
                            <i class="fas fa-chart-bar me-2"></i>Historial de Balances (Últimos 12 Meses)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="tablaBalances">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Periodo</th>
                                        <th>Ingresos</th>
                                        <th>Egresos</th>
                                        <th>Utilidad</th>
                                        <th>Margen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($balances)): ?>
                                        <?php foreach ($balances as $balance): ?>
                                        <?php 
                                        $margen = $balance['total_ingresos'] > 0 ? 
                                            ($balance['utilidad'] / $balance['total_ingresos']) * 100 : 0;
                                        $fecha = date('M/Y', strtotime($balance['fecha_balance']));
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
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="fas fa-info-circle me-2"></i>No hay datos de balances disponibles
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

        <?php if (!empty($balances)): ?>
        <div class="row mx-2">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>Gráfico de Tendencia
                        </h5>
                    </div>
                    <div class="card-body" style="background: rgba(0,0,0,0.05);">
                        <div class="chart-container" style="position: relative; height: 400px;">
                            <canvas id="balanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
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
let balanceChart;

document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($balances)): ?>
    inicializarGrafico();
    <?php endif; ?>
    inicializarFiltros();
});

<?php if (!empty($balances)): ?>
function inicializarGrafico() {
    const ctx = document.getElementById('balanceChart').getContext('2d');
    balanceChart = new Chart(ctx, {
        type: 'line',
        data: getChartData(),
        options: getChartOptions('black')
    });
}

function getChartData() {
    return {
        labels: [<?= implode(',', array_map(function($b) { 
            return "'" . date('M/Y', strtotime($b['fecha_balance'])) . "'"; 
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

function inicializarFiltros() {
    const filtroMes = document.getElementById('filtroMes');
    const filtroAnio = document.getElementById('filtroAnio');
    
    if (filtroMes) filtroMes.addEventListener('input', filtrarTabla);
    if (filtroAnio) filtroAnio.addEventListener('change', filtrarTabla);
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
</script>