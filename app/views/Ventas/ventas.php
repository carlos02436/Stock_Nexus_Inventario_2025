<?php
// app/views/ventas/ventas.php
$ventaController = new VentaController($db);
$ventas = $ventaController->listar();
?>
<div class="container-fluid px-4" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-cash-register me-2"></i>Gestión de Ventas</h1>
        <div class="btn-toolbar mb-2 mb-md-2">
            <a href="index.php?page=crear_venta" class="btn btn-neon">
                <i class="fas fa-plus me-2"></i>Nueva Venta
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Historial de Ventas
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Método Pago</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventas as $venta): ?>
                        <tr>
                            <td><?= $venta['codigo_venta'] ?></td>
                            <td><?= $venta['nombre_cliente'] ?: 'Cliente General' ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></td>
                            <td>$<?= number_format($venta['total_venta'], 2, ',', '.') ?></td>
                            <td>
                                <span class="badge bg-info"><?= $venta['metodo_pago'] ?></span>
                            </td>
                            <td>
                                <span class="badge bg-<?= 
                                    $venta['estado'] == 'Pagada' ? 'success' : 
                                    ($venta['estado'] == 'Pendiente' ? 'warning' : 'danger') 
                                ?>">
                                    <?= $venta['estado'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=detalle_venta&id=<?= $venta['id_venta'] ?>" 
                                       class="btn btn-info" title="Ver Detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($venta['estado'] == 'Pendiente'): ?>
                                    <a href="index.php?page=marcar_venta_pagada&id=<?= $venta['id_venta'] ?>" 
                                       class="btn btn-success" title="Marcar como Pagada">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>