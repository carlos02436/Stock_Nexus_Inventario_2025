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
                                    <a href="index.php?page=marcar_compra_pagada&id=<?= $compra['id_compra'] ?>" 
                                       class="btn btn-success" title="Marcar como Pagada">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($compra['estado'] != 'Anulada'): ?>
                                    <a href="index.php?page=anular_compra&id=<?= $compra['id_compra'] ?>" 
                                       class="btn btn-danger" title="Anular Compra" 
                                       onclick="return confirm('¿Está seguro de anular esta compra? Se restará del inventario.')">
                                        <i class="fas fa-ban"></i>
                                    </a>
                                    <?php else: ?>
                                    <a href="index.php?page=reanudar_compra&id=<?= $compra['id_compra'] ?>" 
                                       class="btn btn-success" title="Reanudar Compra" 
                                       onclick="return confirm('¿Está seguro de reanudar esta compra? Se sumará al inventario.')">
                                        <i class="fas fa-redo"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($compras)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
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
                    <td colspan="5" class="text-center text-muted py-4">
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

    // Filtrar inicialmente para asegurar que todo funcione
    filtrarCompras();
});
</script>