<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Normalizamos el rol para compatibilidad
if (!isset($_SESSION['rol']) && isset($_SESSION['usuario_rol'])) {
    $_SESSION['rol'] = $_SESSION['usuario_rol'];
}

// app/views/ventas/ventas.php
$ventaController = new VentaController($db);
$ventas = $ventaController->listar();
?>
<div class="container-fluid px-4" style="margin-top:180px;margin-bottom:100px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-cash-register me-2"></i>Gestión de Ventas</h1>
        <div class="btn-toolbar mb-2 mb-md-2">
            <a href="index.php?page=crear_venta" class="btn btn-neon">
                <i class="fas fa-plus me-2"></i>Nueva Venta
            </a>
        </div>
    </div>

    <!-- Contenedor de Filtros -->
    <div class="card py-3 mb-4">
        <div class="card-header text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="filtroCodigo" class="form-label small text-white">Código</label>
                    <input type="text" id="filtroCodigo" class="form-control form-control-sm" 
                        placeholder="Código venta...">
                </div>
                <div class="col-md-2">
                    <label for="filtroFactura" class="form-label small text-white">Factura</label>
                    <input type="text" id="filtroFactura" class="form-control form-control-sm" 
                        placeholder="N° factura...">
                </div>
                <div class="col-md-3">
                    <label for="filtroCliente" class="form-label small text-white">Cliente</label>
                    <input type="text" id="filtroCliente" class="form-control form-control-sm" 
                        placeholder="Nombre cliente...">
                </div>
                <div class="col-md-2">
                    <label for="filtroFecha" class="form-label small text-white">Fecha</label>
                    <input type="text" id="filtroFecha" class="form-control form-control-sm" 
                        placeholder="dd/mm/aaaa...">
                </div>
                <div class="col-md-2">
                    <label for="filtroMetodoPago" class="form-label small text-white">Método Pago</label>
                    <select id="filtroMetodoPago" class="form-select form-select-sm">
                        <option value="">Todos los métodos</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Crédito">Crédito</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filtroEstado" class="form-label small text-white">Estado</label>
                    <select id="filtroEstado" class="form-select form-select-sm">
                        <option value="">Todos los estados</option>
                        <option value="Pagada">Pagada</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Anulada">Anulada</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" id="btnLimpiarFiltros" class="btn btn-danger mt-2">
                        <i class="fas fa-undo me-1"></i>Limpiar filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="historial_ventas" class="card">
        <div class="card-header py-4">
            <h5 class="card-title mb-0 text-white">
                <i class="fas fa-list me-2 text-white"></i>Historial de Ventas
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaVentas">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Método Pago</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventas as $venta): ?>
                        <tr class="fila-venta">
                            <td class="codigo-venta"><?= $venta['codigo_venta'] ?></td>
                            <td class="factura-venta">
                                <span class="badge bg-secondary"><?= $venta['factura'] ?? 'N/A' ?></span>
                            </td>
                            <td class="nombre-cliente"><?= $venta['nombre_cliente'] ?: 'Cliente General' ?></td>
                            <td class="fecha-venta"><?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></td>
                            <td class="total-venta">$<?= number_format($venta['total_venta'], 2, ',', '.') ?></td>
                            <td class="metodo-pago">
                                <span class="badge bg-info"><?= $venta['metodo_pago'] ?></span>
                            </td>
                            <td class="estado-venta">
                                <span class="badge bg-<?= 
                                    $venta['estado'] == 'Pagada' ? 'success' : 
                                    ($venta['estado'] == 'Pendiente' ? 'warning' : 'danger') 
                                ?>">
                                    <?= $venta['estado'] ?>
                                </span>
                            </td>
                            <td class="acciones-venta">
                                <div class="btn-group btn-group-sm">
                                    <!-- Ver detalle -->
                                    <a href="index.php?page=detalle_venta&id=<?= $venta['id_venta'] ?>" 
                                    class="btn btn-info" title="Ver Detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Marcar como pagada -->
                                    <?php if ($venta['estado'] == 'Pendiente'): ?>
                                        <button type="button" 
                                                class="btn btn-success btnMarcarPagada" 
                                                data-id="<?= $venta['id_venta'] ?>"
                                                data-codigo="<?= $venta['codigo_venta'] ?>"
                                                data-factura="<?= $venta['factura'] ?? 'N/A' ?>"
                                                data-cliente="<?= $venta['nombre_cliente'] ?: 'Cliente General' ?>"
                                                data-total="$<?= number_format($venta['total_venta'], 2, ',', '.') ?>"
                                                title="Marcar como Pagada">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>

                                    <!-- Anular venta -->
                                    <?php if ($venta['estado'] != 'Anulada'): ?>
                                        <button type="button" 
                                                class="btn btn-danger btnAnularVenta" 
                                                data-id="<?= $venta['id_venta'] ?>"
                                                data-codigo="<?= $venta['codigo_venta'] ?>"
                                                data-factura="<?= $venta['factura'] ?? 'N/A' ?>"
                                                data-cliente="<?= $venta['nombre_cliente'] ?: 'Cliente General' ?>"
                                                data-total="$<?= number_format($venta['total_venta'], 2, ',', '.') ?>"
                                                title="Anular Venta">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    <?php endif; ?>

                                    <!-- Revertir anulación -->
                                    <?php if (
                                        $venta['estado'] == 'Anulada' && 
                                        isset($_SESSION['rol']) && 
                                        stripos(trim($_SESSION['rol']), 'admin') !== false
                                    ): ?>
                                        <button type="button" 
                                                class="btn btn-warning btnRevertirAnulacion" 
                                                data-id="<?= $venta['id_venta'] ?>"
                                                data-codigo="<?= $venta['codigo_venta'] ?>"
                                                data-factura="<?= $venta['factura'] ?? 'N/A' ?>"
                                                data-cliente="<?= $venta['nombre_cliente'] ?: 'Cliente General' ?>"
                                                data-total="$<?= number_format($venta['total_venta'], 2, ',', '.') ?>"
                                                title="Revertir Anulación">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Mensaje cuando no hay resultados -->
                <div id="mensajeNoResultados" class="bg-white rounded p-4 text-center mt-4 d-none shadow">
                    <div class="mb-3">
                        <i class="fas fa-search fa-2x text-secondary"></i>
                    </div>
                    <h4 class="text-dark">No hay coincidencias de búsqueda</h4>
                    <p class="text-dark">Intenta ajustar los filtros para ver más resultados.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA MARCAR VENTA COMO PAGADA -->
