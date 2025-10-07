<?php
// app/views/productos/crear_producto.php
$categoriaController = new CategoriaController($db);
$categorias = $categoriaController->listar();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
     style="margin-top:120px;">
    <h1 class="h2"><i class="fas fa-plus me-2"></i>Crear Nuevo Producto</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=productos" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?page=crear_producto">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="codigo_producto" class="form-label">Código del Producto *</label>
                        <input type="text" class="form-control" id="codigo_producto" name="codigo_producto" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nombre_producto" class="form-label">Nombre del Producto *</label>
                        <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="id_categoria" class="form-label">Categoría</label>
                        <select class="form-select" id="id_categoria" name="id_categoria">
                            <option value="">Seleccionar categoría</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id_categoria'] ?>"><?= $categoria['nombre_categoria'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                        <input type="text" class="form-control" id="unidad_medida" name="unidad_medida" value="Unidad">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="stock_actual" class="form-label">Stock Actual</label>
                        <input type="number" class="form-control" id="stock_actual" name="stock_actual" value="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                        <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" value="5" step="0.01">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="precio_compra" class="form-label">Precio de Compra *</label>
                        <input type="number" class="form-control" id="precio_compra" name="precio_compra" required step="0.01" min="0">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="precio_venta" class="form-label">Precio de Venta *</label>
                        <input type="number" class="form-control" id="precio_venta" name="precio_venta" required step="0.01" min="0">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="Activo" selected>Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </select>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Guardar Producto
                </button>
                <a href="index.php?page=productos" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>