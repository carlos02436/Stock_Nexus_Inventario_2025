<?php
// app/views/reportes/reporte_ventas.php
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

$reporteController = new ReporteController($db);
$ventas = $reporteController->generarReporteVentas($fecha_inicio, $fecha_fin);
$estadisticas = $reporteController->getEstadisticasVentas(30);
$productosMasVendidos = $reporteController->getProductosMasVendidos(10);
?>

<div class="container-fluid px-4 mb-5 text-white" style="margin-top:180px;">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom border-light">
        <h1 class="h2"><i class="fas fa-shopping-cart me-2"></i>Reporte de Ventas</h1>
        <div class="btn-toolbar mb-2 mb-md-2">
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
    <div class="card border-light mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="page" value="reporte_ventas">
                <div class="col-md-4">
                    <label for="fecha_inicio" class="form-label text-white">Fecha Inicio</label>
                    <input type="date" class="form-control text-white border-0" id="fecha_inicio" name="fecha_inicio" 
                           value="<?= $fecha_inicio ?>">
                </div>
                <div class="col-md-4">
                    <label for="fecha_fin" class="form-label text-white">Fecha Fin</label>
                    <input type="date" class="form-control text-white border-0" id="fecha_fin" name="fecha_fin" 
                           value="<?= $fecha_fin ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-white">&nbsp;</label>
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

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white shadow-sm">
                <div class="card-body text-center">
                    <h5>Total Ventas</h5>
                    <h4><?= count($ventas) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white shadow-sm">
                <div class="card-body text-center">
                    <h5>Ingresos Totales</h5>
                    <h4>$<?= number_format(array_sum(array_column($ventas, 'total_venta')), 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white shadow-sm">
                <div class="card-body text-center">
                    <h5>Ventas Pagadas</h5>
                    <h4><?= count(array_filter($ventas, fn($v) => $v['estado'] == 'Pagada')) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white shadow-sm">
                <div class="card-body text-center">
                    <h5>Ventas Pendientes</h5>
                    <h4><?= count(array_filter($ventas, fn($v) => $v['estado'] == 'Pendiente')) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Tablas -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card bg-dark shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">Detalle de Ventas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
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
                                <?php if (empty($ventas)): ?>
                                <tr>
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
            <div class="card shadow-sm">
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