<div class="modal fade" id="modalMarcarPagada" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Marcar Venta como Pagada
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center text-dark">
                <p>¿Está seguro que desea marcar la siguiente venta como <strong>PAGADA</strong>?</p>
                <div class="alert alert-info">
                    <p id="detalleVentaPagada" class="mb-0 fw-bold"></p>
                </div>
                <p class="text-muted small">Esta acción cambiará el estado de la venta a "Pagada".</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <a href="#" id="btnConfirmarPago" class="btn btn-success">
                    <i class="fas fa-check me-1"></i>Confirmar Pago
                </a>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA ANULAR VENTA -->
<div class="modal fade" id="modalAnularVenta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-ban me-2"></i>Anular Venta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-dark text-center">
                <p>¿Está seguro que desea <strong>ANULAR</strong> la siguiente venta?</p>
                <div class="alert alert-warning">
                    <p id="detalleVentaAnular" class="mb-0 fw-bold"></p>
                </div>
                <p class="text-danger small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Esta acción se puede revertir, por el momento afectará el inventario.
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <a href="#" id="btnConfirmarAnularVenta" class="btn btn-danger">
                    <i class="fas fa-ban me-1"></i>Confirmar Anulación
                </a>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA REVERTIR ANULACIÓN -->
<div class="modal fade" id="modalRevertirAnulacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-undo me-2"></i>Revertir Anulación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-dark text-center">
                <p>¿Está seguro que desea <strong>REVERTIR LA ANULACIÓN</strong> de la siguiente venta?</p>
                <div class="alert alert-info">
                    <p id="detalleVentaRevertir" class="mb-0 fw-bold"></p>
                </div>
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta acción restaurará la venta y actualizará el inventario.
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <a href="#" id="btnConfirmarRevertir" class="btn btn-warning">
                    <i class="fas fa-undo me-1"></i>Revertir Anulación
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar que Bootstrap esté disponible
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap no está cargado correctamente');
        return;
    }

    const filtroCodigo = document.getElementById('filtroCodigo');
    const filtroFactura = document.getElementById('filtroFactura');
    const filtroCliente = document.getElementById('filtroCliente');
    const filtroFecha = document.getElementById('filtroFecha');
    const filtroMetodoPago = document.getElementById('filtroMetodoPago');
    const filtroEstado = document.getElementById('filtroEstado');
    const btnLimpiar = document.getElementById('btnLimpiarFiltros');
    const btnLimpiarDesdeMensaje = document.getElementById('btnLimpiarDesdeMensaje');
    const filasVentas = document.querySelectorAll('.fila-venta');
    const mensajeNoResultados = document.getElementById('mensajeNoResultados');
    const tablaVentas = document.querySelector('#tablaVentas tbody');
    
    // Array con todos los filtros
    const filtros = [filtroCodigo, filtroFactura, filtroCliente, filtroFecha, filtroMetodoPago, filtroEstado];
    
    // Función para mostrar/ocultar mensaje de no resultados
    function actualizarMensajeNoResultados() {
        const filasVisibles = Array.from(filasVentas).filter(fila => fila.style.display !== 'none');
        
        if (filasVisibles.length === 0) {
            // No hay resultados
            mensajeNoResultados.classList.remove('d-none');
            tablaVentas.style.display = 'none';
        } else {
            // Hay resultados
            mensajeNoResultados.classList.add('d-none');
            tablaVentas.style.display = '';
        }
    }
    
    // Función para filtrar las ventas
    function filtrarVentas() {
        const valoresFiltros = {
            codigo: filtroCodigo.value.toLowerCase().trim(),
            factura: filtroFactura.value.toLowerCase().trim(),
            cliente: filtroCliente.value.toLowerCase().trim(),
            fecha: filtroFecha.value.toLowerCase().trim(),
            metodoPago: filtroMetodoPago.value,
            estado: filtroEstado.value
        };
        
        let hayResultados = false;
        
        filasVentas.forEach(fila => {
            const codigo = fila.querySelector('.codigo-venta').textContent.toLowerCase();
            const factura = fila.querySelector('.factura-venta').textContent.toLowerCase();
            const cliente = fila.querySelector('.nombre-cliente').textContent.toLowerCase();
            const fecha = fila.querySelector('.fecha-venta').textContent.toLowerCase();
            const metodoPago = fila.querySelector('.metodo-pago').textContent.trim();
            const estado = fila.querySelector('.estado-venta').textContent.trim();
            
            // Verificar si la fila coincide con todos los filtros activos
            const coincide = 
                (!valoresFiltros.codigo || codigo.includes(valoresFiltros.codigo)) &&
                (!valoresFiltros.factura || factura.includes(valoresFiltros.factura)) &&
                (!valoresFiltros.cliente || cliente.includes(valoresFiltros.cliente)) &&
                (!valoresFiltros.fecha || fecha.includes(valoresFiltros.fecha)) &&
                (!valoresFiltros.metodoPago || metodoPago === valoresFiltros.metodoPago) &&
                (!valoresFiltros.estado || estado === valoresFiltros.estado);
            
            // Mostrar u ocultar la fila según si coincide
            fila.style.display = coincide ? '' : 'none';
            
            if (coincide) {
                hayResultados = true;
            }
        });
        
        // Actualizar mensaje de no resultados
        actualizarMensajeNoResultados();
    }
    
    // Función para limpiar filtros
    function limpiarFiltros() {
        filtroCodigo.value = '';
        filtroFactura.value = '';
        filtroCliente.value = '';
        filtroFecha.value = '';
        filtroMetodoPago.value = '';
        filtroEstado.value = '';
        filtrarVentas();
        filtroCodigo.focus();
    }
    
    // Eventos para todos los filtros
    filtros.forEach(filtro => {
        filtro.addEventListener('input', filtrarVentas);
        filtro.addEventListener('change', filtrarVentas);
    });
    
    // Evento para el botón limpiar filtros
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', limpiarFiltros);
    }
    
    // Evento para el botón limpiar desde el mensaje
    if (btnLimpiarDesdeMensaje) {
        btnLimpiarDesdeMensaje.addEventListener('click', limpiarFiltros);
    }

    // Configurar modales de ventas
    const btnMarcarPagada = document.querySelectorAll('.btnMarcarPagada');
    const btnAnularVenta = document.querySelectorAll('.btnAnularVenta');
    const btnRevertirAnulacion = document.querySelectorAll('.btnRevertirAnulacion');
    
    // Modal Marcar como Pagada
    btnMarcarPagada.forEach(boton => {
        boton.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const codigo = this.getAttribute('data-codigo');
            const factura = this.getAttribute('data-factura');
            const cliente = this.getAttribute('data-cliente');
            const total = this.getAttribute('data-total');
            
            console.log('Abriendo modal de pago para venta:', codigo);
            
            // Actualizar detalles en el modal
            document.getElementById('detalleVentaPagada').textContent = 
                `Código: ${codigo} - Factura: ${factura} - Cliente: ${cliente} - Total: ${total}`;
            
            // Actualizar enlace de confirmación
            document.getElementById('btnConfirmarPago').href = 
                `index.php?page=marcar_venta_pagada&id=${id}`;
            
            const modalElement = document.getElementById('modalMarcarPagada');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                console.error('Modal modalMarcarPagada no encontrado');
            }
        });
    });
    
    // Modal Anular Venta
    btnAnularVenta.forEach(boton => {
        boton.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const codigo = this.getAttribute('data-codigo');
            const factura = this.getAttribute('data-factura');
            const cliente = this.getAttribute('data-cliente');
            const total = this.getAttribute('data-total');
            
            console.log('Abriendo modal de anulación para venta:', codigo);
            
            // Actualizar detalles en el modal
            document.getElementById('detalleVentaAnular').textContent = 
                `Código: ${codigo} - Factura: ${factura} - Cliente: ${cliente} - Total: ${total}`;
            
            // Actualizar enlace de confirmación
            document.getElementById('btnConfirmarAnularVenta').href = 
                `index.php?page=anular_venta&id=${id}`;
            
            const modalElement = document.getElementById('modalAnularVenta');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                console.error('Modal modalAnularVenta no encontrado');
            }
        });
    });
    
    // Modal Revertir Anulación
    btnRevertirAnulacion.forEach(boton => {
        boton.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const codigo = this.getAttribute('data-codigo');
            const factura = this.getAttribute('data-factura');
            const cliente = this.getAttribute('data-cliente');
            const total = this.getAttribute('data-total');
            
            console.log('Abriendo modal de revertir anulación para venta:', codigo);
            
            // Actualizar detalles en el modal
            document.getElementById('detalleVentaRevertir').textContent = 
                `Código: ${codigo} - Factura: ${factura} - Cliente: ${cliente} - Total: ${total}`;
            
            // Actualizar enlace de confirmación
            document.getElementById('btnConfirmarRevertir').href = 
                `index.php?page=revertir_anulacion&codigo=${encodeURIComponent(codigo)}`;
            
            const modalElement = document.getElementById('modalRevertirAnulacion');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                console.error('Modal modalRevertirAnulacion no encontrado');
            }
        });
    });
    
    // Inicializar mensaje de no resultados
    actualizarMensajeNoResultados();
});
</script>