<?php
// app/views/reportes/reporte_ventas.php
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

$reporteController = new ReporteController($db);
$ventas = $reporteController->generarReporteVentas($fecha_inicio, $fecha_fin);
$estadisticas = $reporteController->getEstadisticasVentas(30);
$productosMasVendidos = $reporteController->getProductosMasVendidos(10);
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
     style="margin-top:120px;">
    <h1 class="h2"><i class="fas fa-shopping-cart me-2"></i>Reporte de Ventas</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=generar_pdf&tipo=ventas&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>" 
           class="btn btn-danger me-2">
            <i class="fas fa-file-pdf me-2"></i>PDF
        </a>
        <a href="index.php?page=generar_excel&tipo=ventas&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>" 
           class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i>Excel
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <input type="hidden" name="page" value="reporte_ventas">
            <div class="col-md-4">
                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                       value="<?= $fecha_inicio ?>">
            </div>
            <div class="col-md-4">
                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                       value="<?= $fecha_fin ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                    <a href="index.php?page=reporte_ventas" class="btn btn-secondary">Limpiar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h6>Total Ventas</h6>
                <h3><?= count($ventas) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h6>Ingresos Totales</h6>
                <h3>$<?= number_format(array_sum(array_column($ventas, 'total_venta')), 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h6>Ventas Pagadas</h6>
                <h3><?= count(array_filter($ventas, function($v) { return $v['estado'] == 'Pagada'; })) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h6>Ventas Pendientes</h6>
                <h3><?= count(array_filter($ventas, function($v) { return $v['estado'] == 'Pendiente'; })) ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Detalle de Ventas</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Método Pago</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ventas as $venta): ?>
                            <tr>
                                <td><?= $venta['codigo_venta'] ?></td>
                                <td><?= date('d/m/Y', strtotime($venta['fecha_venta'])) ?></td>
                                <td><?= $venta['nombre_cliente'] ?: 'Cliente General' ?></td>
                                <td>$<?= number_format($venta['total_venta'], 2) ?></td>
                                <td><?= $venta['metodo_pago'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $venta['estado'] == 'Pagada' ? 'success' : 'warning' ?>">
                                        <?= $venta['estado'] ?>
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
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Productos Más Vendidos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>