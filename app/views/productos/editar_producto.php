<?php
// app/views/productos/editar_producto.php
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php?page=productos');
    exit;
}

$productoController = new ProductoController($db);
$categoriaController = new CategoriaController($db);

$producto = $productoController->obtener($id);
$categorias = $categoriaController->listar();

if (!$producto) {
    header('Location: index.php?page=productos');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productoController->actualizar($id, $_POST);
    $_SESSION['success'] = "Producto actualizado correctamente";
    header('Location: index.php?page=productos');
    exit;
}
?>

<div class="container-fluid px-4 pb-5">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
         style="margin-top:180px;">
        <h1 class="h2"><i class="fas fa-edit me-2"></i>Editar Producto</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=productos" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="codigo_producto" class="form-label text-white">Código del Producto *</label>
                                    <input type="text" class="form-control" id="codigo_producto" name="codigo_producto" 
                                           value="<?= htmlspecialchars($producto['codigo_producto']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre_producto" class="form-label text-white">Nombre del Producto *</label>
                                    <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" 
                                           value="<?= htmlspecialchars($producto['nombre_producto']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_categoria" class="form-label text-white">Categoría</label>
                                    <select class="form-select" id="id_categoria" name="id_categoria">
                                        <option value="">Seleccionar categoría</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria['id_categoria'] ?>" 
                                                <?= $producto['id_categoria'] == $categoria['id_categoria'] ? 'selected' : '' ?>>
                                                <?= $categoria['nombre_categoria'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unidad_medida" class="form-label text-white">Unidad de Medida</label>
                                    <input type="text" class="form-control" id="unidad_medida" name="unidad_medida" 
                                           value="<?= htmlspecialchars($producto['unidad_medida']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label text-white">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock_actual" class="form-label text-white">Stock Actual</label>
                                    <input type="number" class="form-control" id="stock_actual" name="stock_actual" 
                                           value="<?= $producto['stock_actual'] ?>" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock_minimo" class="form-label text-white">Stock Mínimo</label>
                                    <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" 
                                           value="<?= $producto['stock_minimo'] ?>" step="0.01">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="precio_compra" class="form-label text-white">Precio de Compra *</label>
                                    <input type="number" class="form-control" id="precio_compra" name="precio_compra" 
                                           value="<?= $producto['precio_compra'] ?>" required step="0.01" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="precio_venta" class="form-label text-white">Precio de Venta *</label>
                                    <input type="number" class="form-control" id="precio_venta" name="precio_venta" 
                                           value="<?= $producto['precio_venta'] ?>" required step="0.01" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="estado" class="form-label text-white">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="Activo" <?= $producto['estado'] == 'Activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="Inactivo" <?= $producto['estado'] == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-neon">
                                <i class="fas fa-save me-2"></i>Actualizar Producto
                            </button>
                            <a href="index.php?page=productos" class="btn btn-danger">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>