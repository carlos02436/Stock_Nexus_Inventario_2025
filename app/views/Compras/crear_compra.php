<?php
// app/views/compras/crear_compra.php

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?page=login');
    exit();
}

// Instanciar controladores
$compraController = new CompraController($db);
$proveedorController = new ProveedorController($db);
$productoController = new ProductoController($db);

// Obtener datos necesarios
$proximoCodigo = $compraController->obtenerProximoCodigo();
$proveedores = $proveedorController->listar();
$productos = $productoController->listar();

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descuento = isset($_POST['aplicar_descuento']) ? floatval($_POST['descuento']) : 0;
    $subtotal = floatval($_POST['subtotal_compra']);
    $totalCompra = $subtotal - $descuento;
    
    $datosCompra = [
        'codigo_compra' => $_POST['codigo_compra'],
        'id_proveedor' => $_POST['id_proveedor'],
        'id_usuario' => $_POST['id_usuario'],
        'total_compra' => $totalCompra,
        'descuento' => $descuento,
        'estado' => 'Pagada',
        'productos' => $_POST['productos']
    ];
    
    $resultado = $compraController->crear($datosCompra);
    
    if ($resultado) {
        $_SESSION['mensaje'] = [
            'tipo' => 'success',
            'mensaje' => 'Compra registrada exitosamente con código: ' . $datosCompra['codigo_compra']
        ];
        header('Location: index.php?page=compras');
        exit();
    } else {
        $error = "Error al registrar la compra. Por favor, intente nuevamente.";
    }
}

