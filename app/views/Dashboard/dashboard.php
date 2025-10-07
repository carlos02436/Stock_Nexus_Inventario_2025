<?php
// app/views/dashboard/dashboard.php
$dashboardModel = new Dashboard($db);
$estadisticas = $dashboardModel->obtenerEstadisticasGenerales();
$ventasRecientes = $dashboardModel->obtenerVentasRecientes(5);
$alertasStock = $dashboardModel->obtenerAlertasStock();
$productosMasVendidos = $dashboardModel->obtenerProductosMasVendidos(5);
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Hoy</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Esta semana</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Este mes</button>
        </div>
    </div>
</div>

<!-- Estadísticas principales -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Productos Activos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-white"><?= $estadisticas['total_productos'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Ventas del Mes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-white">$<?= number_format($estadisticas['ingresos_ventas_mes'] ?? 0, 2) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Clientes Registrados
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-white"><?= $estadisticas['total_clientes'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Valor Inventario
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-white">$<?= number_format($estadisticas['valor_inventario'] ?? 0, 2) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-warehouse fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Ventas Recientes -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-shopping-cart me-2"></i>Ventas Recientes
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ventasRecientes as $venta): ?>
                            <tr>
                                <td><?= $venta['codigo_venta'] ?></td>
                                <td><?= $venta['nombre_cliente'] ?: 'Cliente General' ?></td>
                                <td>$<?= number_format($venta['total_venta'], 2) ?></td>
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
                <a href="index.php?page=ventas" class="btn btn-sm btn-outline-primary">Ver todas las ventas</a>
            </div>
        </div>
    </div>

    <!-- Alertas de Stock -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Alertas de Stock Bajo
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($alertasStock)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Stock Actual</th>
                                    <th>Stock Mínimo</th>
                                    <th>Diferencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alertasStock as $producto): ?>
                                <tr>
                                    <td><?= $producto['nombre_producto'] ?></td>
                                    <td><?= $producto['stock_actual'] ?></td>
                                    <td><?= $producto['stock_minimo'] ?></td>
                                    <td>
                                        <span class="badge bg-danger"><?= $producto['diferencia'] ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="index.php?page=productos" class="btn btn-sm btn-outline-danger">Gestionar inventario</a>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p>No hay alertas de stock bajo</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Productos Más Vendidos -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-chart-line me-2"></i>Productos Más Vendidos
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Código</th>
                                <th>Unidades Vendidas</th>
                                <th>Ingresos Generados</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productosMasVendidos as $producto): ?>
                            <tr>
                                <td><?= $producto['nombre_producto'] ?></td>
                                <td><?= $producto['codigo_producto'] ?></td>
                                <td><?= $producto['total_vendido'] ?></td>
                                <td>$<?= number_format($producto['ingresos_generados'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>