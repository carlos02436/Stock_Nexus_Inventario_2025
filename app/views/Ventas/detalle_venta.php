<?php
// app/views/ventas/detalle_venta.php
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php?page=ventas');
    exit;
}

$ventaController = new VentaController($db);
$venta = $ventaController->obtener($id);
$detalles = $ventaController->obtenerDetalle($id);

if (!$venta) {
    header('Location: index.php?page=ventas');
    exit;
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:120px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-eye me-2"></i>Detalle de Venta</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=ventas" class="btn btn-neon">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">Información de la Venta</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Código:</th>
                            <td><?= $venta['codigo_venta'] ?></td>
                        </tr>
                        <tr>
                            <th>Cliente:</th>
                            <td><?= $venta['nombre_cliente'] ?: 'Cliente General' ?></td>
                        </tr>
                        <tr>
                            <th>Fecha:</th>
                            <td><?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></td>
                        </tr>
                        <tr>
                            <th>Método Pago:</th>
                            <td>
                                <span class="badge bg-info"><?= $venta['metodo_pago'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                <span class="badge bg-<?= 
                                    $venta['estado'] == 'Pagada' ? 'success' : 
                                    ($venta['estado'] == 'Pendiente' ? 'warning' : 'danger') 
                                ?>">
                                    <?= $venta['estado'] ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Vendedor:</th>
                            <td><?= $venta['usuario_nombre'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">Resumen de la Venta</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-white">
                            <strong>Total Venta:</strong>
                        </div>
                        <div class="col-6 text-end">
                            <h2 class="text-white"><strong>$<?= number_format($venta['total_venta'], 2, ',', '.') ?></strong></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Productos Vendidos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Producto</th>
                            <th>Código</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $detalle): ?>
                        <tr>
                            <td><?= $detalle['nombre_producto'] ?></td>
                            <td><?= $detalle['codigo_producto'] ?></td>
                            <td><?= $detalle['cantidad'] ?></td>
                            <td>$<?= number_format($detalle['precio_unitario'], 2, ',', '.') ?></td>
                            <td>$<?= number_format($detalle['subtotal'], 2, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-primary">
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td><strong>$<?= number_format($venta['total_venta'], 2, ',', '.') ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>