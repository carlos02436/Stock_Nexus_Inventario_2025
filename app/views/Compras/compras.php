<?php
// app/views/compras/compras.php
$compraController = new CompraController($db);
$compras = $compraController->listar();

// Obtener proveedores para el select - CORREGIDO: sin filtro de estado
$proveedores = [];
try {
    $stmt = $db->query("SELECT id_proveedor, nombre_proveedor FROM proveedores ORDER BY nombre_proveedor");
    $proveedores = $stmt->fetchAll();
    
    // Debug: verificar si se obtuvieron proveedores
    error_log("Proveedores encontrados: " . count($proveedores));
} catch (PDOException $e) {
    error_log("Error al obtener proveedores: " . $e->getMessage());
}

$estados = ['Todos' => '', 'Pagada' => 'Pagada', 'Pendiente' => 'Pendiente', 'Anulada' => 'Anulada'];
?>
<div class="container-fluid px-4" style="margin-top: 180px; margin-bottom: 50px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom border-light">
        <h1 class="h2 text-white"><i class="fas fa-shopping-cart me-2"></i>Gestión de Compras</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=crear_compra" class="btn btn-neon">
                <i class="fas fa-plus me-2"></i>Nueva Compra
            </a>
        </div>
    </div>

    <!-- Contenedor separado para filtros -->
    <div class="card shadow-sm mb-4 py-3">
        <div class="card-header text-white">
            <h6 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-white">Código de Compra</label>
                    <input type="text" id="filtro-codigo" class="form-control form-control-sm" placeholder="Buscar por código..." autocomplete="off">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-white">Proveedor</label>
                    <select id="filtro-proveedor" class="form-control form-control-sm">
                        <option value="">Todos los proveedores</option>
                        <?php foreach ($proveedores as $proveedor): ?>
                            <option value="<?= $proveedor['id_proveedor'] ?>"><?= htmlspecialchars($proveedor['nombre_proveedor']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-white">Fecha</label>
                    <input type="date" id="filtro-fecha" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-white">Estado</label>
                    <select id="filtro-estado" class="form-control form-control-sm">
                        <?php foreach ($estados as $texto => $valor): ?>
                            <option value="<?= $valor ?>"><?= $texto ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button id="btn-limpiar" class="btn btn-danger btn-sm w-90" title="Limpiar todos los filtros">
                        <i class="fas fa-undo me-1"></i>Limpiar filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor de la tabla -->
    <div class="card shadow-sm">
        <div class="card-header text-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Historial de Compras
            </h5>
        </div>
        <div id="historial_compras" class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tabla-compras">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Factura</th>
                            <th>Proveedor</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($compras as $compra): ?>
                        <tr data-codigo="<?= $compra['codigo_compra'] ?>" 
                            data-proveedor="<?= $compra['id_proveedor'] ?>" 
                            data-fecha="<?= date('Y-m-d', strtotime($compra['fecha_compra'])) ?>" 
                            data-estado="<?= $compra['estado'] ?>">
                            <td><?= $compra['codigo_compra'] ?></td>
                            <td><?= $compra['factura'] ?? 'N/A' ?></td>
                            <td><?= $compra['nombre_proveedor'] ?></td>
                            <td><?= date('d/m/Y', strtotime($compra['fecha_compra'])) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $compra['estado'] == 'Pagada' ? 'success' : 
                                    ($compra['estado'] == 'Pendiente' ? 'warning' : 
                                    ($compra['estado'] == 'Anulada' ? 'danger' : 'secondary')) 
                                ?>">
                                    <?= $compra['estado'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=detalle_compra&id=<?= $compra['id_compra'] ?>" 
                                       class="btn btn-info" title="Ver Detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($compra['estado'] == 'Pendiente'): ?>
                                    <button type="button" 
                                            class="btn btn-success btnMarcarCompraPagada" 
                                            data-id="<?= $compra['id_compra'] ?>"
                                            data-codigo="<?= $compra['codigo_compra'] ?>"
                                            data-proveedor="<?= $compra['nombre_proveedor'] ?>"
                                            title="Marcar como Pagada">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($compra['estado'] != 'Anulada'): ?>
                                    <button type="button" 
                                            class="btn btn-danger btnAnularCompra" 
                                            data-id="<?= $compra['id_compra'] ?>"
                                            data-codigo="<?= $compra['codigo_compra'] ?>"
                                            data-proveedor="<?= $compra['nombre_proveedor'] ?>"
                                            title="Anular Compra">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    <?php else: ?>
                                    <button type="button" 
                                            class="btn btn-success btnReanudarCompra" 
                                            data-id="<?= $compra['id_compra'] ?>"
                                            data-codigo="<?= $compra['codigo_compra'] ?>"
                                            data-proveedor="<?= $compra['nombre_proveedor'] ?>"
                                            title="Reanudar Compra">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($compras)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle me-2"></i>No hay compras registradas
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA MARCAR COMPRA COMO PAGADA -->
<div class="modal fade" id="modalMarcarCompraPagada" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Marcar Compra como Pagada
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center text-dark">
                <p>¿Está seguro que desea marcar la siguiente compra como <strong>PAGADA</strong>?</p>
                <div class="alert alert-info">
                    <p id="detalleCompraPagada" class="mb-0 fw-bold">Cargando información...</p>
                </div>
                <p class="text-muted small">Esta acción cambiará el estado de la compra a "Pagada".</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <a href="#" id="btnConfirmarCompraPagada" class="btn btn-success">
                    <i class="fas fa-check me-1"></i>Confirmar Pago
                </a>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA ANULAR COMPRA -->
<div class="modal fade" id="modalAnularCompra" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-ban me-2"></i>Anular Compra
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-dark text-center">
                <p>¿Está seguro que desea <strong>ANULAR</strong> la siguiente compra?</p>
                <div class="alert alert-warning">
                    <p id="detalleCompraAnular" class="mb-0 fw-bold">Cargando información...</p>
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
                <a href="#" id="btnConfirmarAnularCompra" class="btn btn-danger">
                    <i class="fas fa-ban me-1"></i>Confirmar Anulación
                </a>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA REANUDAR COMPRA -->
<div class="modal fade" id="modalReanudarCompra" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-redo me-2"></i>Reanudar Compra
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-dark text-center">
                <p>¿Está seguro que desea <strong>REANUDAR</strong> la siguiente compra anulada?</p>
                <div class="alert alert-info">
                    <p id="detalleCompraReanudar" class="mb-0 fw-bold">Cargando información...</p>
                </div>
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta acción restaurará la compra y actualizará el inventario.
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <a href="#" id="btnConfirmarReanudarCompra" class="btn btn-warning">
                    <i class="fas fa-redo me-1"></i>Reanudar Compra
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filtroCodigo = document.getElementById('filtro-codigo');
    const filtroProveedor = document.getElementById('filtro-proveedor');
    const filtroFecha = document.getElementById('filtro-fecha');
    const filtroEstado = document.getElementById('filtro-estado');
    const btnLimpiar = document.getElementById('btn-limpiar');
    const filas = document.querySelectorAll('#tabla-compras tbody tr');

    // Función para filtrar las compras
    function filtrarCompras() {
        const codigo = filtroCodigo.value.toLowerCase();
        const proveedor = filtroProveedor.value;
        const fecha = filtroFecha.value; // Ya está en formato YYYY-MM-DD
        const estado = filtroEstado.value;

        let resultadosVisibles = 0;

        filas.forEach(fila => {
            const codigoFila = fila.getAttribute('data-codigo').toLowerCase();
            const proveedorFila = fila.getAttribute('data-proveedor');
            const fechaFila = fila.getAttribute('data-fecha'); // Ya está en formato YYYY-MM-DD
            const estadoFila = fila.getAttribute('data-estado');

            const coincideCodigo = codigoFila.includes(codigo);
            const coincideProveedor = !proveedor || proveedorFila == proveedor;
            const coincideFecha = !fecha || fechaFila === fecha;
            const coincideEstado = !estado || estadoFila === estado;

            if (coincideCodigo && coincideProveedor && coincideFecha && coincideEstado) {
                fila.style.display = '';
                resultadosVisibles++;
            } else {
                fila.style.display = 'none';
            }
        });

        // Mostrar mensaje si no hay resultados
        const mensajeNoResultados = document.getElementById('mensaje-no-resultados');
        const tbody = document.querySelector('#tabla-compras tbody');
        
        if (resultadosVisibles === 0) {
            if (!mensajeNoResultados) {
                const filaMensaje = document.createElement('tr');
                filaMensaje.id = 'mensaje-no-resultados';
                filaMensaje.innerHTML = `
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-search me-2"></i>No se encontraron compras con los filtros aplicados
                    </td>
                `;
                tbody.appendChild(filaMensaje);
            }
        } else if (mensajeNoResultados) {
            mensajeNoResultados.remove();
        }
    }

    // Event listeners para filtros automáticos
    filtroCodigo.addEventListener('input', filtrarCompras);
    filtroProveedor.addEventListener('change', filtrarCompras);
    filtroFecha.addEventListener('change', filtrarCompras);
    filtroEstado.addEventListener('change', filtrarCompras);

    // Botón limpiar
    btnLimpiar.addEventListener('click', function() {
        filtroCodigo.value = '';
        filtroProveedor.value = '';
        filtroFecha.value = '';
        filtroEstado.value = '';
        filtrarCompras();
        
        // Enfocar el primer campo después de limpiar
        filtroCodigo.focus();
    });

    // Permitir limpiar con Escape en el campo de código
    filtroCodigo.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            filtroCodigo.value = '';
            filtrarCompras();
        }
    });

    // Configurar modales de compras
    const btnMarcarCompraPagada = document.querySelectorAll('.btnMarcarCompraPagada');
    const btnAnularCompra = document.querySelectorAll('.btnAnularCompra');
    const btnReanudarCompra = document.querySelectorAll('.btnReanudarCompra');
    
    // Modal Marcar Compra como Pagada
    btnMarcarCompraPagada.forEach(boton => {
        boton.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const codigo = this.getAttribute('data-codigo');
            const proveedor = this.getAttribute('data-proveedor');
            
            // Actualizar detalles en el modal
            document.getElementById('detalleCompraPagada').textContent = 
                `Código: ${codigo} - Proveedor: ${proveedor}`;
            
            // Actualizar enlace de confirmación
            document.getElementById('btnConfirmarCompraPagada').href = 
                `index.php?page=marcar_compra_pagada&id=${id}`;
            
            const modal = new bootstrap.Modal(document.getElementById('modalMarcarCompraPagada'));
            modal.show();
        });
    });
    
    // Modal Anular Compra
    btnAnularCompra.forEach(boton => {
        boton.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const codigo = this.getAttribute('data-codigo');
            const proveedor = this.getAttribute('data-proveedor');
            
            // Actualizar detalles en el modal
            document.getElementById('detalleCompraAnular').textContent = 
                `Código: ${codigo} - Proveedor: ${proveedor}`;
            
            // Actualizar enlace de confirmación
            document.getElementById('btnConfirmarAnularCompra').href = 
                `index.php?page=anular_compra&id=${id}`;
            
            const modal = new bootstrap.Modal(document.getElementById('modalAnularCompra'));
            modal.show();
        });
    });
    
    // Modal Reanudar Compra
    btnReanudarCompra.forEach(boton => {
        boton.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const codigo = this.getAttribute('data-codigo');
            const proveedor = this.getAttribute('data-proveedor');
            
            // Actualizar detalles en el modal
            document.getElementById('detalleCompraReanudar').textContent = 
                `Código: ${codigo} - Proveedor: ${proveedor}`;
            
            // Actualizar enlace de confirmación
            document.getElementById('btnConfirmarReanudarCompra').href = 
                `index.php?page=reanudar_compra&id=${id}`;
            
            const modal = new bootstrap.Modal(document.getElementById('modalReanudarCompra'));
            modal.show();
        });
    });

    // Filtrar inicialmente para asegurar que todo funcione
    filtrarCompras();
});
</script>