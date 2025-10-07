<?php
// app/views/inventario/inventario.php
$productoController = new ProductoController($db);
$productos = $productoController->listar();
$inventarioController = new InventarioController($db);
$valorTotal = $inventarioController->getValorTotalInventario();
$stockBajo = $inventarioController->getProductosConStockBajo();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
     style="margin-top:120px;">
    <h1 class="h2"><i class="fas fa-warehouse me-2"></i>Gestión de Inventario</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=crear_producto" class="btn btn-success me-2">
            <i class="fas fa-plus me-2"></i>Nuevo Producto
        </a>
        <a href="index.php?page=movimientos" class="btn btn-info">
            <i class="fas fa-exchange-alt me-2"></i>Ver Movimientos
        </a>
    </div>
</div>

<!-- Resumen del Inventario -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Productos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count($productos) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Stock Bajo
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count($stockBajo) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Valor Inventario
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            $<?= number_format($valorTotal, 2) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Productos Activos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count(array_filter($productos, function($p) { return $p['estado'] == 'Activo'; })) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alertas de Stock Bajo -->
<?php if (!empty($stockBajo)): ?>
<div class="alert alert-warning mb-4">
    <h5><i class="fas fa-exclamation-triangle me-2"></i>Alertas de Stock Bajo</h5>
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Stock Actual</th>
                    <th>Stock Mínimo</th>
                    <th>Diferencia</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stockBajo as $producto): ?>
                <tr>
                    <td><?= $producto['nombre_producto'] ?></td>
                    <td><?= $producto['stock_actual'] ?></td>
                    <td><?= $producto['stock_minimo'] ?></td>
                    <td>
                        <span class="badge bg-danger"><?= $producto['diferencia'] ?></span>
                    </td>
                    <td>
                        <a href="index.php?page=editar_producto&id=<?= $producto['id_producto'] ?>" 
                           class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Reabastecer
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Lista de Productos -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Lista de Productos en Inventario
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="tablaInventario">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                        <th>Precio Compra</th>
                        <th>Precio Venta</th>
                        <th>Valor en Inventario</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): 
                        $valorInventario = $producto['stock_actual'] * $producto['precio_compra'];
                    ?>
                    <tr>
                        <td><?= $producto['codigo_producto'] ?></td>
                        <td><?= $producto['nombre_producto'] ?></td>
                        <td><?= $producto['nombre_categoria'] ?: 'Sin categoría' ?></td>
                        <td>
                            <span class="badge bg-<?= $producto['stock_actual'] <= $producto['stock_minimo'] ? 'danger' : ($producto['stock_actual'] <= ($producto['stock_minimo'] * 2) ? 'warning' : 'success') ?>">
                                <?= $producto['stock_actual'] ?>
                            </span>
                        </td>
                        <td>$<?= number_format($producto['precio_compra'], 2) ?></td>
                        <td>$<?= number_format($producto['precio_venta'], 2) ?></td>
                        <td><strong>$<?= number_format($valorInventario, 2) ?></strong></td>
                        <td>
                            <span class="badge bg-<?= $producto['estado'] == 'Activo' ? 'success' : 'secondary' ?>">
                                <?= $producto['estado'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="index.php?page=editar_producto&id=<?= $producto['id_producto'] ?>" 
                                   class="btn btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="index.php?page=eliminar_producto&id=<?= $producto['id_producto'] ?>" 
                                   class="btn btn-danger btn-delete" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable si existe la librería
    if ($.fn.DataTable) {
        $('#tablaInventario').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            order: [[3, 'asc']] // Ordenar por stock (columna 3) ascendente
        });
    }
});
</script>