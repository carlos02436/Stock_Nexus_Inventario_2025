<?php
// app/views/dashboard/dashboard.php

// Inicializar variables primero
$filtro = $_GET['filtro'] ?? 'mes';
$fechaInicio = '';
$fechaFin = '';

// Calcular fechas según el filtro seleccionado
switch($filtro) {
    case 'hoy':
        $fechaInicio = date('Y-m-d');
        $fechaFin = date('Y-m-d');
        break;
    case 'semana':
        $fechaInicio = date('Y-m-d', strtotime('monday this week'));
        $fechaFin = date('Y-m-d');
        break;
    case 'mes':
    default:
        $fechaInicio = date('Y-m-01');
        $fechaFin = date('Y-m-d');
        break;
}

// Inicializar el modelo y obtener datos
$dashboardModel = new Dashboard($db);
$estadisticas = $dashboardModel->obtenerEstadisticasGenerales($fechaInicio, $fechaFin);
$ventasRecientes = $dashboardModel->obtenerVentasRecientes(5, $fechaInicio, $fechaFin);
$alertasStock = $dashboardModel->obtenerAlertasStock();
$productosMasVendidos = $dashboardModel->obtenerProductosMasVendidos(5, $fechaInicio, $fechaFin);

// Si hay error en ventasRecientes, inicializar como array vacío
if (!isset($ventasRecientes) || $ventasRecientes === null) {
    $ventasRecientes = [];
}

// DEPURACIÓN
echo "<!-- ===== DEBUG ===== -->";
echo "<!-- Filtro: $filtro -->";
echo "<!-- Fecha Inicio: $fechaInicio -->";
echo "<!-- Fecha Fin: $fechaFin -->";
echo "<!-- Ventas Recientes count: " . count($ventasRecientes) . " -->";
echo "<!-- Ingresos: " . ($estadisticas['ingresos_ventas_mes'] ?? 0) . " -->";
echo "<!-- ================= -->";
?>

<div class="container-fluid px-4 pb-5">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
        style="margin-top:120px;">
        <h1 class="h2"><i class="fas fa-tachometer-alt me-2"></i>Dashboard 
            <small class="text-muted fs-6">
                <?= htmlspecialchars($filtro == 'hoy' ? '(Hoy)' : ($filtro == 'semana' ? '(Esta semana)' : '(Este mes)')) ?>
            </small>
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="index.php?page=dashboard&filtro=hoy" 
                   class="btn btn-sm <?= ($filtro == 'hoy') ? 'btn-primary' : 'btn-outline-secondary' ?>">Hoy</a>
                <a href="index.php?page=dashboard&filtro=semana" 
                   class="btn btn-sm <?= ($filtro == 'semana') ? 'btn-primary' : 'btn-outline-secondary' ?>">Esta semana</a>
                <a href="index.php?page=dashboard&filtro=mes" 
                   class="btn btn-sm <?= ($filtro == 'mes') ? 'btn-primary' : 'btn-outline-secondary' ?>">Este mes</a>
            </div>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Productos Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                <?= htmlspecialchars($estadisticas['total_productos'] ?? 0) ?>
                            </div>
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
                        <div class="col">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                <?php 
                                if ($filtro == 'hoy') {
                                    echo 'Ventas de Hoy';
                                } elseif ($filtro == 'semana') {
                                    echo 'Ventas de la Semana';
                                } else {
                                    echo 'Ventas del Mes';
                                }
                                ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                $<?= number_format($estadisticas['ingresos_ventas_mes'] ?? 0, 2) ?>
                            </div>
                            <small class="text-white-50">
                                <?= $estadisticas['cantidad_ventas'] ?? 0 ?> ventas
                                <?php if (($estadisticas['ventas_pagadas'] ?? 0) > 0): ?>
                                    <br><?= $estadisticas['cantidad_pagadas'] ?? 0 ?> pagadas
                                <?php endif; ?>
                            </small>
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
                        <div class="col">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Clientes Registrados</div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                <?= htmlspecialchars($estadisticas['total_clientes'] ?? 0) ?>
                            </div>
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
                        <div class="col">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Valor Inventario</div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                $<?= number_format($estadisticas['valor_inventario'] ?? 0, 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-warehouse fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventas Recientes y Alertas -->
    <div class="row">
        <!-- Ventas Recientes -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-white">
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
                                <?php if (!empty($ventasRecientes)): ?>
                                    <?php foreach ($ventasRecientes as $venta): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($venta['codigo_venta'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($venta['nombre_cliente'] ?? 'Cliente General') ?></td>
                                        <td>$<?= number_format($venta['total_venta'] ?? 0, 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?= ($venta['estado'] ?? '') == 'Pagada' ? 'success' : 'warning' ?>">
                                                <?= htmlspecialchars($venta['estado'] ?? '') ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">
                                            No hay ventas recientes
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="index.php?page=ventas" class="btn btn-sm btn-neon">Ver todas las ventas</a>
                </div>
            </div>
        </div>

        <!-- Alertas de Stock -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i><strong>Alertas de Stock Bajo</strong>
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
                                        <td><?= htmlspecialchars($producto['nombre_producto'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($producto['stock_actual'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars($producto['stock_minimo'] ?? 0) ?></td>
                                        <td><span class="badge bg-danger"><?= htmlspecialchars($producto['diferencia'] ?? 0) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="index.php?page=productos" class="btn btn-sm btn-outline-danger">Gestionar inventario</a>
                    <?php else: ?>
                        <div class="text-center text-white py-4">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <p>No hay alertas de stock bajo</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos Más Vendidos -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-white">
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
                                <?php if (!empty($productosMasVendidos)): ?>
                                    <?php foreach ($productosMasVendidos as $producto): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($producto['nombre_producto'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($producto['codigo_producto'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($producto['total_vendido'] ?? 0) ?></td>
                                        <td>$<?= number_format($producto['ingresos_generados'] ?? 0, 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">
                                            No hay datos de productos vendidos
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
</div>