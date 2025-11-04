<?php
// app/views/compras/detalle_compra.php
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php?page=compras');
    exit;
}

$compraController = new CompraController($db);
$compra = $compraController->obtener($id);
$detalles = $compraController->obtenerDetalle($id);

if (!$compra) {
    header('Location: index.php?page=compras');
    exit;
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2 text-white"><i class="fas fa-eye me-2"></i>Detalle de Compra</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=compras" class="boton3 text-decoration-none">
                <div class="boton-top3"><i class="fas fa-arrow-left me-2"></i>Volver a Compras</div>
                <div class="boton-bottom3"></div>
                <div class="boton-base3"></div>
            </a>
        </div>
    </div>

<div class="row mx-1">
    <div class="col-md-12">
        <div class="card mb-4 py-3">
            <div class="card-header text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Información de la Compra</h5>
                <div class="text-end">
                    <div class="h4 text-warning mb-0">$<?= number_format($compra['total_compra'], 2) ?></div>
                    <small class="text-white-50">Total Compra</small>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Código:</th>
                                <td><?= $compra['codigo_compra'] ?></td>
                            </tr>
                            <tr>
                                <th>Factura:</th>
                                <td>
                                    <span class="badge bg-secondary"><?= $compra['factura'] ?? 'N/A' ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Proveedor:</th>
                                <td><?= $compra['nombre_proveedor'] ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Fecha:</th>
                                <td><?= date('d/m/Y H:i', strtotime($compra['fecha_compra'])) ?></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    <span class="badge bg-<?= 
                                        $compra['estado'] == 'Pagada' ? 'success' : 
                                        ($compra['estado'] == 'Pendiente' ? 'warning' : 'danger') 
                                    ?>">
                                        <?= $compra['estado'] ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Registrado por:</th>
                                <td><?= $compra['usuario_nombre'] ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="card mx-1 py-3">
        <div class="card-header text-white">
            <h5 class="card-title mb-0">Productos Comprados</h5>
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
                            <td>$<?= number_format($detalle['precio_unitario'], 2) ?></td>
                            <td>$<?= number_format($detalle['subtotal'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-primary">
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td><strong>$<?= number_format($compra['total_compra'], 2) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>