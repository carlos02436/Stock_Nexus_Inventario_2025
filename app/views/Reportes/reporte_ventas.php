<?php
// app/views/reportes/reporte_ventas.php
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

$reporteController = new ReporteController($db);
$ventas = $reporteController->generarReporteVentas($fecha_inicio, $fecha_fin);
$estadisticas = $reporteController->getEstadisticasVentas(30);
$productosMasVendidos = $reporteController->getProductosMasVendidos(10);

// Calcular estadísticas iniciales - SOLO VENTAS ACTIVAS (no anuladas)
$ventasActivas = array_filter($ventas, fn($v) => $v['estado'] != 'Anulada');
$totalVentas = count($ventasActivas);
$ingresosTotales = array_sum(array_column($ventasActivas, 'total_venta'));
$ventasPagadas = count(array_filter($ventasActivas, fn($v) => $v['estado'] == 'Pagada'));
$ventasPendientes = count(array_filter($ventasActivas, fn($v) => $v['estado'] == 'Pendiente'));
?>
<div class="container-fluid px-4 mb-5 text-white" style="margin-top:180px;">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom border-light">
        <h1 class="h2"><i class="fas fa-shopping-cart me-2"></i>Reporte de Ventas</h1>
        <div class="btn-toolbar mb-2 mb-md-2">
            <a href="index.php?page=reportes" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver a Reportes
            </a>
            <a href="index.php?page=generar_pdf_reporte&tipo=ventas&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>" 
               class="btn btn-danger me-2">
                <i class="fas fa-file-pdf me-2"></i>PDF
            </a>
            <a href="index.php?page=generar_excel_reporte&tipo=ventas&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>" 
               class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Excel
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2 text-white"
                style="border-left: 4px solid #4e73df !important;">
                <div class="card-body text-center">
                    <h5>Total Ventas</h5>
                    <h4 id="totalVentas"><?= $totalVentas ?></h4>
                    <small class="text-white">(Excluyendo anuladas)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2 text-white"
                style="border-left: 4px solid #09ff53ff !important;">
                <div class="card-body text-center">
                    <h5>Ingresos Totales</h5>
                    <h4 id="ingresosTotales">$<?= number_format($ingresosTotales, 2) ?></h4>
                    <small class="text-white">(Excluyendo anuladas)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow text-white"
                style="border-left: 4px solid #ff0000ff !important;">
                <div class="card-body text-center">
                    <h5>Ventas Pagadas</h5>
                    <h4 id="ventasPagadas"><?= $ventasPagadas ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow text-white"
                style="border-left: 4px solid #c4ff01ff !important;">
                <div class="card-body text-center">
                    <h5>Ventas Pendientes</h5>
                    <h4 id="ventasPendientes"><?= $ventasPendientes ?></h4>
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
                <input type="hidden" name="page" value="reporte_ventas">
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
                    <label for="filtroCodigo" class="form-label text-white">Código</label>
                    <input type="text" id="filtroCodigo" class="form-control text-black border-0" 
                           placeholder="Código venta...">
                </div>
                <div class="col-md-2">
                    <label for="filtroCliente" class="form-label text-white">Cliente</label>
                    <input type="text" id="filtroCliente" class="form-control text-black border-0" 
                           placeholder="Nombre cliente...">
                </div>
                <div class="col-md-2">
                    <label for="filtroMetodoPago" class="form-label text-white">Método Pago</label>
                    <select id="filtroMetodoPago" class="form-select text-black border-0">
                        <option value="">Todos</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Crédito">Crédito</option>
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
                    <button type="button" id="btnLimpiarFiltros" class="btn btn-danger w-90">
                        <i class="fas fa-undo me-1"></i>Limpiar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tablas -->
    <div class="row mb-3">
        <div class="col-lg-8">
            <div class="card shadow-sm py-3">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">Detalle de Ventas</h5>
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
                                    <th>Método Pago</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyVentas">
                                <?php foreach ($ventas as $venta): ?>
                                <tr class="fila-venta" 
                                    data-fecha="<?= date('Y-m-d', strtotime($venta['fecha_venta'])) ?>" 
                                    data-total="<?= $venta['total_venta'] ?>" 
                                    data-estado="<?= $venta['estado'] ?>"
                                    data-es-activa="<?= $venta['estado'] != 'Anulada' ? '1' : '0' ?>">
                                    <td class="codigo-venta"><?= $venta['codigo_venta'] ?></td>
                                    <td class="fecha-venta"><?= date('d/m/Y', strtotime($venta['fecha_venta'])) ?></td>
                                    <td class="nombre-cliente"><?= $venta['nombre_cliente'] ?: 'Cliente General' ?></td>
                                    <td class="total-venta">$<?= number_format($venta['total_venta'], 2) ?></td>
                                    <td class="metodo-pago"><?= $venta['metodo_pago'] ?></td>
                                    <td class="estado-venta">
                                        <span class="badge bg-<?= $venta['estado'] == 'Pagada' ? 'success' : ($venta['estado'] == 'Pendiente' ? 'warning' : 'danger') ?>">
                                            <?= $venta['estado'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($ventas)): ?>
                                <tr class="no-resultados">
                                    <td colspan="6" class="text-center text-muted">No hay ventas registradas en este período.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Productos más vendidos -->
        <div class="col-lg-4">
            <div class="card shadow-sm py-3">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">Productos Más Vendidos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-dark text-white">
                                <tr>
                                    <th>Producto</th>
                                    <th>Vendido</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productosMasVendidos as $producto): ?>
                                <tr>
                                    <td><?= $producto['nombre_producto'] ?></td>
                                    <td><?= $producto['total_vendido'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($productosMasVendidos)): ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No hay productos registrados.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
    const filtroCodigo = document.getElementById('filtroCodigo');
    const filtroCliente = document.getElementById('filtroCliente');
    const filtroMetodoPago = document.getElementById('filtroMetodoPago');
    const filtroEstado = document.getElementById('filtroEstado');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    const filasVentas = document.querySelectorAll('.fila-venta');
    
    // Función para verificar si hay filtros activos
    function hayFiltrosActivos() {
        return filtroCodigo.value || filtroCliente.value || filtroMetodoPago.value || filtroEstado.value;
    }
    
    // Función para actualizar estadísticas - TODAS LAS VENTAS
    function actualizarEstadisticas(ventasFiltradas) {
        const totalVentas = ventasFiltradas.length;
        
        // Calcular ingresos totales de TODAS las ventas
        const ingresosTotales = ventasFiltradas.reduce((sum, fila) => {
            return sum + parseFloat(fila.getAttribute('data-total'));
        }, 0);
        
        // Contar ventas por estado de TODAS las ventas
        const ventasPagadas = ventasFiltradas.filter(fila => 
            fila.getAttribute('data-estado') === 'Pagada'
        ).length;
        
        const ventasPendientes = ventasFiltradas.filter(fila => 
            fila.getAttribute('data-estado') === 'Pendiente'
        ).length;
        
        const ventasAnuladas = ventasFiltradas.filter(fila => 
            fila.getAttribute('data-estado') === 'Anulada'
        ).length;
        
        // Actualizar las estadísticas en la interfaz
        document.getElementById('totalVentas').textContent = totalVentas;
        document.getElementById('ingresosTotales').textContent = '$' + ingresosTotales.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        document.getElementById('ventasPagadas').textContent = ventasPagadas;
        document.getElementById('ventasPendientes').textContent = ventasPendientes;
        document.getElementById('ventasAnuladas').textContent = ventasAnuladas;
    }
    
    // Función para filtrar las ventas
    function filtrarVentas() {
        const fechaInicioVal = fechaInicio.value;
        const fechaFinVal = fechaFin.value;
        const codigoVal = filtroCodigo.value.toLowerCase().trim();
        const clienteVal = filtroCliente.value.toLowerCase().trim();
        const metodoPagoVal = filtroMetodoPago.value;
        const estadoVal = filtroEstado.value;
        
        let ventasFiltradas = [];
        let hayCoincidencias = false;
        
        filasVentas.forEach(fila => {
            const fechaVenta = fila.getAttribute('data-fecha');
            const codigo = fila.querySelector('.codigo-venta').textContent.toLowerCase();
            const cliente = fila.querySelector('.nombre-cliente').textContent.toLowerCase();
            const metodoPago = fila.querySelector('.metodo-pago').textContent.trim();
            const estado = fila.getAttribute('data-estado');
            
            // Verificar filtro de fechas
            const coincideFecha = (!fechaInicioVal || fechaVenta >= fechaInicioVal) && 
                                 (!fechaFinVal || fechaVenta <= fechaFinVal);
            
            // Verificar filtros de campos
            const coincideCampos = 
                (!codigoVal || codigo.includes(codigoVal)) &&
                (!clienteVal || cliente.includes(clienteVal)) &&
                (!metodoPagoVal || metodoPago === metodoPagoVal) &&
                (!estadoVal || estado === estadoVal);
            
            // Mostrar u ocultar la fila
            if (coincideFecha && coincideCampos) {
                fila.style.display = '';
                ventasFiltradas.push(fila);
                hayCoincidencias = true;
            } else {
                fila.style.display = 'none';
            }
        });
        
        // Actualizar estadísticas con TODAS las ventas filtradas
        actualizarEstadisticas(ventasFiltradas);
        
        // Mostrar mensaje solo si se han aplicado filtros y no hay coincidencias
        const tbody = document.getElementById('tbodyVentas');
        const mensajeNoResultados = tbody.querySelector('.no-resultados');
        
        // Verificar si hay filtros activos (excluyendo fechas por defecto)
        const hayFiltros = hayFiltrosActivos();
        
        if (!hayCoincidencias && hayFiltros) {
            if (!mensajeNoResultados) {
                const tr = document.createElement('tr');
                tr.className = 'no-resultados';
                tr.innerHTML = '<td colspan="6" class="text-center text-muted">No se encontraron ventas con los filtros aplicados.</td>';
                tbody.appendChild(tr);
            }
        } else if (mensajeNoResultados && (hayCoincidencias || !hayFiltros)) {
            // Remover mensaje si hay coincidencias o no hay filtros activos
            mensajeNoResultados.remove();
        }
    }
    
    // Eventos para todos los filtros (incluyendo fechas)
    fechaInicio.addEventListener('change', filtrarVentas);
    fechaFin.addEventListener('change', filtrarVentas);
    filtroCodigo.addEventListener('input', filtrarVentas);
    filtroCliente.addEventListener('input', filtrarVentas);
    filtroMetodoPago.addEventListener('change', filtrarVentas);
    filtroEstado.addEventListener('change', filtrarVentas);
    
    // Evento para limpiar filtros
    btnLimpiarFiltros.addEventListener('click', function() {
        // Limpiar todos los filtros
        fechaInicio.value = '<?= date('Y-m-01') ?>';
        fechaFin.value = '<?= date('Y-m-d') ?>';
        filtroCodigo.value = '';
        filtroCliente.value = '';
        filtroMetodoPago.value = '';
        filtroEstado.value = '';
        filtrarVentas();
    });
    
    // NO filtrar automáticamente al cargar la página
    // Solo mostrar todas las ventas inicialmente
});
</script>