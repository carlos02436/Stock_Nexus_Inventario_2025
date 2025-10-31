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
            </div>
            <div class="row mt-3">
                <div class="col-md-12 text-start">
                    <button id="btnLimpiarFiltros" class="btn btn-danger btn-sm">
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
                                        <a href="index.php?page=marcar_venta_pagada&id=<?= $venta['id_venta'] ?>" 
                                        class="btn btn-success" title="Marcar como Pagada">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php endif; ?>

                                    <!-- Anular venta (todos los roles pueden hacerlo) -->
                                    <?php if ($venta['estado'] != 'Anulada'): ?>
                                        <a href="index.php?page=anular_venta&id=<?= $venta['id_venta'] ?>" 
                                        class="btn btn-danger" title="Anular Venta">
                                            <i class="fas fa-ban"></i>
                                        </a>
                                    <?php endif; ?>

                                    <!-- Revertir anulación (solo administrador) -->
                                    <?php if (
                                        $venta['estado'] == 'Anulada' && 
                                        isset($_SESSION['rol']) && 
                                        stripos(trim($_SESSION['rol']), 'admin') !== false
                                    ): ?>
                                        <a href="index.php?page=revertir_anulacion&codigo=<?= urlencode($venta['codigo_venta']) ?>" 
                                        class="btn btn-warning" title="Revertir Anulación">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filtroCodigo = document.getElementById('filtroCodigo');
    const filtroCliente = document.getElementById('filtroCliente');
    const filtroFecha = document.getElementById('filtroFecha');
    const filtroMetodoPago = document.getElementById('filtroMetodoPago');
    const filtroEstado = document.getElementById('filtroEstado');
    const btnLimpiar = document.getElementById('btnLimpiarFiltros');
    const filasVentas = document.querySelectorAll('.fila-venta');
    
    // Array con todos los filtros
    const filtros = [filtroCodigo, filtroCliente, filtroFecha, filtroMetodoPago, filtroEstado];
    
    // Función para filtrar las ventas
    function filtrarVentas() {
        const valoresFiltros = {
            codigo: filtroCodigo.value.toLowerCase().trim(),
            cliente: filtroCliente.value.toLowerCase().trim(),
            fecha: filtroFecha.value.toLowerCase().trim(),
            metodoPago: filtroMetodoPago.value,
            estado: filtroEstado.value
        };
        
        filasVentas.forEach(fila => {
            const codigo = fila.querySelector('.codigo-venta').textContent.toLowerCase();
            const cliente = fila.querySelector('.nombre-cliente').textContent.toLowerCase();
            const fecha = fila.querySelector('.fecha-venta').textContent.toLowerCase();
            const metodoPago = fila.querySelector('.metodo-pago').textContent.trim();
            const estado = fila.querySelector('.estado-venta').textContent.trim();
            
            // Verificar si la fila coincide con todos los filtros activos
            const coincide = 
                (!valoresFiltros.codigo || codigo.includes(valoresFiltros.codigo)) &&
                (!valoresFiltros.cliente || cliente.includes(valoresFiltros.cliente)) &&
                (!valoresFiltros.fecha || fecha.includes(valoresFiltros.fecha)) &&
                (!valoresFiltros.metodoPago || metodoPago === valoresFiltros.metodoPago) &&
                (!valoresFiltros.estado || estado === valoresFiltros.estado);
            
            // Mostrar u ocultar la fila según si coincide
            fila.style.display = coincide ? '' : 'none';
        });
    }
    
    // Eventos para todos los filtros
    filtros.forEach(filtro => {
        filtro.addEventListener('input', filtrarVentas);
        filtro.addEventListener('change', filtrarVentas);
    });
    
    // Evento para el botón limpiar filtros
    btnLimpiar.addEventListener('click', function() {
        filtroCodigo.value = '';
        filtroCliente.value = '';
        filtroFecha.value = '';
        filtroMetodoPago.value = '';
        filtroEstado.value = '';
        filtrarVentas();
        filtroCodigo.focus();
    });
});
</script>