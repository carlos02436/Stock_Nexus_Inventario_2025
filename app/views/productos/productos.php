<?php
// app/views/productos/productos.php
$productoController = new ProductoController($db);
$productos = $productoController->listar();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-boxes me-2"></i>Gestión de Productos</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=crear_producto" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Nuevo Producto
        </a>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Lista de Productos
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="tablaProductos">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                        <th>Precio Compra</th>
                        <th>Precio Venta</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?= $producto['codigo_producto'] ?></td>
                        <td><?= $producto['nombre_producto'] ?></td>
                        <td><?= $producto['nombre_categoria'] ?: 'Sin categoría' ?></td>
                        <td>
                            <span class="badge bg-<?= $producto['stock_actual'] <= $producto['stock_minimo'] ? 'danger' : 'success' ?>">
                                <?= $producto['stock_actual'] ?>
                            </span>
                        </td>
                        <td>$<?= number_format($producto['precio_compra'], 2) ?></td>
                        <td>$<?= number_format($producto['precio_venta'], 2) ?></td>
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
        $('#tablaProductos').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            }
        });
    }
});
</script>