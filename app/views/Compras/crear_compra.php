<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?page=login');
    exit();
}

// Incluir controladores
$compraController = new CompraController($db);
$proveedorController = new ProveedorController($db);
$productoController = new ProductoController($db);

$proveedores = $proveedorController->listar();
$productos = $productoController->listar();

// Obtener consecutivo de compra
$proximoCodigo = $compraController->obtenerProximoCodigo();

// Estados de compra
$estadosCompra = [
    'Pagada' => 'Pagada',
    'Pendiente' => 'Pendiente', 
    'Crédito' => 'Crédito'
];

// Guardar compra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DEBUG: Ver qué está llegando
    error_log("Total recibido: " . $_POST['total_compra']);
    
    // Método robusto para limpiar el formato del total
    $totalRecibido = $_POST['total_compra'];
    
    // Si ya viene como número (cuando JavaScript limpia el formato)
    if (is_numeric($totalRecibido)) {
        $totalLimpio = floatval($totalRecibido);
    } else {
        // Limpiar formato completo (quitar $, espacios, puntos de miles, coma decimal por punto)
        $totalLimpio = str_replace(['$', ' ', '.'], '', $totalRecibido);
        $totalLimpio = str_replace(',', '.', $totalLimpio);
        $totalLimpio = floatval($totalLimpio);
    }
    
    // Procesar descuento si aplica
    $descuentoAplicado = 0;
    $porcentajeDescuento = 0;
    
    if (isset($_POST['aplicar_descuento']) && $_POST['aplicar_descuento'] === 'on') {
        $porcentajeDescuento = floatval($_POST['porcentaje_descuento'] ?? 0);
        if ($porcentajeDescuento > 0 && $porcentajeDescuento <= 100) {
            $descuentoAplicado = ($totalLimpio * $porcentajeDescuento) / 100;
            $totalLimpio = $totalLimpio - $descuentoAplicado;
        }
    }
    
    // Validar que el total sea un número válido
    if ($totalLimpio <= 0) {
        $error = "El total de la compra debe ser mayor a cero";
    } else {
        error_log("Total limpio: " . $totalLimpio);

        // Validar productos
        $productosCompra = [];
        if (isset($_POST['productos']) && is_array($_POST['productos'])) {
            foreach ($_POST['productos'] as $index => $prod) {
                if (!empty($prod['id_producto']) && !empty($prod['cantidad']) && $prod['cantidad'] > 0) {
                    // Limpiar también el precio unitario si viene formateado
                    $precioUnitario = $prod['precio_unitario'] ?? 0;
                    if (is_string($precioUnitario)) {
                        $precioUnitario = str_replace(',', '.', $precioUnitario);
                    }
                    
                    $productosCompra[] = [
                        'id_producto' => $prod['id_producto'],
                        'cantidad' => floatval($prod['cantidad']),
                        'precio_unitario' => floatval($precioUnitario)
                    ];
                }
            }
        }

        // Validar que haya productos
        if (empty($productosCompra)) {
            $error = "Debe agregar al menos un producto a la compra";
        } else {
            // Datos listos para guardar - CORREGIDOS los nombres de las columnas
            $datos = [
                'codigo_compra' => $_POST['codigo_compra'],
                'id_proveedor' => $_POST['id_proveedor'],
                'id_usuario' => $_SESSION['usuario_id'],
                'total_compra' => $totalLimpio,
                'descuento_aplicado' => $descuentoAplicado,
                'porcentaje_descuento' => $porcentajeDescuento,
                'estado' => $_POST['estado'],
                'productos' => $productosCompra
            ];

            $compraGuardada = $compraController->crear($datos);

            if ($compraGuardada) {
                $mensajeTotal = "Compra registrada correctamente. Total: $" . number_format($totalLimpio, 2, ',', '.');
                if ($descuentoAplicado > 0) {
                    $mensajeTotal .= " (Descuento aplicado: " . number_format($porcentajeDescuento, 2, ',', '.') . "%)";
                }
                $_SESSION['mensaje'] = $mensajeTotal;
                $_SESSION['tipo_mensaje'] = "success";
                header("Location: index.php?page=compras");
                exit;
            } else {
                $error = "Error al registrar la compra. Verifique los datos e intente nuevamente.";
            }
        }
    }
}

