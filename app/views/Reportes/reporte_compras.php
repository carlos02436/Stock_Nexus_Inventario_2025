<?php
// app/views/reportes/reporte_compras.php
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

// Incluir el controlador
require_once __DIR__ . '/../../controllers/CompraController.php';

// Crear instancia del controlador
$compraController = new CompraController($db);
$compras = $compraController->listar();

// Filtrar compras por período seleccionado
$comprasPeriodo = array_filter($compras, function($compra) use ($fecha_inicio, $fecha_fin) {
    $fechaCompra = date('Y-m-d', strtotime($compra['fecha_compra']));
    return $fechaCompra >= $fecha_inicio && $fechaCompra <= $fecha_fin;
});

// Calcular estadísticas del período
$totalCompras = count($comprasPeriodo);
$montoTotal = array_sum(array_column($comprasPeriodo, 'total_compra'));
$comprasPagadas = count(array_filter($comprasPeriodo, fn($c) => $c['estado'] == 'Pagada'));
$comprasPendientes = count(array_filter($comprasPeriodo, fn($c) => $c['estado'] == 'Pendiente'));
$comprasAnuladas = count(array_filter($comprasPeriodo, fn($c) => $c['estado'] == 'Anulada'));

// Calcular promedio de compra
$promedioCompra = $totalCompras > 0 ? $montoTotal / $totalCompras : 0;

// Obtener compras por proveedor
$comprasPorProveedor = [];
foreach ($comprasPeriodo as $compra) {
    $proveedor = $compra['nombre_proveedor'];
    if (!isset($comprasPorProveedor[$proveedor])) {
        $comprasPorProveedor[$proveedor] = [
            'cantidad' => 0,
            'monto_total' => 0
        ];
    }
    $comprasPorProveedor[$proveedor]['cantidad']++;
    $comprasPorProveedor[$proveedor]['monto_total'] += $compra['total_compra'];
}