// Función para formatear números en formato COP
function formatoCOP($numero) {
    return number_format($numero, 2, ',', '.');
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2"><i class="fas fa-plus me-2"></i>Registrar Nueva Compra</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=compras" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="index.php?page=crear_compra" id="formCompra">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="codigo_compra" class="form-label text-white">Código de Compra *</label>
                                    <input type="text" class="form-control text-black border-0" id="codigo_compra" name="codigo_compra" 
                                           value="<?= htmlspecialchars($proximoCodigo) ?>" required readonly>
                                    <div class="form-text text-light">Código generado automáticamente</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_proveedor" class="form-label text-white">Proveedor *</label>
                                    <select class="form-select text-black border-0" id="id_proveedor" name="id_proveedor" required>
                                        <option value="">Seleccionar proveedor</option>
                                        <?php foreach ($proveedores as $proveedor): ?>
                                            <option value="<?= $proveedor['id_proveedor'] ?>">
                                                <?= htmlspecialchars($proveedor['nombre_proveedor']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Productos -->
                        <div class="mb-4">
                            <h5 class="mb-3 text-white"><i class="fas fa-boxes me-2"></i>Productos de la Compra</h5>
                            <div id="productos-container">
                                <div class="producto-item row mb-3 g-2">
                                    <div class="col-md-5">
                                        <select class="form-select producto-select text-black border-0" name="productos[0][id_producto]" required>
                                            <option value="">Seleccionar producto</option>
                                            <?php foreach ($productos as $producto): ?>
                                                <option value="<?= $producto['id_producto'] ?>" 
                                                        data-precio="<?= $producto['precio_compra'] ?>">
                                                    <?= htmlspecialchars($producto['nombre_producto']) ?> - $<?= formatoCOP($producto['precio_compra']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control cantidad text-black border-0" name="productos[0][cantidad]" 
                                               placeholder="Cantidad" step="0.01" min="0.01" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control precio text-black border-0" name="productos[0][precio_unitario]" 
                                               placeholder="Precio unitario" step="0.01" min="0" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control subtotal text-black border-0" readonly placeholder="Subtotal">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-producto w-100">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm mt-2" id="agregar-producto">
                                <i class="fas fa-plus me-1"></i>Agregar Producto
                            </button>
                        </div>

                        <!-- Descuento -->
                        <div class="row mb-4">
                            <div class="col-md-8 offset-md-4">
                                <div class="card bg-dark border-light">
                                    <div class="card-body py-3">
                                        <div class="row mb-2">
                                            <div class="col-6 text-white"><strong>Subtotal:</strong></div>
                                            <div class="col-6 text-end text-white">$<span id="subtotal-total">0,00</span></div>
                                        </div>
                                        
                                        <!-- Checkbox y campo de descuento -->
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="aplicar_descuento" name="aplicar_descuento">
                                                    <label class="form-check-label text-white" for="aplicar_descuento">
                                                        <strong>Aplicar Descuento</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control text-black border-0" 
                                                           id="descuento" name="descuento" 
                                                           placeholder="0,00" step="0.01" min="0" 
                                                           value="0" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-2">
                                            <div class="col-6 text-white"><strong>Descuento Aplicado:</strong></div>
                                            <div class="col-6 text-end text-warning">-$<span id="descuento-aplicado">0,00</span></div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-6 text-white"><strong>Total Compra:</strong></div>
                                            <div class="col-6 text-end h5 text-primary">$<span id="total-compra">0,00</span></div>
                                        </div>
                                        
                                        <input type="hidden" name="total_compra" id="input-total-compra">
                                        <input type="hidden" name="subtotal_compra" id="input-subtotal-compra">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="id_usuario" value="<?= $_SESSION['usuario_id'] ?>">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="submit" class="btn btn-success px-4 py-2">
                                <i class="fas fa-save me-2"></i>Registrar Compra
                            </button>
                            <a href="index.php?page=compras" class="btn btn-secondary px-4 py-2">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let productoCount = 1;
    
    // Función para formatear números en formato COP
    function formatoCOP(numero) {
        return new Intl.NumberFormat('es-CO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(numero);
    }
    
    // Función para formatear input en tiempo real
    function formatearInputCOP(input) {
        input.addEventListener('blur', function() {
            const valor = parseFloat(this.value) || 0;
            this.value = formatoCOP(valor);
        });
        
        input.addEventListener('focus', function() {
            this.value = this.value.replace(/\./g, '').replace(',', '.');
        });
    }
    
    // Toggle descuento
    const checkboxDescuento = document.getElementById('aplicar_descuento');
    const inputDescuento = document.getElementById('descuento');
    
    checkboxDescuento.addEventListener('change', function() {
        inputDescuento.disabled = !this.checked;
        if (!this.checked) {
            inputDescuento.value = '0';
            calcularTotales();
        }
    });
    
    // Calcular cuando cambia el descuento
    inputDescuento.addEventListener('input', function() {
        calcularTotales();
    });
    
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
        
        // Aplicar formato COP a los nuevos inputs
        formatearInputCOP(newProducto.querySelector('.precio'));
        
        productoCount++;
    });
    
    // Remover producto
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-producto') || e.target.closest('.remove-producto')) {
            const productoItems = document.querySelectorAll('.producto-item');
            if (productoItems.length > 1) {
                const itemToRemove = e.target.closest('.producto-item');
                itemToRemove.remove();
                calcularTotales();
            }
        }
    });
    
    // Calcular subtotal cuando cambia cantidad o precio
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('cantidad') || e.target.classList.contains('precio')) {
            const item = e.target.closest('.producto-item');
            const cantidad = parseFloat(item.querySelector('.cantidad').value) || 0;
            const precio = parseFloat(item.querySelector('.precio').value.replace(/\./g, '').replace(',', '.')) || 0;
            const subtotal = cantidad * precio;
            
            item.querySelector('.subtotal').value = formatoCOP(subtotal);
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
                item.querySelector('.precio').value = formatoCOP(precio);
                
                // Trigger input event para calcular subtotal
                const event = new Event('input');
                item.querySelector('.precio').dispatchEvent(event);
            }
        }
    });
    
    function calcularTotales() {
        let subtotalTotal = 0;
        
        // Calcular subtotal de todos los productos
        document.querySelectorAll('.producto-item').forEach(item => {
            const subtotalText = item.querySelector('.subtotal').value;
            const subtotal = parseFloat(subtotalText.replace(/\./g, '').replace(',', '.')) || 0;
            subtotalTotal += subtotal;
        });
        
        // Obtener descuento
        const descuento = parseFloat(inputDescuento.value) || 0;
        const totalCompra = Math.max(0, subtotalTotal - descuento);
        
        // Actualizar displays
        document.getElementById('subtotal-total').textContent = formatoCOP(subtotalTotal);
        document.getElementById('descuento-aplicado').textContent = formatoCOP(descuento);
        document.getElementById('total-compra').textContent = formatoCOP(totalCompra);
        
        // Actualizar hidden inputs
        document.getElementById('input-subtotal-compra').value = subtotalTotal;
        document.getElementById('input-total-compra').value = totalCompra;
    }
    
    // Aplicar formato COP a los inputs de precio existentes
    document.querySelectorAll('.precio').forEach(formatearInputCOP);
    
    // Calcular totales iniciales
    calcularTotales();
});
</script>