// Función para formatear números en formato COP
function formatoCOP($numero) {
    return number_format($numero, 2, ',', '.');
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2 text-white"><i class="fas fa-plus me-2"></i>Registrar Nueva Compra</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=compras" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Compras
            </a>
        </div>
    </div>

    <!-- Mostrar mensajes de error -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mt-4" style="max-width: 950px; margin: 0 auto; border: 1px solid #00ff00;">
        <div class="card-body text-dark rounded-4">

        <!-- FORMULARIO -->
        <form id="formCompra" method="POST" action="">
            <input type="hidden" name="id_usuario" value="<?= $_SESSION['usuario_id'] ?>">

            <div class="row mb-3 text-white">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Código Compra</label>
                    <input type="text" class="form-control" name="codigo_compra" value="<?= $proximoCodigo ?>" readonly>
                </div>
                <div class="col-md-4 text-white">
                    <label class="form-label fw-bold">Proveedor *</label>
                    <select name="id_proveedor" class="form-select" required>
                        <option value="">Seleccionar proveedor</option>
                        <?php foreach ($proveedores as $proveedor): ?>
                            <option value="<?= $proveedor['id_proveedor'] ?>"><?= htmlspecialchars($proveedor['nombre_proveedor']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 text-white">
                    <label class="form-label fw-bold">Estado de Pago *</label>
                    <select name="estado" class="form-select" required>
                        <?php foreach ($estadosCompra as $valor => $texto): ?>
                            <option value="<?= $valor ?>" <?= $valor == 'Pagada' ? 'selected' : '' ?>>
                                <?= $texto ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <hr>

            <div class="mb-3 text-white">
                <h5><i class="fas fa-boxes text-primary me-2 text-white"></i>Productos de la Compra</h5>

                <div id="productos-container">
                    <div class="producto-item row g-3 py-2 align-items-center">
                        <div class="col-md-4">
                            <select class="form-select producto-select" name="productos[0][id_producto]" required>
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
                            <input type="number" class="form-control cantidad" name="productos[0][cantidad]" placeholder="Cant." min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">$</span>
                                <input type="text" class="form-control precio" name="productos[0][precio_unitario]" placeholder="0,00" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">$</span>
                                <input type="text" class="form-control subtotal" readonly placeholder="0,00">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm eliminar-producto">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" id="agregar-producto" class="btn btn-neon btn-sm mt-2">
                    <i class="fas fa-plus"></i> Agregar Producto
                </button>
                <button type="button" id="limpiar-formulario" class="btn btn-danger btn-sm mt-2">
                    <i class="fas fa-broom"></i> Limpiar
                </button>
            </div>

            <!-- SECCIÓN DE DESCUENTO -->
            <div class="mt-4 p-2 bg-light text-dark rounded-3 shadow-sm">
                <div class="col-md-12">
                    <div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="aplicar_descuento" name="aplicar_descuento">
                                        <label class="form-check-label fw-bold" for="aplicar_descuento">
                                            <i class="fas fa-tag me-2 text-warning"></i>Aplicar Descuento
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Porcentaje de Descuento</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="porcentaje_descuento" name="porcentaje_descuento" 
                                               min="0" max="100" step="0.01" placeholder="0.00" disabled>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold">Valor del Descuento</label>
                                    <input type="text" class="form-control" id="valor_descuento" readonly value="$0,00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 p-2 bg-light text-dark rounded-3 shadow-sm">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h5 class="fw-bold text-dark mb-0">Subtotal:</h5>
                        <input type="text" id="subtotal_compra" class="form-control text-end fw-bold" readonly value="$0,00">
                    </div>
                    <div class="col-md-4">
                        <h5 class="fw-bold text-dark mb-0">Descuento:</h5>
                        <input type="text" id="descuento_aplicado_display" class="form-control text-end fw-bold text-danger" readonly value="$0,00">
                    </div>
                    <div class="col-md-4">
                        <h5 class="fw-bold text-dark mb-0">Total Compra:</h5>
                        <input type="text" id="total_compra" name="total_compra" class="form-control text-end fw-bold fs-5 text-success"
                               readonly value="$0,00">
                        <input type="hidden" id="total_compra_limpio" name="total_compra_limpio" value="0">
                        <input type="hidden" id="descuento_aplicado" name="descuento_aplicado" value="0">
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-neon btn-lg px-5">
                    <i class="fas fa-save me-2"></i>Registrar Compra
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const contenedor = document.getElementById("productos-container");
    const btnAgregar = document.getElementById("agregar-producto");
    const btnLimpiar = document.getElementById("limpiar-formulario");
    const totalCompraInput = document.getElementById("total_compra");
    const totalCompraLimpioInput = document.getElementById("total_compra_limpio");
    const subtotalCompraInput = document.getElementById("subtotal_compra");
    const descuentoAplicadoInput = document.getElementById("descuento_aplicado");
    const descuentoAplicadoDisplay = document.getElementById("descuento_aplicado_display");
    const formCompra = document.getElementById("formCompra");
    const aplicarDescuentoCheckbox = document.getElementById("aplicar_descuento");
    const porcentajeDescuentoInput = document.getElementById("porcentaje_descuento");
    const valorDescuentoInput = document.getElementById("valor_descuento");

    // Función para convertir string COP a número
    function copANumero(copString) {
        if (!copString) return 0;
        // Remover símbolo $, puntos de miles y convertir coma decimal a punto
        const limpio = copString.toString().replace(/\$/g, '').replace(/\./g, '').replace(',', '.');
        return parseFloat(limpio) || 0;
    }

    // Función para formatear números en formato COP
    function formatoCOP(valor) {
        if (!isFinite(valor)) valor = 0;
        const fijo = Number(valor).toFixed(2);
        const parts = fijo.split('.');
        const intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return '$' + intPart + ',' + parts[1];
    }

    // Función para formatear input en tiempo real (COP)
    function formatearInputCOP(input) {
        // Al perder foco, formatear como COP
        input.addEventListener('blur', function() {
            const valor = copANumero(this.value);
            this.value = formatoCOP(valor);
            // Recalcular cuando se formatea
            actualizarTotales();
        });
        
        // Al obtener foco, quitar formato para edición
        input.addEventListener('focus', function() {
            const valor = copANumero(this.value);
            this.value = valor > 0 ? valor.toString() : '';
        });
    }

    // Calcular subtotal de un producto individual
    function calcularSubtotalProducto(item) {
        const cantidad = parseFloat(item.querySelector(".cantidad").value) || 0;
        const precio = copANumero(item.querySelector(".precio").value) || 0;
        return cantidad * precio;
    }

    // Calcular totales con/sin descuento
    function actualizarTotales() {
        console.log("=== ACTUALIZANDO TOTALES ===");
        
        let subtotal = 0;
        
        // Calcular subtotal de cada producto y sumar
        contenedor.querySelectorAll(".producto-item").forEach((item, index) => {
            const subtotalProducto = calcularSubtotalProducto(item);
            item.querySelector(".subtotal").value = formatoCOP(subtotalProducto);
            subtotal += subtotalProducto;
            console.log(`Producto ${index + 1}: ${subtotalProducto}`);
        });
        
        console.log("Subtotal total:", subtotal);
        subtotalCompraInput.value = formatoCOP(subtotal);
        
        // Calcular descuento por porcentaje si está activo
        let descuento = 0;
        let total = subtotal;
        
        if (aplicarDescuentoCheckbox.checked && porcentajeDescuentoInput.value) {
            const porcentaje = parseFloat(porcentajeDescuentoInput.value) || 0;
            if (porcentaje > 0 && porcentaje <= 100) {
                descuento = (subtotal * porcentaje) / 100;
                total = Math.max(0, subtotal - descuento);
                console.log(`Descuento: ${porcentaje}% = ${descuento}`);
            }
        }
        
        console.log("Descuento aplicado:", descuento);
        console.log("Total final:", total);
        
        // Actualizar todos los displays
        descuentoAplicadoInput.value = descuento.toFixed(2);
        descuentoAplicadoDisplay.value = formatoCOP(descuento);
        valorDescuentoInput.value = formatoCOP(descuento);
        totalCompraInput.value = formatoCOP(total);
        totalCompraLimpioInput.value = total.toFixed(2);
    }

    // Habilitar/deshabilitar campo de porcentaje de descuento
    aplicarDescuentoCheckbox.addEventListener("change", function() {
        porcentajeDescuentoInput.disabled = !this.checked;
        if (!this.checked) {
            porcentajeDescuentoInput.value = "";
            valorDescuentoInput.value = "$0,00";
        } else {
            porcentajeDescuentoInput.focus();
        }
        actualizarTotales();
    });

    // Calcular descuento cuando cambia el porcentaje
    porcentajeDescuentoInput.addEventListener("input", function() {
        const valor = parseFloat(this.value) || 0;
        if (valor < 0) {
            this.value = 0;
        } else if (valor > 100) {
            this.value = 100;
        }
        actualizarTotales();
    });

    porcentajeDescuentoInput.addEventListener("blur", function() {
        actualizarTotales();
    });

    // Función para limpiar el formulario
    function limpiarFormulario() {
        // Confirmar con el usuario
        if (!confirm("¿Está seguro de que desea limpiar el formulario? Se perderán todos los datos ingresados.")) {
            return;
        }
        
        // Limpiar campos del proveedor y estado
        document.querySelector('select[name="id_proveedor"]').selectedIndex = 0;
        document.querySelector('select[name="estado"]').value = 'Pagada';
        
        // Limpiar descuento
        aplicarDescuentoCheckbox.checked = false;
        porcentajeDescuentoInput.value = "";
        porcentajeDescuentoInput.disabled = true;
        valorDescuentoInput.value = "$0,00";
        
        // Limpiar todos los productos excepto el primero
        const productosItems = contenedor.querySelectorAll(".producto-item");
        productosItems.forEach((item, index) => {
            if (index === 0) {
                // Primer producto: resetear valores
                item.querySelector(".producto-select").selectedIndex = 0;
                item.querySelector(".cantidad").value = "";
                item.querySelector(".precio").value = "";
                item.querySelector(".subtotal").value = "$0,00";
            } else {
                // Eliminar productos adicionales
                item.remove();
            }
        });
        
        // Resetear totales
        subtotalCompraInput.value = "$0,00";
        descuentoAplicadoDisplay.value = "$0,00";
        totalCompraInput.value = "$0,00";
        totalCompraLimpioInput.value = "0";
        descuentoAplicadoInput.value = "0";
    }

    // Seleccionar producto - Auto-completar precio
    contenedor.addEventListener("change", e => {
        if (e.target.classList.contains("producto-select")) {
            const opt = e.target.selectedOptions[0];
            const precio = parseFloat(opt.getAttribute("data-precio")) || 0;
            const item = e.target.closest(".producto-item");
            
            if (precio > 0) {
                item.querySelector(".precio").value = formatoCOP(precio);
                // Calcular subtotal automáticamente si hay cantidad
                const cantidad = parseFloat(item.querySelector(".cantidad").value) || 0;
                if (cantidad > 0) {
                    actualizarTotales();
                }
            }
        }
    });

    // Cambiar cantidad - Recalcular automáticamente
    contenedor.addEventListener("input", e => {
        if (e.target.classList.contains("cantidad")) {
            const item = e.target.closest(".producto-item");
            const precio = copANumero(item.querySelector(".precio").value) || 0;
            const cantidad = parseFloat(e.target.value) || 0;
            
            if (cantidad > 0 && precio > 0) {
                const subtotalProducto = cantidad * precio;
                item.querySelector(".subtotal").value = formatoCOP(subtotalProducto);
                actualizarTotales();
            }
        }
    });

    // Cambiar precio - Recalcular automáticamente
    contenedor.addEventListener("input", e => {
        if (e.target.classList.contains("precio")) {
            const item = e.target.closest(".producto-item");
            const cantidad = parseFloat(item.querySelector(".cantidad").value) || 0;
            const precio = copANumero(e.target.value) || 0;
            
            if (cantidad > 0 && precio > 0) {
                const subtotalProducto = cantidad * precio;
                item.querySelector(".subtotal").value = formatoCOP(subtotalProducto);
                actualizarTotales();
            }
        }
    });

    // También recalcular cuando se pierde el foco en precio
    contenedor.addEventListener("blur", e => {
        if (e.target.classList.contains("precio")) {
            actualizarTotales();
        }
    });

    // Agregar producto
    btnAgregar.addEventListener("click", () => {
        const index = contenedor.querySelectorAll(".producto-item").length;
        const nuevo = contenedor.firstElementChild.cloneNode(true);
        
        // Actualizar nombres de los inputs
        nuevo.querySelectorAll("input, select").forEach(el => {
            if (el.name) {
                el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
            }
            if (el.type !== 'hidden') {
                el.value = "";
            }
            if (el.classList.contains('subtotal')) {
                el.value = "$0,00";
            }
            if (el.tagName === "SELECT") {
                el.selectedIndex = 0;
            }
        });
        
        contenedor.appendChild(nuevo);
        
        // Aplicar formato COP al nuevo input de precio
        formatearInputCOP(nuevo.querySelector('.precio'));
        
        // Agregar event listeners al nuevo producto
        agregarEventListenersProducto(nuevo);
    });

    // Función para agregar event listeners a productos nuevos
    function agregarEventListenersProducto(productoItem) {
        const cantidadInput = productoItem.querySelector('.cantidad');
        const precioInput = productoItem.querySelector('.precio');
        const productoSelect = productoItem.querySelector('.producto-select');
        
        cantidadInput.addEventListener('input', function() {
            const cantidad = parseFloat(this.value) || 0;
            const precio = copANumero(precioInput.value) || 0;
            if (cantidad > 0 && precio > 0) {
                const subtotal = cantidad * precio;
                productoItem.querySelector('.subtotal').value = formatoCOP(subtotal);
                actualizarTotales();
            }
        });
        
        precioInput.addEventListener('input', function() {
            const precio = copANumero(this.value) || 0;
            const cantidad = parseFloat(cantidadInput.value) || 0;
            if (cantidad > 0 && precio > 0) {
                const subtotal = cantidad * precio;
                productoItem.querySelector('.subtotal').value = formatoCOP(subtotal);
                actualizarTotales();
            }
        });
        
        productoSelect.addEventListener('change', function() {
            const opt = this.selectedOptions[0];
            const precio = parseFloat(opt.getAttribute("data-precio")) || 0;
            if (precio > 0) {
                precioInput.value = formatoCOP(precio);
                const cantidad = parseFloat(cantidadInput.value) || 0;
                if (cantidad > 0) {
                    const subtotal = cantidad * precio;
                    productoItem.querySelector('.subtotal').value = formatoCOP(subtotal);
                    actualizarTotales();
                }
            }
        });
    }

    // Eliminar producto
    contenedor.addEventListener("click", e => {
        if (e.target.closest(".eliminar-producto")) {
            const items = contenedor.querySelectorAll(".producto-item");
            if (items.length > 1) {
                e.target.closest(".producto-item").remove();
                actualizarTotales();
            } else {
                alert("Debe haber al menos un producto en la compra");
            }
        }
    });

    // Limpiar formulario
    btnLimpiar.addEventListener("click", limpiarFormulario);

    // Validar formulario antes de enviar
    formCompra.addEventListener('submit', function(e) {
        // Usar el valor limpio en el campo principal
        totalCompraInput.value = totalCompraLimpioInput.value;
        
        // Validar que haya al menos un producto con cantidad
        let productosValidos = false;
        contenedor.querySelectorAll(".producto-item").forEach(item => {
            const productoSelect = item.querySelector(".producto-select");
            const cantidadInput = item.querySelector(".cantidad");
            const precioInput = item.querySelector(".precio");
            
            if (productoSelect.value && cantidadInput.value && cantidadInput.value > 0 && precioInput.value) {
                productosValidos = true;
            }
        });
        
        if (!productosValidos) {
            e.preventDefault();
            alert("Debe agregar al menos un producto con cantidad y precio válidos");
            return;
        }
        
        // Validar proveedor
        const proveedorSelect = document.querySelector('select[name="id_proveedor"]');
        if (!proveedorSelect.value) {
            e.preventDefault();
            alert("Debe seleccionar un proveedor");
            return;
        }
        
        // Validar descuento si está activo
        if (aplicarDescuentoCheckbox.checked) {
            const porcentaje = parseFloat(porcentajeDescuentoInput.value) || 0;
            if (porcentaje <= 0 || porcentaje > 100) {
                e.preventDefault();
                alert("El porcentaje de descuento debe estar entre 0.01% y 100%");
                return;
            }
        }
    });

    // Aplicar formato COP a los inputs de precio existentes
    document.querySelectorAll('.precio').forEach(formatearInputCOP);
    
    // Agregar event listeners a los productos iniciales
    document.querySelectorAll('.producto-item').forEach(agregarEventListenersProducto);

    // Calcular totales iniciales
    actualizarTotales();
});
</script>