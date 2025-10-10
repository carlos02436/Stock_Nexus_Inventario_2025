<?php
// app/views/compras/compras.php
$compraController = new CompraController($db);
$compras = $compraController->listar();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
     style="margin-top:180px;">
    <h1 class="h2"><i class="fas fa-shopping-cart me-2"></i>Gestión de Compras</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=crear_compra" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Nueva Compra
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Historial de Compras
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($compras as $compra): ?>
                    <tr>
                        <td><?= $compra['codigo_compra'] ?></td>
                        <td><?= $compra['nombre_proveedor'] ?></td>
                        <td><?= date('d/m/Y', strtotime($compra['fecha_compra'])) ?></td>
                        <td>$<?= number_format($compra['total_compra'], 2) ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $compra['estado'] == 'Pagada' ? 'success' : 
                                ($compra['estado'] == 'Pendiente' ? 'warning' : 'danger') 
                            ?>">
                                <?= $compra['estado'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="index.php?page=detalle_compra&id=<?= $compra['id_compra'] ?>" 
                                   class="btn btn-info" title="Ver Detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($compra['estado'] == 'Pendiente'): ?>
                                <a href="index.php?page=marcar_compra_pagada&id=<?= $compra['id_compra'] ?>" 
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