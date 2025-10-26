<?php
// app/views/reportes/reporte_finanzas.php
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

// Incluir el controlador
require_once __DIR__ . '/../../controllers/FinanzaController.php';

// Crear instancia del controlador
$finanzaController = new FinanzaController($db);
$resumen = $finanzaController->getResumenFinanciero();
$ingresosVsEgresos = $finanzaController->getIngresosVsEgresos(12);

// Obtener datos del período seleccionado
try {
    // Ventas del período
    $queryVentasPeriodo = "
        SELECT 
            codigo_venta,
            fecha_venta,
            total_venta,
            metodo_pago,
            estado,
            nombre_cliente
        FROM ventas 
        LEFT JOIN clientes ON ventas.id_cliente = clientes.id_cliente
        WHERE fecha_venta BETWEEN :fecha_inicio AND :fecha_fin
        ORDER BY fecha_venta DESC
    ";
    $stmtVentas = $db->prepare($queryVentasPeriodo);
    $stmtVentas->execute([
        ':fecha_inicio' => $fecha_inicio,
        ':fecha_fin' => $fecha_fin
    ]);
    $ventasPeriodo = $stmtVentas->fetchAll(PDO::FETCH_ASSOC);

    // Compras del período
    $queryComprasPeriodo = "
        SELECT 
            codigo_compra,
            fecha_compra,
            total_compra,
            estado,
            nombre_proveedor
        FROM compras 
        LEFT JOIN proveedores ON compras.id_proveedor = proveedores.id_proveedor
        WHERE fecha_compra BETWEEN :fecha_inicio AND :fecha_fin
        ORDER BY fecha_compra DESC
    ";
    $stmtCompras = $db->prepare($queryComprasPeriodo);
    $stmtCompras->execute([
        ':fecha_inicio' => $fecha_inicio,
        ':fecha_fin' => $fecha_fin
    ]);
    $comprasPeriodo = $stmtCompras->fetchAll(PDO::FETCH_ASSOC);

    // Métodos de pago del período
    $queryMetodosPago = "
        SELECT 
            metodo_pago,
            COUNT(*) as cantidad,
            SUM(total_venta) as total
        FROM ventas 
        WHERE estado = 'Pagada' 
        AND fecha_venta BETWEEN :fecha_inicio AND :fecha_fin
        GROUP BY metodo_pago
        ORDER BY total DESC
    ";
    $stmtMetodos = $db->prepare($queryMetodosPago);
    $stmtMetodos->execute([
        ':fecha_inicio' => $fecha_inicio,
        ':fecha_fin' => $fecha_fin
    ]);
    $metodosPago = $stmtMetodos->fetchAll(PDO::FETCH_ASSOC);

    // Calcular estadísticas del período
    $ingresosPeriodo = array_sum(array_column(
        array_filter($ventasPeriodo, fn($v) => $v['estado'] == 'Pagada'), 
        'total_venta'
    ));
    
    $egresosPeriodo = array_sum(array_column(
        array_filter($comprasPeriodo, fn($c) => $c['estado'] == 'Pagada'), 
        'total_compra'
    ));
    
    $utilidadPeriodo = $ingresosPeriodo - $egresosPeriodo;
    
    $ventasPagadasPeriodo = count(array_filter($ventasPeriodo, fn($v) => $v['estado'] == 'Pagada'));
    $ventasPendientesPeriodo = count(array_filter($ventasPeriodo, fn($v) => $v['estado'] == 'Pendiente'));
    $ventasAnuladasPeriodo = count(array_filter($ventasPeriodo, fn($v) => $v['estado'] == 'Anulada'));

} catch (PDOException $e) {
    error_log("Error en reporte_finanzas.php: " . $e->getMessage());
    $ventasPeriodo = [];
    $comprasPeriodo = [];
    $metodosPago = [];
    $ingresosPeriodo = 0;
    $egresosPeriodo = 0;
    $utilidadPeriodo = 0;
}
?>
<div class="container-fluid px-4 mb-5 text-white" style="margin-top:180px;">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom border-light">
        <h1 class="h2"><i class="fas fa-chart-line me-2"></i>Reporte Financiero</h1>
        <div class="btn-toolbar mb-2 mb-md-2">
            <a href="index.php?page=reportes" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver a Reportes
            </a>
            <a href="index.php?page=generar_pdf_finanzas&tipo=finanzas&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>" 
               class="btn btn-danger me-2">
                <i class="fas fa-file-pdf me-2"></i>PDF
            </a>
            <a href="index.php?page=generar_excel_finanzas&tipo=finanzas&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>" 
               class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Excel
            </a>
        </div>
    </div>

    <!-- Estadísticas del Período -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2 text-white"
                style="border-left: 4px solid #4e73df !important;">
                <div class="card-body text-center">
                    <h5>Ingresos del Período</h5>
                    <h4 id="ingresosPeriodo">$<?= number_format($ingresosPeriodo, 2) ?></h4>
                    <small class="text-white">Ventas Pagadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2 text-white"
                style="border-left: 4px solid #e74a3b !important;">
                <div class="card-body text-center">
                    <h5>Egresos del Período</h5>
                    <h4 id="egresosPeriodo">$<?= number_format($egresosPeriodo, 2) ?></h4>
                    <small class="text-white">Compras Pagadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow text-white"
                style="border-left: 4px solid #1cc88a !important;">
                <div class="card-body text-center">
                    <h5>Utilidad Neta</h5>
                    <h4 id="utilidadPeriodo">$<?= number_format($utilidadPeriodo, 2) ?></h4>
                    <small class="text-white">Ingresos - Egresos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow text-white"
                style="border-left: 4px solid #f6c23e !important;">
                <div class="card-body text-center">
                    <h5>Margen de Utilidad</h5>
                    <h4 id="margenUtilidad"><?= $ingresosPeriodo > 0 ? number_format(($utilidadPeriodo/$ingresosPeriodo)*100, 1) : 0 ?>%</h4>
                    <small class="text-white">Rentabilidad</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card py-3 mb-4">
        <div class="card-header text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3" id="formFiltros">
                <input type="hidden" name="page" value="reporte_finanzas">
                <div class="col-md-3">
                    <label for="fecha_inicio" class="form-label text-white">Fecha Inicio</label>
                    <input type="date" class="form-control text-black border-0" id="fecha_inicio" name="fecha_inicio" 
                           value="<?= $fecha_inicio ?>">
                </div>
                <div class="col-md-3">
                    <label for="fecha_fin" class="form-label text-white">Fecha Fin</label>
                    <input type="date" class="form-control text-black border-0" id="fecha_fin" name="fecha_fin" 
                           value="<?= $fecha_fin ?>">
                </div>
                <div class="col-md-2">
                    <label for="filtroTipo" class="form-label text-white">Tipo</label>
                    <select id="filtroTipo" class="form-select text-black border-0">
                        <option value="">Todos</option>
                        <option value="venta">Ventas</option>
                        <option value="compra">Compras</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filtroEstado" class="form-label text-white">Estado</label>
                    <select id="filtroEstado" class="form-select text-black border-0">
                        <option value="">Todos</option>
                        <option value="Pagada">Pagada</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Anulada">Anulada</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filtroMetodoPago" class="form-label text-white">Método Pago</label>
                    <select id="filtroMetodoPago" class="form-select text-black border-0">
                        <option value="">Todos</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Crédito">Crédito</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-white d-block">&nbsp;</label>
                    <button type="button" id="btnLimpiarFiltros" class="btn btn-danger w-100">
                        <i class="fas fa-undo me-1"></i>Limpiar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Gráficos y Tablas -->
    <div class="row mb-4">
        <!-- Gráfico de Ingresos vs Egresos -->
        <div class="col-lg-8">
            <div class="card shadow-sm py-3">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">Evolución de Ingresos vs Egresos (Últimos 12 Meses)</h5>
                </div>
                <div class="card-body">
                    <canvas id="ingresosEgresosChart" style="width: 100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Métodos de Pago -->
        <div class="col-lg-4">
            <div class="card shadow-sm py-3">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">Distribución por Métodos de Pago</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-dark text-white">
                                <tr>
                                    <th>Método</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($metodosPago as $metodo): ?>
                                <tr>
                                    <td><?= $metodo['metodo_pago'] ?></td>
                                    <td class="text-center"><?= $metodo['cantidad'] ?></td>
                                    <td class="text-success">$<?= number_format($metodo['total'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($metodosPago)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No hay datos de métodos de pago.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tablas de Movimientos -->
    <div class="row">
        <!-- Ventas del Período -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm py-3">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">Ventas del Período</h5>
                    <small class="text-white-50">Total: <?= count($ventasPeriodo) ?> ventas</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="tablaVentas">
                            <thead class="table-dark text-white">
                                <tr>
                                    <th>Código</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Método</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyVentas">
                                <?php foreach ($ventasPeriodo as $venta): ?>
                                <tr class="fila-movimiento" 
                                    data-tipo="venta"
                                    data-fecha="<?= date('Y-m-d', strtotime($venta['fecha_venta'])) ?>"
                                    data-total="<?= $venta['total_venta'] ?>"
                                    data-estado="<?= $venta['estado'] ?>"
                                    data-metodo-pago="<?= $venta['metodo_pago'] ?>">
                                    <td><?= $venta['codigo_venta'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($venta['fecha_venta'])) ?></td>
                                    <td><?= $venta['nombre_cliente'] ?: 'Cliente General' ?></td>
                                    <td class="text-success">$<?= number_format($venta['total_venta'], 2) ?></td>
                                    <td><?= $venta['metodo_pago'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $venta['estado'] == 'Pagada' ? 'success' : ($venta['estado'] == 'Pendiente' ? 'warning' : 'danger') ?>">
                                            <?= $venta['estado'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($ventasPeriodo)): ?>
                                <tr class="no-resultados">
                                    <td colspan="6" class="text-center text-muted">No hay ventas en este período.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compras del Período -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm py-3">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">Compras del Período</h5>
                    <small class="text-white-50">Total: <?= count($comprasPeriodo) ?> compras</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="tablaCompras">
                            <thead class="table-dark text-white">
                                <tr>
                                    <th>Código</th>
                                    <th>Fecha</th>
                                    <th>Proveedor</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyCompras">
                                <?php foreach ($comprasPeriodo as $compra): ?>
                                <tr class="fila-movimiento" 
                                    data-tipo="compra"
                                    data-fecha="<?= date('Y-m-d', strtotime($compra['fecha_compra'])) ?>"
                                    data-total="<?= $compra['total_compra'] ?>"
                                    data-estado="<?= $compra['estado'] ?>">
                                    <td><?= $compra['codigo_compra'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($compra['fecha_compra'])) ?></td>
                                    <td><?= $compra['nombre_proveedor'] ?: 'Proveedor General' ?></td>
                                    <td class="text-danger">$<?= number_format($compra['total_compra'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $compra['estado'] == 'Pagada' ? 'success' : ($compra['estado'] == 'Pendiente' ? 'warning' : 'danger') ?>">
                                            <?= $compra['estado'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($comprasPeriodo)): ?>
                                <tr class="no-resultados">
                                    <td colspan="5" class="text-center text-muted">No hay compras en este período.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen Estadístico -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm py-3"
                 style="border-left: 4px solid #e6ff8eff !important;">
                <div class="card-header text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Resumen Estadístico del Período
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="text-white">
                                <h4><?= $ventasPagadasPeriodo ?></h4>
                                <small>Ventas Pagadas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-white">
                                <h4><?= $ventasPendientesPeriodo ?></h4>
                                <small>Ventas Pendientes</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-white">
                                <h4><?= $ventasAnuladasPeriodo ?></h4>
                                <small>Ventas Anuladas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-white">
                                <h4><?= count($comprasPeriodo) ?></h4>
                                <small>Total Compras</small>
                            </div>
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
            labels: labels.reverse(),
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

    // Filtros
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const filtroTipo = document.getElementById('filtroTipo');
    const filtroEstado = document.getElementById('filtroEstado');
    const filtroMetodoPago = document.getElementById('filtroMetodoPago');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    const filasMovimientos = document.querySelectorAll('.fila-movimiento');

    // Función para filtrar movimientos
    function filtrarMovimientos() {
        const fechaInicioVal = fechaInicio.value;
        const fechaFinVal = fechaFin.value;
        const tipoVal = filtroTipo.value;
        const estadoVal = filtroEstado.value;
        const metodoPagoVal = filtroMetodoPago.value;

        filasMovimientos.forEach(fila => {
            const fechaMovimiento = fila.getAttribute('data-fecha');
            const tipo = fila.getAttribute('data-tipo');
            const estado = fila.getAttribute('data-estado');
            const metodoPago = fila.getAttribute('data-metodo-pago');

            const coincideFecha = (!fechaInicioVal || fechaMovimiento >= fechaInicioVal) && 
                                 (!fechaFinVal || fechaMovimiento <= fechaFinVal);
            const coincideTipo = !tipoVal || tipo === tipoVal;
            const coincideEstado = !estadoVal || estado === estadoVal;
            const coincideMetodoPago = !metodoPagoVal || metodoPago === metodoPagoVal;

            if (coincideFecha && coincideTipo && coincideEstado && coincideMetodoPago) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    }

    // Eventos para filtros
    fechaInicio.addEventListener('change', filtrarMovimientos);
    fechaFin.addEventListener('change', filtrarMovimientos);
    filtroTipo.addEventListener('change', filtrarMovimientos);
    filtroEstado.addEventListener('change', filtrarMovimientos);
    filtroMetodoPago.addEventListener('change', filtrarMovimientos);

    // Limpiar filtros
    btnLimpiarFiltros.addEventListener('click', function() {
        fechaInicio.value = '<?= date('Y-m-01') ?>';
        fechaFin.value = '<?= date('Y-m-d') ?>';
        filtroTipo.value = '';
        filtroEstado.value = '';
        filtroMetodoPago.value = '';
        filtrarMovimientos();
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