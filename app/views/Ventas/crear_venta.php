<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir controladores
$clienteController = new ClienteController($db);
$productoController = new ProductoController($db);
$ventaController = new VentaController($db);

$clientes = $clienteController->listar();
$productos = $productoController->listar();

// Obtener consecutivo de venta
$ultimoCodigo = $ventaController->obtenerUltimoCodigo();
if ($ultimoCodigo) {
    $num = (int) filter_var($ultimoCodigo, FILTER_SANITIZE_NUMBER_INT);
    $nuevoCodigo = 'VENTA' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
} else {
    $nuevoCodigo = 'VENTA001';
}

// Guardar venta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DEBUG: Ver qué está llegando
    error_log("Total recibido: " . $_POST['total_venta']);
    
    // Método robusto para limpiar el formato del total
    $totalRecibido = $_POST['total_venta'];
    
    // Si ya viene como número (cuando JavaScript limpia el formato)
    if (is_numeric($totalRecibido)) {
        $totalLimpio = floatval($totalRecibido);
    } else {
        // Limpiar formato completo (quitar $, espacios, puntos de miles, coma decimal por punto)
        $totalLimpio = str_replace(['$', ' ', '.'], '', $totalRecibido);
        $totalLimpio = str_replace(',', '.', $totalLimpio);
        $totalLimpio = floatval($totalLimpio);
    }
    
    // Validar que el total sea un número válido
    if ($totalLimpio <= 0) {
        $error = "El total de la venta debe ser mayor a cero";
    } else {
        error_log("Total limpio: " . $totalLimpio);

        // Validar productos
        $productosVenta = [];
        if (isset($_POST['productos']) && is_array($_POST['productos'])) {
            foreach ($_POST['productos'] as $index => $prod) {
                if (!empty($prod['id_producto']) && !empty($prod['cantidad']) && $prod['cantidad'] > 0) {
                    // Limpiar también el precio unitario si viene formateado
                    $precioUnitario = $prod['precio_unitario'] ?? 0;
                    if (is_string($precioUnitario)) {
                        $precioUnitario = str_replace(',', '.', $precioUnitario);
                    }
                    
                    $productosVenta[] = [
                        'id_producto' => $prod['id_producto'],
                        'cantidad' => intval($prod['cantidad']),
                        'precio_unitario' => floatval($precioUnitario)
                    ];
                }
            }
        }

        // Validar que haya productos
        if (empty($productosVenta)) {
            $error = "Debe agregar al menos un producto a la venta";
        } else {
            // Datos listos para guardar
            $datos = [
                'codigo_venta' => $_POST['codigo_venta'],
                'id_cliente' => !empty($_POST['id_cliente']) ? $_POST['id_cliente'] : null,
                'id_usuario' => $_SESSION['id_usuario'] ?? 1,
                'metodo_pago' => $_POST['metodo_pago'],
                'total_venta' => $totalLimpio,
                'estado' => 'Pendiente',
                'productos' => $productosVenta
            ];

            $ventaGuardada = $ventaController->crear($datos);

            if ($ventaGuardada) {
                $_SESSION['mensaje'] = "Venta registrada correctamente. Total: $" . number_format($totalLimpio, 2, ',', '.');
                $_SESSION['tipo_mensaje'] = "success";
                header("Location: index.php?page=ventas");
                exit;
            } else {
                $error = "Error al registrar la venta. Verifique los datos e intente nuevamente.";
            }
        }
    }
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:120px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2"><i class="fas fa-plus me-2"></i>Registrar Nueva Venta</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=ventas" class="btn btn-neon">
                <i class="fas fa-arrow-left me-2"></i>Volver
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
        <form id="formVenta" method="POST" action="">
            <input type="hidden" name="id_usuario" value="<?= $_SESSION['id_usuario'] ?? 1 ?>">
            <input type="hidden" name="estado" value="Pendiente">

            <div class="row mb-3 text-white">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Código Venta</label>
                    <input type="text" class="form-control" name="codigo_venta" value="<?= $nuevoCodigo ?>" readonly>
                </div>
                <div class="col-md-4 text-white">
                    <label class="form-label fw-bold">Cliente</label>
                    <select name="id_cliente" class="form-select">
                        <option value="">Cliente General</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $cliente['id_cliente'] ?>"><?= htmlspecialchars($cliente['nombre_cliente']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 text-white">
                    <label class="form-label fw-bold">Método de Pago</label>
                    <select name="metodo_pago" class="form-select" required>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Crédito">Crédito</option>
                    </select>
                </div>
            </div>

            <hr>

            <div class="mb-3 text-white">
                <h5><i class="fas fa-boxes text-primary me-2 text-white"></i>Productos de la Venta</h5>

                <div id="productos-container">
                    <div class="producto-item row mb-3">
                        <div class="col-md-4">
                            <select class="form-select producto-select" name="productos[0][id_producto]" required>
                                <option value="">Seleccionar producto</option>
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?= $producto['id_producto'] ?>"
                                            data-precio="<?= $producto['precio_venta'] ?>"
                                            data-stock="<?= $producto['stock_actual'] ?>">
                                        <?= htmlspecialchars($producto['nombre_producto']) ?> (Stock: <?= $producto['stock_actual'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control cantidad" name="productos[0][cantidad]" placeholder="Cant." min="1" required>
                        </div>
                        <div class="col-md-3">
                            <input type="hidden" class="precio" name="productos[0][precio_unitario]" value="">
                            <input type="text" class="form-control precio_display" placeholder="$0,00" readonly>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control subtotal" readonly placeholder="$0,00">
                        </div>
                        <div class="col-md-1 d-flex justify-content-center">
                            <button type="button" class="btn btn-danger btn-sm eliminar-producto">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" id="agregar-producto" class="btn btn-neon btn-sm mt-2">
                    <i class="fas fa-plus"></i> Agregar Producto
                </button>
            </div>

            <div class="mt-4 p-2 bg-light text-dark rounded-3 shadow-sm">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="fw-bold text-dark">Total Venta:</h5>
                    </div>
                    <div class="col-md-6">
                        <input type="text" id="total_venta" name="total_venta" class="form-control text-end fw-bold fs-5"
                               readonly value="$0,00">
                        <input type="hidden" id="total_venta_limpio" name="total_venta_limpio" value="0">
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-neon btn-lg px-5">
                    <i class="fas fa-save me-2"></i>Registrar Venta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const contenedor = document.getElementById("productos-container");
    const btnAgregar = document.getElementById("agregar-producto");
    const totalVentaInput = document.getElementById("total_venta");
    const totalVentaLimpioInput = document.getElementById("total_venta_limpio");
    const formVenta = document.getElementById("formVenta");

    // Formato COP
    function formatoCOP(valor) {
        if (!isFinite(valor)) valor = 0;
        const fijo = Number(valor).toFixed(2);
        const parts = fijo.split('.');
        const intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return '$' + intPart + ',' + parts[1];
    }

    // Convertir texto a número
    function textoANumero(str) {
        if (!str) return 0;
        let tmp = str.replace(/\s/g, '').replace(/\$/g, '').replace(/\./g, '').replace(',', '.');
        return parseFloat(tmp) || 0;
    }

    // Limpiar formato para enviar
    function limpiarFormatoCOP(str) {
        if (!str) return '0';
        return str.replace(/\$/g, '').replace(/\./g, '').replace(',', '.');
    }

    // Calcular totales
    function actualizarTotales() {
        let total = 0;
        contenedor.querySelectorAll(".producto-item").forEach(item => {
            const cantidad = parseFloat(item.querySelector(".cantidad").value) || 0;
            const precio = parseFloat(item.querySelector(".precio").value) || 0;
            const subtotal = cantidad * precio;
            item.querySelector(".subtotal").value = formatoCOP(subtotal);
            total += subtotal;
        });
        totalVentaInput.value = formatoCOP(total);
        totalVentaLimpioInput.value = total.toFixed(2); // Guardar el valor limpio
    }

    // Seleccionar producto
    contenedor.addEventListener("change", e => {
        if (e.target.classList.contains("producto-select")) {
            const opt = e.target.selectedOptions[0];
            const precio = parseFloat(opt.getAttribute("data-precio")) || 0;
            const stock = parseInt(opt.getAttribute("data-stock")) || 0;
            const item = e.target.closest(".producto-item");
            
            item.querySelector(".precio").value = precio.toFixed(2);
            item.querySelector(".precio_display").value = formatoCOP(precio);
            
            // Limitar cantidad al stock disponible
            const cantidadInput = item.querySelector(".cantidad");
            cantidadInput.max = stock;
            
            actualizarTotales();
        }
    });

    // Cambiar cantidad
    contenedor.addEventListener("input", e => {
        if (e.target.classList.contains("cantidad")) {
            const item = e.target.closest(".producto-item");
            const select = item.querySelector(".producto-select");
            const stock = parseInt(select.selectedOptions[0]?.getAttribute("data-stock")) || 0;
            const cantidad = parseInt(e.target.value) || 0;
            
            if (cantidad > stock) {
                e.target.value = stock;
                alert(`No hay suficiente stock. Stock disponible: ${stock}`);
            }
            
            actualizarTotales();
        }
    });

    // Agregar producto
    btnAgregar.addEventListener("click", () => {
        const index = contenedor.querySelectorAll(".producto-item").length;
        const nuevo = contenedor.firstElementChild.cloneNode(true);
        
        nuevo.querySelectorAll("input, select").forEach(el => {
            if (el.name) {
                el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
            }
            if (el.type !== 'hidden') {
                el.value = "";
            }
            if (el.tagName === "SELECT") {
                el.selectedIndex = 0;
            }
        });
        
        contenedor.appendChild(nuevo);
    });

    // Eliminar producto
    contenedor.addEventListener("click", e => {
        if (e.target.closest(".eliminar-producto")) {
            const items = contenedor.querySelectorAll(".producto-item");
            if (items.length > 1) {
                e.target.closest(".producto-item").remove();
                actualizarTotales();
            } else {
                alert("Debe haber al menos un producto en la venta");
            }
        }
    });

    // IMPORTANTE: Limpiar el formato antes de enviar el formulario
    formVenta.addEventListener('submit', function(e) {
        // Usar el valor limpio en el campo principal
        totalVentaInput.value = totalVentaLimpioInput.value;
        
        // Validar que haya al menos un producto con cantidad
        let productosValidos = false;
        contenedor.querySelectorAll(".producto-item").forEach(item => {
            const productoSelect = item.querySelector(".producto-select");
            const cantidadInput = item.querySelector(".cantidad");
            
            if (productoSelect.value && cantidadInput.value && cantidadInput.value > 0) {
                productosValidos = true;
            }
        });
        
        if (!productosValidos) {
            e.preventDefault();
            alert("Debe agregar al menos un producto con cantidad válida");
            return;
        }
        
        // Validar stock
        let stockValido = true;
        contenedor.querySelectorAll(".producto-item").forEach(item => {
            const productoSelect = item.querySelector(".producto-select");
            const cantidadInput = item.querySelector(".cantidad");
            const stock = parseInt(productoSelect.selectedOptions[0]?.getAttribute("data-stock")) || 0;
            const cantidad = parseInt(cantidadInput.value) || 0;
            
            if (productoSelect.value && cantidad > stock) {
                stockValido = false;
                const productoNombre = productoSelect.selectedOptions[0].text.split(' (')[0];
                alert(`Stock insuficiente para: ${productoNombre}\nStock disponible: ${stock}`);
            }
        });
        
        if (!stockValido) {
            e.preventDefault();
            return;
        }
        
        // El formulario se enviará con los valores limpios
    });

    actualizarTotales();
});
</script>