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

// Obtener consecutivo de factura
$ultimaFactura = $ventaController->obtenerUltimaFactura();
if ($ultimaFactura) {
    $numFactura = (int) str_replace('FACT', '', $ultimaFactura);
    $nuevaFactura = 'FACT' . str_pad($numFactura + 1, 7, '0', STR_PAD_LEFT);
} else {
    $nuevaFactura = 'FACT0000001';
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
                'factura' => $_POST['factura'],
                'id_cliente' => !empty($_POST['id_cliente']) ? $_POST['id_cliente'] : null,
                'id_usuario' => $_SESSION['id_usuario'] ?? 1,
                'metodo_pago' => $_POST['metodo_pago'],
                'total_venta' => $totalLimpio,
                'descuento_aplicado' => $descuentoAplicado,
                'porcentaje_descuento' => $porcentajeDescuento,
                'estado' => 'Pendiente',
                'productos' => $productosVenta
            ];

            $ventaGuardada = $ventaController->crear($datos);

            if ($ventaGuardada) {
                $mensajeTotal = "Venta registrada correctamente. Total: $" . number_format($totalLimpio, 2, ',', '.');
                if ($descuentoAplicado > 0) {
                    $mensajeTotal .= " (Descuento aplicado: " . number_format($porcentajeDescuento, 2, ',', '.') . "%)";
                }
                
                // En lugar de redireccionar inmediatamente, guardamos los datos para mostrar el modal
                $_SESSION['venta_exitosa'] = true;
                $_SESSION['mensaje_venta'] = $mensajeTotal;
                $_SESSION['codigo_venta'] = $datos['codigo_venta'];
                $_SESSION['total_venta'] = $totalLimpio;
                
                header("Location: index.php?page=ventas");
                exit;
            } else {
                $error = "Error al registrar la venta. Verifique los datos e intente nuevamente.";
            }
        }
    }
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2"><i class="fas fa-plus me-2"></i>Registrar Nueva Venta</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=ventas" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Ventas
            </a>
        </div>
    </div>

    <div class="card shadow-sm mt-4" style="max-width: 950px; margin: 0 auto; border: 1px solid #00ff00;">
        <div class="card-body text-dark rounded-4">

        <!-- FORMULARIO -->
        <form id="formVenta" method="POST" action="">
            <input type="hidden" name="id_usuario" value="<?= $_SESSION['id_usuario'] ?? 1 ?>">
            <input type="hidden" name="estado" value="Pendiente">

            <div class="row mb-3 text-white">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Código Venta</label>
                    <input type="text" class="form-control" name="codigo_venta" value="<?= $nuevoCodigo ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">N° Factura</label>
                    <input type="text" class="form-control" name="factura" value="<?= $nuevaFactura ?>" readonly>
                </div>
                <div class="col-md-3 text-white">
                    <label class="form-label fw-bold">Cliente</label>
                    <select name="id_cliente" class="form-select">
                        <option value="">Cliente General</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $cliente['id_cliente'] ?>"><?= htmlspecialchars($cliente['nombre_cliente']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 text-white">
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
                        <div class="col-md-2">
                            <input type="hidden" class="precio" name="productos[0][precio_unitario]" value="">
                            <input type="text" class="form-control precio_display" placeholder="$0,00" readonly>
                        </div>
                        <div class="col-md-3">
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
                <button type="button" id="limpiar-formulario" class="btn btn-danger btn-sm mt-2">
                    <i class="fas fa-undo me-1"></i>Limpiar
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
                        <input type="text" id="subtotal_venta" class="form-control text-end fw-bold" readonly value="$0,00">
                    </div>
                    <div class="col-md-4">
                        <h5 class="fw-bold text-dark mb-0">Descuento:</h5>
                        <input type="text" id="descuento_aplicado_display" class="form-control text-end fw-bold text-danger" readonly value="$0,00">
                    </div>
                    <div class="col-md-4">
                        <h5 class="fw-bold text-dark mb-0">Total Venta:</h5>
                        <input type="text" id="total_venta" name="total_venta" class="form-control text-end fw-bold fs-5 text-success"
                               readonly value="$0,00">
                        <input type="hidden" id="total_venta_limpio" name="total_venta_limpio" value="0">
                        <input type="hidden" id="descuento_aplicado" name="descuento_aplicado" value="0">
                    </div>
                </div>
            </div>

            <div class="alert alert-success my-4">
                <small>
                    <i class="fas fa-info-circle me-2"></i>
                    Los campos marcados con * son obligatorios.
                </small>
            </div> 

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-neon btn-lg px-5">
                    <i class="fas fa-save me-2"></i>Registrar Venta
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL PARA ERRORES DE VENTA -->
<div class="modal fade" id="modalErrorVenta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <!-- Header del Modal -->
            <div class="modal-header bg-danger text-white py-3 rounded-top-3">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-shrink-0">
                        <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="modal-title fw-bold mb-0">Error al Registrar Venta</h5>
                        <p class="mb-0 opacity-85 small">Validación de Datos</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            
            <!-- Body del Modal -->
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="bg-danger bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                    </div>
                    <h6 class="fw-semibold text-dark mb-3" id="modalErrorMessage">
                        <!-- El mensaje de error se insertará aquí -->
                    </h6>
                </div>
                
                <div class="alert alert-warning border-warning border-2">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle text-warning me-2 mt-1"></i>
                        <div class="flex-grow-1">
                            <small class="text-muted">
                                Verifique los datos ingresados y asegúrese de que todos los campos obligatorios estén completos.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer del Modal -->
            <div class="modal-footer border-top-0 bg-light rounded-bottom-3 py-3">
                <div class="w-100 d-flex justify-content-center">
                    <button type="button" class="btn btn-danger px-4 py-2 rounded-2 fw-semibold" data-bs-dismiss="modal">
                        <i class="fas fa-check-circle me-2"></i>
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA VENTA EXITOSA -->
<div class="modal fade" id="modalExitoVenta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <!-- Header del Modal -->
            <div class="modal-header bg-success text-white py-3 rounded-top-3">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-shrink-0">
                        <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="modal-title fw-bold mb-0">Venta Registrada Exitosamente</h5>
                        <p class="mb-0 opacity-85 small">Confirmación de Transacción</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            
            <!-- Body del Modal -->
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="fas fa-check fa-2x text-success"></i>
                    </div>
                    <h6 class="fw-semibold text-dark mb-2" id="modalExitoMessage">
                        <!-- El mensaje de éxito se insertará aquí -->
                    </h6>
                    <p class="text-muted small mb-0" id="modalExitoDetalles">
                        <!-- Detalles adicionales -->
                    </p>
                </div>
                
                <div class="alert alert-info border-info border-2">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-lightbulb text-info me-2 mt-1"></i>
                        <div class="flex-grow-1">
                            <small class="text-muted">
                                La venta ha sido registrada en el sistema. Puede consultarla en el listado de ventas.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer del Modal -->
            <div class="modal-footer border-top-0 bg-light rounded-bottom-3 py-3">
                <div class="w-100 d-flex justify-content-center">
                    <a href="index.php?page=ventas" class="btn btn-success px-4 py-2 rounded-2 fw-semibold">
                        <i class="fas fa-list me-2"></i>
                        Ver Lista de Ventas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PROFESIONAL PARA STOCK INSUFICIENTE - MÁS ANCHO Y COMPACTO -->