// Ordenar proveedores por monto total
arsort($comprasPorProveedor);
?>
<div class="container-fluid px-4 mb-5 text-white" style="margin-top:180px;">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom border-light">
        <h1 class="h2"><i class="fas fa-shopping-cart me-2"></i>Reporte de Compras</h1>
        <div class="btn-toolbar mb-2 mb-md-2">
            <a href="index.php?page=reportes" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver a Reportes
            </a>
            <a href="index.php?page=generar_pdf_compras&tipo=compras&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>" 
               class="btn btn-danger me-2">
                <i class="fas fa-file-pdf me-2"></i>PDF
            </a>
            <a href="index.php?page=generar_excel_compras&tipo=compras&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>" 
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
                    <h5>Total Compras</h5>
                    <h4 id="totalCompras"><?= $totalCompras ?></h4>
                    <small class="text-white">En el período</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2 text-white"
                style="border-left: 4px solid #e74a3b !important;">
                <div class="card-body text-center">
                    <h5>Monto Total</h5>
                    <h4 id="montoTotal">$<?= number_format($montoTotal, 2) ?></h4>
                    <small class="text-white">Inversión total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow text-white"
                style="border-left: 4px solid #1cc88a !important;">
                <div class="card-body text-center">
                    <h5>Compras Pagadas</h5>
                    <h4 id="comprasPagadas"><?= $comprasPagadas ?></h4>
                    <small class="text-white"><?= $totalCompras > 0 ? number_format(($comprasPagadas/$totalCompras)*100, 1) : 0 ?>% del total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow text-white"
                style="border-left: 4px solid #f6c23e !important;">
                <div class="card-body text-center">
                    <h5>Promedio por Compra</h5>
                    <h4 id="promedioCompra">$<?= number_format($promedioCompra, 2) ?></h4>
                    <small class="text-white">Valor promedio</small>
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
                <input type="hidden" name="page" value="reporte_compras">
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
                    <label for="filtroProveedor" class="form-label text-white">Proveedor</label>
                    <select id="filtroProveedor" class="form-select text-black border-0">
                        <option value="">Todos</option>
                        <?php
                        $proveedores = array_unique(array_column($comprasPeriodo, 'nombre_proveedor'));
                        foreach ($proveedores as $proveedor): 
                            if (!empty($proveedor)):
                        ?>
                        <option value="<?= htmlspecialchars($proveedor) ?>"><?= htmlspecialchars($proveedor) ?></option>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
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
                    <label class="form-label text-white d-block">&nbsp;</label>
                    <button type="button" id="btnLimpiarFiltros" class="btn btn-danger w-100">
                        <i class="fas fa-undo me-1"></i>Limpiar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tablas de Compras -->
    <div class="row mb-4">
        <!-- Compras del Período -->
        <div class="col-lg-8">
            <div class="card shadow-sm py-3">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">Detalle de Compras del Período</h5>
                    <small class="text-white-50">Total: <?= $totalCompras ?> compras - $<?= number_format($montoTotal, 2) ?></small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="tablaCompras">
                            <thead class="table-dark text-white">
                                <tr>
                                    <th>Código</th>
                                    <th>Proveedor</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyCompras">
                                <?php foreach ($comprasPeriodo as $compra): ?>
                                <tr class="fila-compra" 
                                    data-fecha="<?= date('Y-m-d', strtotime($compra['fecha_compra'])) ?>"
                                    data-proveedor="<?= htmlspecialchars($compra['nombre_proveedor']) ?>"
                                    data-total="<?= $compra['total_compra'] ?>"
                                    data-estado="<?= $compra['estado'] ?>">
                                    <td><?= $compra['codigo_compra'] ?></td>
                                    <td><?= $compra['nombre_proveedor'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($compra['fecha_compra'])) ?></td>
                                    <td class="text-danger fw-bold">$<?= number_format($compra['total_compra'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $compra['estado'] == 'Pagada' ? 'success' : 
                                            ($compra['estado'] == 'Pendiente' ? 'warning' : 'danger') 
                                        ?>">
                                            <?= $compra['estado'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($comprasPeriodo)): ?>
                                <tr class="no-resultados">
                                    <td colspan="5" class="text-center text-muted">No hay compras registradas en este período.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compras por Proveedor -->
        <div class="col-lg-4">
            <div class="card shadow-sm py-3">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">Compras por Proveedor</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-dark text-white">
                                <tr>
                                    <th>Proveedor</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comprasPorProveedor as $proveedor => $datos): ?>
                                <tr>
                                    <td><?= htmlspecialchars($proveedor) ?></td>
                                    <td class="text-center"><?= $datos['cantidad'] ?></td>
                                    <td class="text-danger">$<?= number_format($datos['monto_total'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($comprasPorProveedor)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No hay compras por proveedor.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Resumen por Estado -->
            <div class="card shadow-sm py-3 mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">Distribución por Estado</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-dark text-white">
                                <tr>
                                    <th>Estado</th>
                                    <th>Cantidad</th>
                                    <th>Porcentaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-success">Pagada</span></td>
                                    <td class="text-center"><?= $comprasPagadas ?></td>
                                    <td class="text-center"><?= $totalCompras > 0 ? number_format(($comprasPagadas/$totalCompras)*100, 1) : 0 ?>%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning">Pendiente</span></td>
                                    <td class="text-center"><?= $comprasPendientes ?></td>
                                    <td class="text-center"><?= $totalCompras > 0 ? number_format(($comprasPendientes/$totalCompras)*100, 1) : 0 ?>%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">Anulada</span></td>
                                    <td class="text-center"><?= $comprasAnuladas ?></td>
                                    <td class="text-center"><?= $totalCompras > 0 ? number_format(($comprasAnuladas/$totalCompras)*100, 1) : 0 ?>%</td>
                                </tr>
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
            <div class="card shadow-sm py-3" style="border-left: 4px solid #6fff00ff !important;">
                <div class="card-header text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Resumen Estadístico del Período
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="text-white">
                                <h4><?= $totalCompras ?></h4>
                                <small>Total Compras</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-white">
                                <h4>$<?= number_format($montoTotal, 2) ?></h4>
                                <small>Inversión Total</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-white">
                                <h4>$<?= number_format($promedioCompra, 2) ?></h4>
                                <small>Promedio por Compra</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-white">
                                <h4><?= count($comprasPorProveedor) ?></h4>
                                <small>Proveedores</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const filtroProveedor = document.getElementById('filtroProveedor');
    const filtroEstado = document.getElementById('filtroEstado');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    const filasCompras = document.querySelectorAll('.fila-compra');

    // Función para actualizar estadísticas
    function actualizarEstadisticas(comprasFiltradas) {
        const totalCompras = comprasFiltradas.length;
        const montoTotal = comprasFiltradas.reduce((sum, fila) => {
            return sum + parseFloat(fila.getAttribute('data-total'));
        }, 0);
        const comprasPagadas = comprasFiltradas.filter(fila => 
            fila.getAttribute('data-estado') === 'Pagada'
        ).length;
        const promedioCompra = totalCompras > 0 ? montoTotal / totalCompras : 0;

        // Actualizar las estadísticas en la interfaz
        document.getElementById('totalCompras').textContent = totalCompras;
        document.getElementById('montoTotal').textContent = '$' + montoTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        document.getElementById('comprasPagadas').textContent = comprasPagadas;
        document.getElementById('promedioCompra').textContent = '$' + promedioCompra.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    // Función para filtrar las compras
    function filtrarCompras() {
        const fechaInicioVal = fechaInicio.value;
        const fechaFinVal = fechaFin.value;
        const proveedorVal = filtroProveedor.value;
        const estadoVal = filtroEstado.value;

        let comprasFiltradas = [];
        let hayCoincidencias = false;

        filasCompras.forEach(fila => {
            const fechaCompra = fila.getAttribute('data-fecha');
            const proveedor = fila.getAttribute('data-proveedor');
            const estado = fila.getAttribute('data-estado');

            const coincideFecha = (!fechaInicioVal || fechaCompra >= fechaInicioVal) && 
                                 (!fechaFinVal || fechaCompra <= fechaFinVal);
            const coincideProveedor = !proveedorVal || proveedor === proveedorVal;
            const coincideEstado = !estadoVal || estado === estadoVal;

            if (coincideFecha && coincideProveedor && coincideEstado) {
                fila.style.display = '';
                comprasFiltradas.push(fila);
                hayCoincidencias = true;
            } else {
                fila.style.display = 'none';
            }
        });

        // Actualizar estadísticas con compras filtradas
        actualizarEstadisticas(comprasFiltradas);

        // Mostrar mensaje si no hay coincidencias
        const tbody = document.getElementById('tbodyCompras');
        const mensajeNoResultados = tbody.querySelector('.no-resultados');
        
        const hayFiltros = proveedorVal || estadoVal;

        if (!hayCoincidencias && hayFiltros) {
            if (!mensajeNoResultados) {
                const tr = document.createElement('tr');
                tr.className = 'no-resultados';
                tr.innerHTML = '<td colspan="5" class="text-center text-muted">No se encontraron compras con los filtros aplicados.</td>';
                tbody.appendChild(tr);
            }
        } else if (mensajeNoResultados && (hayCoincidencias || !hayFiltros)) {
            mensajeNoResultados.remove();
        }
    }

    // Eventos para filtros
    fechaInicio.addEventListener('change', filtrarCompras);
    fechaFin.addEventListener('change', filtrarCompras);
    filtroProveedor.addEventListener('change', filtrarCompras);
    filtroEstado.addEventListener('change', filtrarCompras);

    // Limpiar filtros
    btnLimpiarFiltros.addEventListener('click', function() {
        fechaInicio.value = '<?= date('Y-m-01') ?>';
        fechaFin.value = '<?= date('Y-m-d') ?>';
        filtroProveedor.value = '';
        filtroEstado.value = '';
        filtrarCompras();
    });

    // Inicializar estadísticas
    actualizarEstadisticas(Array.from(filasCompras));
});
</script>