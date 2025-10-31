<?php
// app/views/finanzas/gastos.php
require_once __DIR__ . '/../../helpers/PermisoHelper.php';
?>
<div class="container-fluid px-4" style="margin-top: 180px; margin-bottom: 100px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2"><i class="fas fa-money-bill-wave me-2"></i>Gastos Operativos</h1>
        <div>
            <?php if (PermisoHelper::puede('Finanzas', 'crear')): ?>
                <a href="index.php?page=finanzas" class="btn btn-secondary rounded-3 py-2 me-2">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Finanzas
                </a>
                <a href="index.php?page=crear_gasto" class="btn btn-neon">
                    <i class="fas fa-plus me-2"></i>Nuevo Gasto
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['tipo_mensaje'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show mx-2">
            <?= $_SESSION['mensaje'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
    <?php endif; ?>

    <!-- Filtros de Búsqueda -->
    <div class="card mx-2 mb-4">
        <div class="card-header py-3 text-white">
            <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="filtro-fecha" class="form-label text-white">Fecha</label>
                    <input type="date" class="form-control" id="filtro-fecha" placeholder="Filtrar por fecha...">
                </div>
                <div class="col-md-4">
                    <label for="filtro-categoria" class="form-label text-white">Categoría/Descripción</label>
                    <input type="text" class="form-control" id="filtro-categoria" placeholder="Buscar en categoría o descripción...">
                </div>
                <div class="col-md-4">
                    <label for="filtro-estado" class="form-label text-white">Estado</label>
                    <select class="form-control" id="filtro-estado">
                        <option value="">Todos los estados</option>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-start">
                    <button type="button" class="btn btn-danger" id="btn-limpiar-filtros">
                        <i class="fas fa-undo me-1"></i>Limpiar filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mx-2">
        <div class="card-body">
            <?php if (empty($gastos)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-3x text-white mb-3"></i>
                    <h4 class="text-white">No hay gastos operativos registrados</h4>
                    <p class="text-white">Comienza agregando tu primer gasto operativo.</p>
                    <?php if (PermisoHelper::puede('Finanzas', 'crear')): ?>
                        <a href="index.php?page=crear_gasto" class="btn btn-neon">
                            <i class="fas fa-plus me-2"></i>Crear Primer Gasto
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tabla-gastos">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Categoría</th>
                                <th>Descripción</th>
                                <th>Valor</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Calcular total solo de gastos activos
                            $totalActivos = 0;
                            foreach ($gastos as $gasto) {
                                if ($gasto['estado'] == 'Activo') {
                                    $totalActivos += $gasto['valor'];
                                }
                            }
                            ?>
                            <?php foreach ($gastos as $gasto): ?>
                                <tr>
                                    <td data-fecha="<?= $gasto['fecha'] ?>"><?= date('d/m/Y', strtotime($gasto['fecha'])) ?></td>
                                    <td>
                                        <span class="badge bg-info"><?= htmlspecialchars($gasto['categoria'] ?? 'Sin categoría') ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($gasto['descripcion'] ?? '') ?></td>
                                    <td class="text-end" data-valor="<?= $gasto['valor'] ?>">
                                        <strong>$<?= number_format($gasto['valor'], 2, ',', '.') ?></strong>
                                    </td>
                                    <td data-estado="<?= $gasto['estado'] ?>">
                                        <span class="badge bg-<?= $gasto['estado'] == 'Activo' ? 'success' : 'secondary' ?>">
                                            <?= $gasto['estado'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <?php if (PermisoHelper::puede('Finanzas', 'editar')): ?>
                                                <a href="index.php?page=editar_gasto&id=<?= $gasto['id_gasto'] ?>" 
                                                   class="btn btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if (PermisoHelper::puede('Finanzas', 'eliminar')): ?>
                                                <?php if ($gasto['estado'] == 'Activo'): ?>
                                                    <button type="button" 
                                                            class="btn btn-danger" 
                                                            title="Inactivar"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalInactivarGasto"
                                                            data-gasto-id="<?= $gasto['id_gasto'] ?>"
                                                            data-gasto-descripcion="<?= htmlspecialchars($gasto['descripcion'] ?? 'Sin descripción') ?>">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" 
                                                            class="btn btn-success" 
                                                            title="Activar"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalActivarGasto"
                                                            data-gasto-id="<?= $gasto['id_gasto'] ?>"
                                                            data-gasto-descripcion="<?= htmlspecialchars($gasto['descripcion'] ?? 'Sin descripción') ?>">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="3" class="text-end"><strong>Total Gastos Activos:</strong></td>
                                <td class="text-end" id="total-gastos">
                                    <strong>$<?= number_format($totalActivos, 2, ',', '.') ?></strong>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Mensaje cuando no hay resultados -->
                <div id="mensajeNoResultados" class="alert alert-warning text-center d-none mt-3">
                    <i class="fas fa-search fa-2x mb-2"></i>
                    <h5 class="mb-2">No se encontraron resultados</h5>
                    <p class="mb-0">No hay gastos que coincidan con los filtros aplicados.</p>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="btn-limpiar-filtros-mensaje">
                        <i class="fas fa-undo me-1"></i>Limpiar filtros
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Inactivar Gasto -->
<div class="modal fade" id="modalInactivarGasto" tabindex="-1" aria-labelledby="modalInactivarGastoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalInactivarGastoLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Inactivación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas <strong>inactivar</strong> el siguiente gasto?</p>
                <div class="alert alert-info">
                    <strong>Descripción:</strong> <span id="gasto-descripcion-inactivar"></span>
                </div>
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    El gasto se marcará como inactivo y ya no aparecerá en los listados principales, 
                    pero la información se mantendrá en el sistema para fines de auditoría.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="btnConfirmarInactivar">
                    <i class="fas fa-ban me-2"></i>Inactivar Gasto
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Activar Gasto -->
<div class="modal fade" id="modalActivarGasto" tabindex="-1" aria-labelledby="modalActivarGastoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalActivarGastoLabel">
                    <i class="fas fa-check-circle me-2"></i>Confirmar Activación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas <strong>activar</strong> el siguiente gasto?</p>
                <div class="alert alert-info">
                    <strong>Descripción:</strong> <span id="gasto-descripcion-activar"></span>
                </div>
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    El gasto se marcará como activo y volverá a aparecer en los listados principales.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnConfirmarActivar">
                    <i class="fas fa-check me-2"></i>Activar Gasto
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Configurar el modal para inactivar gastos
document.addEventListener('DOMContentLoaded', function() {
    // Modal Inactivar
    const modalInactivarGasto = document.getElementById('modalInactivarGasto');
    const gastoDescripcionInactivar = document.getElementById('gasto-descripcion-inactivar');
    const btnConfirmarInactivar = document.getElementById('btnConfirmarInactivar');
    
    // Modal Activar
    const modalActivarGasto = document.getElementById('modalActivarGasto');
    const gastoDescripcionActivar = document.getElementById('gasto-descripcion-activar');
    const btnConfirmarActivar = document.getElementById('btnConfirmarActivar');
    
    let gastoIdActual = null;
    
    // Cuando se abre el modal de inactivar
    modalInactivarGasto.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        gastoIdActual = button.getAttribute('data-gasto-id');
        const descripcion = button.getAttribute('data-gasto-descripcion');
        
        gastoDescripcionInactivar.textContent = descripcion;
    });
    
    // Cuando se abre el modal de activar
    modalActivarGasto.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        gastoIdActual = button.getAttribute('data-gasto-id');
        const descripcion = button.getAttribute('data-gasto-descripcion');
        
        gastoDescripcionActivar.textContent = descripcion;
    });
    
    // Confirmar inactivación
    btnConfirmarInactivar.addEventListener('click', function() {
        if (gastoIdActual) {
            window.location.href = 'index.php?page=inactivar_gasto&id=' + gastoIdActual;
        }
    });
    
    // Confirmar activación
    btnConfirmarActivar.addEventListener('click', function() {
        if (gastoIdActual) {
            window.location.href = 'index.php?page=activar_gasto&id=' + gastoIdActual;
        }
    });
    
    // Limpiar cuando se cierran los modales
    modalInactivarGasto.addEventListener('hidden.bs.modal', function () {
        gastoIdActual = null;
        gastoDescripcionInactivar.textContent = '';
    });
    
    modalActivarGasto.addEventListener('hidden.bs.modal', function () {
        gastoIdActual = null;
        gastoDescripcionActivar.textContent = '';
    });
    
    // Filtros de búsqueda
    const filtroFecha = document.getElementById('filtro-fecha');
    const filtroCategoria = document.getElementById('filtro-categoria');
    const filtroEstado = document.getElementById('filtro-estado');
    const btnLimpiarFiltros = document.getElementById('btn-limpiar-filtros');
    const btnLimpiarFiltrosMensaje = document.getElementById('btn-limpiar-filtros-mensaje');
    const tabla = document.getElementById('tabla-gastos');
    const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    const mensajeNoResultados = document.getElementById('mensajeNoResultados');
    const tfoot = tabla.getElementsByTagName('tfoot')[0];
    const totalGastosElement = document.getElementById('total-gastos');
    
    // Función para formatear números en formato pesos colombianos con 2 decimales
    function formatearPesosColombianos(valor) {
        return '$' + valor.toLocaleString('es-CO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    
    // Función para aplicar filtros
    function aplicarFiltros() {
        const filtroFechaVal = filtroFecha.value;
        const filtroCategoriaVal = filtroCategoria.value.toLowerCase();
        const filtroEstadoVal = filtroEstado.value;
        
        let filasVisibles = 0;
        let totalFiltrado = 0;
        
        for (let i = 0; i < filas.length; i++) {
            const celdas = filas[i].getElementsByTagName('td');
            
            // Verificar si la fila tiene suficientes celdas
            if (celdas.length < 6) {
                filas[i].style.display = 'none';
                continue;
            }
            
            const fechaOriginal = celdas[0].getAttribute('data-fecha'); // Fecha en formato YYYY-MM-DD
            const categoria = celdas[1].textContent.toLowerCase();
            const descripcion = celdas[2].textContent.toLowerCase();
            const valor = parseFloat(celdas[3].getAttribute('data-valor')) || 0;
            const estado = celdas[4].getAttribute('data-estado');
            
            // Comparar fechas en formato original (YYYY-MM-DD)
            let coincideFecha = true;
            if (filtroFechaVal) {
                coincideFecha = fechaOriginal === filtroFechaVal;
            }
            
            // Buscar en categoría Y descripción
            const coincideCategoria = !filtroCategoriaVal || 
                                     categoria.includes(filtroCategoriaVal) || 
                                     descripcion.includes(filtroCategoriaVal);
            
            const coincideEstado = !filtroEstadoVal || estado === filtroEstadoVal;
            
            if (coincideFecha && coincideCategoria && coincideEstado) {
                filas[i].style.display = '';
                filasVisibles++;
                
                // Solo sumar al total si el gasto está activo
                if (estado === 'Activo') {
                    totalFiltrado += valor;
                }
            } else {
                filas[i].style.display = 'none';
            }
        }
        
        // Actualizar total en el tfoot
        totalGastosElement.innerHTML = `<strong>${formatearPesosColombianos(totalFiltrado)}</strong>`;
        
        // Mostrar/ocultar mensaje de sin resultados
        if (filasVisibles === 0) {
            tabla.style.display = 'none';
            tfoot.style.display = 'none';
            mensajeNoResultados.classList.remove('d-none');
        } else {
            tabla.style.display = '';
            tfoot.style.display = '';
            mensajeNoResultados.classList.add('d-none');
        }
    }
    
    // Event listeners para filtros
    filtroFecha.addEventListener('input', aplicarFiltros);
    filtroCategoria.addEventListener('input', aplicarFiltros);
    filtroEstado.addEventListener('change', aplicarFiltros);
    
    // Limpiar filtros
    function limpiarFiltros() {
        filtroFecha.value = '';
        filtroCategoria.value = '';
        filtroEstado.value = '';
        aplicarFiltros();
    }
    
    btnLimpiarFiltros.addEventListener('click', limpiarFiltros);
    if (btnLimpiarFiltrosMensaje) {
        btnLimpiarFiltrosMensaje.addEventListener('click', limpiarFiltros);
    }
    
    // Aplicar filtros inicialmente para calcular total correcto
    aplicarFiltros();
});
</script>