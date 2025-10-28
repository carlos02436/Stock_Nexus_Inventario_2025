<?php
// app/views/productos/crear_producto.php
$categoriaController = new CategoriaController($db);
$categorias = $categoriaController->listar();

// Obtener el último código por categoría (necesitarás agregar este método a tu ProductoController)
$productoController = new ProductoController($db);
$ultimosCodigos = $productoController->obtenerUltimosCodigosPorCategoria();
?>

<div class="container-fluid px-4 pb-5">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
         style="margin-top:180px;">
        <h1 class="h2"><i class="fas fa-plus me-2"></i>Crear Nuevo Producto</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=productos" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Productos
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="index.php?page=crear_producto">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_categoria" class="form-label text-white">Categoría *</label>
                                    <select class="form-select" id="id_categoria" name="id_categoria" required onchange="generarCodigoProducto()">
                                        <option value="">Seleccionar categoría</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria['id_categoria'] ?>" data-prefijo="<?= substr($categoria['nombre_categoria'], 0, 3) ?>">
                                                <?= $categoria['nombre_categoria'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="codigo_producto" class="form-label text-white">Código del Producto *</label>
                                    <input type="text" class="form-control" id="codigo_producto" name="codigo_producto" required readonly>
                                    <small class="form-text text-muted">Código generado automáticamente</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nombre_producto" class="form-label text-white">Nombre del Producto *</label>
                                    <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unidad_medida" class="form-label text-white">Unidad de Medida</label>
                                    <input type="text" class="form-control" id="unidad_medida" name="unidad_medida" value="Unidad">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estado" class="form-label text-white">Estado</label>
                                    <select class="form-select" id="estado" name="estado">
                                        <option value="Activo" selected>Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label text-white">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock_actual" class="form-label text-white">Stock Actual</label>
                                    <input type="number" class="form-control" id="stock_actual" name="stock_actual" value="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock_minimo" class="form-label text-white">Stock Mínimo</label>
                                    <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" value="5" step="0.01">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="precio_compra" class="form-label text-white">Precio de Compra *</label>
                                    <input type="number" class="form-control" id="precio_compra" name="precio_compra" required step="0.01" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="precio_venta" class="form-label text-white">Precio de Venta *</label>
                                    <input type="number" class="form-control" id="precio_venta" name="precio_venta" required step="0.01" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success">
                            <small>
                                <i class="fas fa-info-circle me-2"></i>
                                Los campos marcados con * son obligatorios.
                            </small>
                        </div>

                        <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                            <button type="submit" class="btn btn-neon">
                                <i class="fas fa-save me-2"></i>Guardar Producto
                            </button>
                            <a href="index.php?page=productos" class="btn btn-danger">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Datos de últimos códigos por categoría desde PHP
const ultimosCodigos = <?= json_encode($ultimosCodigos) ?>;

function generarCodigoProducto() {
    const categoriaSelect = document.getElementById('id_categoria');
    const codigoInput = document.getElementById('codigo_producto');
    const categoriaId = categoriaSelect.value;
    
    if (categoriaId) {
        const selectedOption = categoriaSelect.options[categoriaSelect.selectedIndex];
        const prefijo = selectedOption.getAttribute('data-prefijo').toUpperCase();
        
        // Buscar el último código para esta categoría
        let ultimoNumero = 1;
        if (ultimosCodigos[categoriaId]) {
            const ultimoCodigo = ultimosCodigos[categoriaId];
            // Extraer el número del último código (ej: "ELE001" -> 1)
            const match = ultimoCodigo.match(/\d+/);
            if (match) {
                ultimoNumero = parseInt(match[0]) + 1;
            }
        }
        
        // Formatear el número con ceros a la izquierda (001, 002, etc.)
        const numeroFormateado = ultimoNumero.toString().padStart(3, '0');
        const nuevoCodigo = prefijo + numeroFormateado;
        
        codigoInput.value = nuevoCodigo;
    } else {
        codigoInput.value = '';
    }
}

// Generar código automáticamente al cargar la página si hay una categoría seleccionada
document.addEventListener('DOMContentLoaded', function() {
    const categoriaSelect = document.getElementById('id_categoria');
    categoriaSelect.addEventListener('change', generarCodigoProducto);
});
</script>