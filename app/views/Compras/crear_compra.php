<?php
// app/views/compras/crear_compra.php
$proveedorController = new ProveedorController($db);
$productoController = new ProductoController($db);

$proveedores = $proveedorController->listar();
$productos = $productoController->listar();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
     style="margin-top:180px;">
    <h1 class="h2"><i class="fas fa-plus me-2"></i>Registrar Nueva Compra</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=compras" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?page=crear_compra" id="formCompra">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="codigo_compra" class="form-label">Código de Compra *</label>
                        <input type="text" class="form-control" id="codigo_compra" name="codigo_compra" 
                               value="C<?= date('YmdHis') ?>" required readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="id_proveedor" class="form-label">Proveedor *</label>
                        <select class="form-select" id="id_proveedor" name="id_proveedor" required>
                            <option value="">Seleccionar proveedor</option>
                            <?php foreach ($proveedores as $proveedor): ?>
                                <option value="<?= $proveedor['id_proveedor'] ?>"><?= $proveedor['nombre_proveedor'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Productos -->
            <div class="mb-4">
                <h5 class="mb-3"><i class="fas fa-boxes me-2"></i>Productos de la Compra</h5>
                <div id="productos-container">
                    <div class="producto-item row mb-3">
                        <div class="col-md-4">
                            <select class="form-select producto-select" name="productos[0][id_producto]" required>
                                <option value="">Seleccionar producto</option>
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?= $producto['id_producto'] ?>" 
                                            data-precio="<?= $producto['precio_compra'] ?>">
                                        <?= $producto['nombre_producto'] ?> - $<?= $producto['precio_compra'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control cantidad" name="productos[0][cantidad]" 
                                   placeholder="Cantidad" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control precio" name="productos[0][precio_unitario]" 
                                   placeholder="Precio unitario" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control subtotal" readonly placeholder="Subtotal">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-producto">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" id="agregar-producto">
                    <i class="fas fa-plus me-1"></i>Agregar Producto
                </button>
            </div>

            <!-- Totales -->
            <div class="row mb-4">
                <div class="col-md-6 offset-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-6"><strong>Subtotal:</strong></div>
                                <div class="col-6 text-end">$<span id="subtotal-total">0.00</span></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6"><strong>Total Compra:</strong></div>
                                <div class="col-6 text-end h5 text-primary">$<span id="total-compra">0.00</span></div>
                            </div>
                            <input type="hidden" name="total_compra" id="input-total-compra">
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="id_usuario" value="<?= $_SESSION['usuario_id'] ?>">

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Registrar Compra
                </button>
                <a href="index.php?page=compras" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let productoCount = 1;
    
    // Agregar producto
    document.getElementById('agregar-producto').addEventListener('click', function() {
        const container = document.getElementById('productos-container');
        const newProducto = container.firstElementChild.cloneNode(true);
        
        // Actualizar nombres de los inputs
        const inputs = newProducto.querySelectorAll('input, select');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace('[0]', `[${productoCount}]`));
            }
        });
        
        // Limpiar valores
        newProducto.querySelector('.producto-select').value = '';
        newProducto.querySelector('.cantidad').value = '';
        newProducto.querySelector('.precio').value = '';
        newProducto.querySelector('.subtotal').value = '';
        
        container.appendChild(newProducto);
        productoCount++;
    });
    
    // Remover producto
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-producto')) {
            if (document.querySelectorAll('.producto-item').length > 1) {
                e.target.closest('.producto-item').remove();
                calcularTotales();
            }
        }
    });
    
    // Calcular subtotal cuando cambia cantidad o precio
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('cantidad') || e.target.classList.contains('precio')) {
            const item = e.target.closest('.producto-item');
            const cantidad = parseFloat(item.querySelector('.cantidad').value) || 0;
            const precio = parseFloat(item.querySelector('.precio').value) || 0;
            const subtotal = cantidad * precio;
            
            item.querySelector('.subtotal').value = subtotal.toFixed(2);
            calcularTotales();
        }
    });
    
    // Auto-completar precio cuando se selecciona producto
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('producto-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const precio = selectedOption.getAttribute('data-precio');
            if (precio) {
                const item = e.target.closest('.producto-item');
                item.querySelector('.precio').value = precio;
                
                // Trigger input event para calcular subtotal
                const event = new Event('input');
                item.querySelector('.precio').dispatchEvent(event);
            }
        }
    });
    
    function calcularTotales() {
        let subtotalTotal = 0;
        
        document.querySelectorAll('.producto-item').forEach(item => {
            const subtotal = parseFloat(item.querySelector('.subtotal').value) || 0;
            subtotalTotal += subtotal;
        });
        
        document.getElementById('subtotal-total').textContent = subtotalTotal.toFixed(2);
        document.getElementById('total-compra').textContent = subtotalTotal.toFixed(2);
        document.getElementById('input-total-compra').value = subtotalTotal;
    }
});
</script>