<div class="modal fade" id="modalStockInsuficiente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3" style="max-width: 700px; margin: 0 auto;">
            <!-- Header del Modal -->
            <div class="modal-header bg-danger text-white py-3 rounded-top-3">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-shrink-0">
                        <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="modal-title fw-bold mb-0">Stock Insuficiente</h5>
                        <p class="mb-0 opacity-85 small">Validación de Inventario</p>
                    </div>
                </div>
            </div>
            
            <!-- Body del Modal -->
            <div class="modal-body p-4">
                <div class="row">
                    <!-- Columna Izquierda: Información principal -->
                    <div class="col-md-6 border-end pe-md-4">
                        <!-- Icono Principal -->
                        <div class="text-center mb-3">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3 d-inline-block">
                                <i class="fas fa-box-open fa-2x text-danger"></i>
                            </div>
                        </div>

                        <!-- Información del Producto -->
                        <div class="text-center mb-4">
                            <h6 class="fw-semibold text-dark mb-2">
                                <i class="fas fa-cube me-2 text-primary"></i>
                                <span id="modalProductName">Cargando producto...</span>
                            </h6>
                        </div>

                        <!-- Tarjeta de Comparación Stock -->
                        <div class="card border-danger border-2 mb-3">
                            <div class="card-header bg-danger bg-opacity-5 py-2">
                                <small class="fw-bold text-dark">
                                    <i class="fas fa-chart-bar me-2"></i>Comparación de Stock
                                </small>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3 text-center">
                                    <!-- Stock Disponible -->
                                    <div class="col-6">
                                        <div class="d-flex flex-column align-items-center">
                                            <small class="text-muted fw-semibold mb-1">Stock Disponible</small>
                                            <div class="bg-success bg-opacity-10 p-2 w-100 border border-success border-opacity-25">
                                                <div class="fw-bold text-success fs-4" id="modalStockDisponible">0</div>
                                                <small class="text-success fw-semibold">unidades</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Cantidad Solicitada -->
                                    <div class="col-6">
                                        <div class="d-flex flex-column align-items-center">
                                            <small class="text-muted fw-semibold mb-1">Cantidad Solicitada</small>
                                            <div class="bg-danger bg-opacity-10 rounded-2 p-2 w-100 border border-danger border-opacity-25">
                                                <div class="fw-bold text-danger fs-4" id="modalCantidadSolicitada">0</div>
                                                <small class="text-danger fw-semibold">unidades</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Diferencia -->
                                <div class="mt-3 pt-2 border-top">
                                    <div class="d-flex justify-content-between align-items-center px-2">
                                        <small class="text-muted fw-semibold">Diferencia:</small>
                                        <span class="badge bg-danger fs-6 px-2 py-1" id="modalDiferencia">-0 unidades</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna Derecha: Alertas y soluciones -->
                    <div class="col-md-6 ps-md-4 justify-content-center">
                        <!-- Mensaje de Alerta -->
                        <div class="alert alert-warning border-warning border-2 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-circle text-warning me-2 mt-1"></i>
                                <div class="flex-grow-1">
                                    <h6 class="fw-semibold text-dark mb-1">Stock insuficiente</h6>
                                    <div class="text-muted small">
                                        No hay suficiente inventario disponible para completar la venta con la cantidad solicitada.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Solución Sugerida -->
                        <div class="alert alert-info border-info border-2 mb-4">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-lightbulb text-info me-2 mt-1"></i>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-1">Solución recomendada</h6>
                                    <div class="text-muted small">
                                        Reduzca la cantidad solicitada o seleccione un producto alternativo con stock disponible.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer del Modal -->
            <div class="modal-footer border-top-0 bg-light rounded-bottom-3 py-3">
                <div class="w-100 d-flex justify-content-center">
                    <button type="button" class="btn btn-danger px-4 py-2 rounded-2 fw-semibold" data-bs-dismiss="modal">
                        <i class="fas fa-check-circle me-2"></i>
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const contenedor = document.getElementById("productos-container");
    const btnAgregar = document.getElementById("agregar-producto");
    const btnLimpiar = document.getElementById("limpiar-formulario");
    const totalVentaInput = document.getElementById("total_venta");
    const totalVentaLimpioInput = document.getElementById("total_venta_limpio");
    const subtotalVentaInput = document.getElementById("subtotal_venta");
    const descuentoAplicadoInput = document.getElementById("descuento_aplicado");
    const descuentoAplicadoDisplay = document.getElementById("descuento_aplicado_display");
    const formVenta = document.getElementById("formVenta");
    const aplicarDescuentoCheckbox = document.getElementById("aplicar_descuento");
    const porcentajeDescuentoInput = document.getElementById("porcentaje_descuento");
    const valorDescuentoInput = document.getElementById("valor_descuento");

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

    // Calcular totales con descuento
    function actualizarTotales() {
        let subtotal = 0;
        contenedor.querySelectorAll(".producto-item").forEach(item => {
            const cantidad = parseFloat(item.querySelector(".cantidad").value) || 0;
            const precio = parseFloat(item.querySelector(".precio").value) || 0;
            const subtotalProducto = cantidad * precio;
            item.querySelector(".subtotal").value = formatoCOP(subtotalProducto);
            subtotal += subtotalProducto;
        });
        
        subtotalVentaInput.value = formatoCOP(subtotal);
        
        // Calcular descuento si está activo
        let descuento = 0;
        let total = subtotal;
        
        if (aplicarDescuentoCheckbox.checked && porcentajeDescuentoInput.value) {
            const porcentaje = parseFloat(porcentajeDescuentoInput.value) || 0;
            if (porcentaje > 0 && porcentaje <= 100) {
                descuento = (subtotal * porcentaje) / 100;
                total = subtotal - descuento;
            }
        }
        
        descuentoAplicadoInput.value = descuento.toFixed(2);
        descuentoAplicadoDisplay.value = formatoCOP(descuento);
        valorDescuentoInput.value = formatoCOP(descuento);
        totalVentaInput.value = formatoCOP(total);
        totalVentaLimpioInput.value = total.toFixed(2);
    }

    // Habilitar/deshabilitar campo de porcentaje de descuento
    aplicarDescuentoCheckbox.addEventListener("change", function() {
        porcentajeDescuentoInput.disabled = !this.checked;
        if (!this.checked) {
            porcentajeDescuentoInput.value = "";
            valorDescuentoInput.value = "$0,00";
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

    // Función para limpiar el formulario
    function limpiarFormulario() {
        // Confirmar con el usuario
        if (!confirm("¿Está seguro de que desea limpiar el formulario? Se perderán todos los datos ingresados.")) {
            return;
        }
        
        // Limpiar campos del cliente y método de pago
        document.querySelector('select[name="id_cliente"]').selectedIndex = 0;
        document.querySelector('select[name="metodo_pago"]').selectedIndex = 0;
        
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
                item.querySelector(".precio_display").value = "";
                item.querySelector(".subtotal").value = "";
            } else {
                // Eliminar productos adicionales
                item.remove();
            }
        });
        
        // Resetear totales
        subtotalVentaInput.value = "$0,00";
        descuentoAplicadoDisplay.value = "$0,00";
        totalVentaInput.value = "$0,00";
        totalVentaLimpioInput.value = "0";
        descuentoAplicadoInput.value = "0";
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

    // Cambiar cantidad - CON MODAL PROFESIONAL ACTUALIZADO
    contenedor.addEventListener("input", e => {
        if (e.target.classList.contains("cantidad")) {
            const item = e.target.closest(".producto-item");
            const select = item.querySelector(".producto-select");
            const stock = parseInt(select.selectedOptions[0]?.getAttribute("data-stock")) || 0;
            const cantidad = parseInt(e.target.value) || 0;
            
            if (cantidad > stock) {
                e.target.value = stock;
                
                // Mostrar modal profesional en lugar de alert
                const productoNombre = select.selectedOptions[0]?.text.split(' (')[0] || 'Producto';
                const diferencia = cantidad - stock;
                
                // Actualizar información del modal con los nuevos IDs
                document.getElementById('modalProductName').textContent = productoNombre;
                document.getElementById('modalStockDisponible').textContent = stock;
                document.getElementById('modalCantidadSolicitada').textContent = cantidad;
                document.getElementById('modalDiferencia').textContent = `-${diferencia}`;
                
                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('modalStockInsuficiente'));
                modal.show();
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

    // Limpiar formulario
    btnLimpiar.addEventListener("click", limpiarFormulario);

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
        
        // Validar stock - CON MODAL PROFESIONAL ACTUALIZADO
        let stockValido = true;
        let productoConError = null;
        
        contenedor.querySelectorAll(".producto-item").forEach(item => {
            const productoSelect = item.querySelector(".producto-select");
            const cantidadInput = item.querySelector(".cantidad");
            const stock = parseInt(productoSelect.selectedOptions[0]?.getAttribute("data-stock")) || 0;
            const cantidad = parseInt(cantidadInput.value) || 0;
            
            if (productoSelect.value && cantidad > stock) {
                stockValido = false;
                productoConError = {
                    nombre: productoSelect.selectedOptions[0].text.split(' (')[0],
                    stock: stock,
                    cantidad: cantidad
                };
            }
        });
        
        if (!stockValido && productoConError) {
            e.preventDefault();
            
            const diferencia = productoConError.cantidad - productoConError.stock;
            
            // Mostrar modal con información del producto con error (IDs actualizados)
            document.getElementById('modalProductName').textContent = productoConError.nombre;
            document.getElementById('modalStockDisponible').textContent = productoConError.stock;
            document.getElementById('modalCantidadSolicitada').textContent = productoConError.cantidad;
            document.getElementById('modalDiferencia').textContent = `-${diferencia}`;
            
            const modal = new bootstrap.Modal(document.getElementById('modalStockInsuficiente'));
            modal.show();
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
        
        // El formulario se enviará con los valores limpios
    });

    actualizarTotales();
});

// Mostrar modal de error si existe un error en PHP
<?php if (isset($error)): ?>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar el mensaje en el modal
    document.getElementById('modalErrorMessage').textContent = '<?= addslashes($error) ?>';
    
    // Mostrar el modal después de un breve delay para asegurar que el DOM esté listo
    setTimeout(function() {
        const errorModal = new bootstrap.Modal(document.getElementById('modalErrorVenta'));
        errorModal.show();
    }, 100);
});
<?php endif; ?>

// Mostrar modal de éxito si la venta fue exitosa
<?php if (isset($_SESSION['venta_exitosa']) && $_SESSION['venta_exitosa']): ?>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar el mensaje en el modal
    document.getElementById('modalExitoMessage').textContent = '<?= addslashes($_SESSION['mensaje_venta'] ?? 'Venta registrada correctamente') ?>';
    document.getElementById('modalExitoDetalles').textContent = 'Código: <?= $_SESSION['codigo_venta'] ?? '' ?>';
    
    // Mostrar el modal después de un breve delay
    setTimeout(function() {
        const exitoModal = new bootstrap.Modal(document.getElementById('modalExitoVenta'));
        exitoModal.show();
    }, 100);
    
    // Limpiar la sesión después de mostrar
    <?php 
    unset($_SESSION['venta_exitosa']);
    unset($_SESSION['mensaje_venta']);
    unset($_SESSION['codigo_venta']);
    unset($_SESSION['total_venta']);
    ?>
});
<?php endif; ?>
</script>