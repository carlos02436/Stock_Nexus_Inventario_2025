<?php
// app/views/movimientos/movimientos.php
$inventarioController = new InventarioController($db);
$movimientos = $inventarioController->listarMovimientos();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
     style="margin-top:120px;">
    <h1 class="h2"><i class="fas fa-exchange-alt me-2"></i>Movimientos de Bodega</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=crear_movimiento" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Nuevo Movimiento
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Historial de Movimientos
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Descripción</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $movimiento): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($movimiento['fecha_movimiento'])) ?></td>
                        <td><?= $movimiento['nombre_producto'] ?> (<?= $movimiento['codigo_producto'] ?>)</td>
                        <td>
                            <span class="badge bg-<?= 
                                $movimiento['tipo_movimiento'] == 'Entrada' ? 'success' : 
                                ($movimiento['tipo_movimiento'] == 'Salida' ? 'danger' : 'warning')
                            ?>">
                                <?= $movimiento['tipo_movimiento'] ?>
                            </span>
                        </td>
                        <td><?= $movimiento['cantidad'] ?></td>
                        <td><?= $movimiento['descripcion'] ?></td>
                        <td><?= $movimiento['usuario_nombre'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>