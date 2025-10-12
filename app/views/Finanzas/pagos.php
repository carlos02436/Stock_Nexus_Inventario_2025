<?php
// app/views/finanzas/pagos.php
$finanzaController = new FinanzaController($db);
$pagos = $finanzaController->listarPagos();

// Obtener valores únicos para los filtros
$tiposUnicos = array_unique(array_column($pagos, 'tipo_pago'));
$metodosUnicos = array_unique(array_column($pagos, 'metodo_pago'));
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-money-bill-wave me-2"></i>Registro de Pagos</h1>
        <div class="btn-toolbar mb-2 mb-md-2">
            <div class="d-flex gap-2">
                <a href="index.php?page=finanzas" class="btn btn-secondary rounded-3 px-3 py-2">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Finanzas
                </a>
                <a href="index.php?page=crear_pago&tipo=Ingreso" class="btn btn-neon rounded-3 px-3 py-2">
                    <i class="fas fa-plus me-2"></i>Nuevo Ingreso
                </a>
                <a href="index.php?page=crear_pago&tipo=Egreso" class="btn btn-danger rounded-3 px-3 py-2">
                    <i class="fas fa-minus me-2"></i>Nuevo Egreso
                </a>
            </div>
        </div>
    </div>

    <!-- Card de Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Filtro por Fecha -->
                <div class="col-md-3">
                    <label for="filtroFecha" class="form-label fw-bold text-white">Fecha</label>
                    <input type="date" class="form-control" id="filtroFecha" placeholder="Buscar por fecha...">
                </div>
                
                <!-- Filtro por Tipo -->
                <div class="col-md-3">
                    <label for="filtroTipo" class="form-label fw-bold text-white">Tipo</label>
                    <select class="form-select" id="filtroTipo">
                        <option value="">Todos los tipos</option>
                        <?php foreach ($tiposUnicos as $tipo): ?>
                            <option value="<?= $tipo ?>"><?= $tipo ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Filtro por Referencia -->
                <div class="col-md-3">
                    <label for="filtroReferencia" class="form-label fw-bold text-white">Referencia</label>
                    <input type="text" class="form-control" id="filtroReferencia" placeholder="Buscar referencia...">
                </div>
                
                <!-- Filtro por Método -->
                <div class="col-md-3">
                    <label for="filtroMetodo" class="form-label fw-bold text-white">Método</label>
                    <select class="form-select" id="filtroMetodo">
                        <option value="">Todos los métodos</option>
                        <?php foreach ($metodosUnicos as $metodo): ?>
                            <option value="<?= $metodo ?>"><?= $metodo ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <!-- Botón Limpiar Filtros -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="d-flex gap-2 justify-content-start">
                        <button type="button" id="btnLimpiarFiltros" class="btn btn-danger rounded-3 px-3 py-2">
                            <i class="fas fa-broom me-2"></i>Limpiar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card de la Tabla -->
    <div class="card shadow-sm">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Historial de Pagos
            </h5>
            <div class="text-muted small" id="contadorResultados">
                Mostrando <?= count($pagos) ?> registros
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaPagos">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Referencia</th>
                            <th>Descripción</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPagos">
                        <?php foreach ($pagos as $pago): ?>
                        <tr>
                            <td data-fecha="<?= date('Y-m-d', strtotime($pago['fecha_pago'])) ?>">
                                <?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?>
                            </td>
                            <td data-tipo="<?= $pago['tipo_pago'] ?>">
                                <span class="badge bg-<?= $pago['tipo_pago'] == 'Ingreso' ? 'success' : 'danger' ?>">
                                    <?= $pago['tipo_pago'] ?>
                                </span>
                            </td>
                            <td data-referencia="<?= strtolower($pago['referencia']) ?>">
                                <?= $pago['referencia'] ?>
                            </td>
                            <td><?= $pago['descripcion'] ?></td>
                            <td class="<?= $pago['tipo_pago'] == 'Ingreso' ? 'text-success' : 'text-danger' ?>">
                                <strong>$<?= number_format($pago['monto'], 2) ?></strong>
                            </td>
                            <td data-metodo="<?= $pago['metodo_pago'] ?>">
                                <span class="badge bg-info"><?= $pago['metodo_pago'] ?></span>
                            </td>
                            <td><?= $pago['usuario_nombre'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Mensaje cuando no hay resultados -->
                <div id="mensajeNoResultados" class="text-center py-5 d-none">
                    <div class="mb-3">
                        <i class="fas fa-search fa-3x text-muted"></i>
                    </div>
                    <h4 class="text-white">No hay coincidencias de búsqueda</h4>
                    <p class="text-white">Intenta ajustar los filtros para ver más resultados.</p>
                    <button type="button" id="btnLimpiarDesdeMensaje" class="btn btn-neon mt-2">
                        <i class="fas fa-broom me-2 text-white"></i>Limpiar todos los filtros
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabla = document.getElementById('tablaPagos');
    const tbody = document.getElementById('tbodyPagos');
    const filas = Array.from(tbody.querySelectorAll('tr'));
    const contador = document.getElementById('contadorResultados');
    const mensajeNoResultados = document.getElementById('mensajeNoResultados');
    const btnLimpiarDesdeMensaje = document.getElementById('btnLimpiarDesdeMensaje');
    
    // Elementos de filtro
    const filtroFecha = document.getElementById('filtroFecha');
    const filtroTipo = document.getElementById('filtroTipo');
    const filtroReferencia = document.getElementById('filtroReferencia');
    const filtroMetodo = document.getElementById('filtroMetodo');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    
    // Función para mostrar/ocultar mensaje de no resultados
    function toggleMensajeNoResultados(mostrar) {
        if (mostrar) {
            mensajeNoResultados.classList.remove('d-none');
            tabla.querySelector('thead').style.display = 'none';
        } else {
            mensajeNoResultados.classList.add('d-none');
            tabla.querySelector('thead').style.display = '';
        }
    }
    
    // Función para aplicar filtros automáticamente
    function aplicarFiltros() {
        const fechaValor = filtroFecha.value;
        const tipoValor = filtroTipo.value.toLowerCase();
        const referenciaValor = filtroReferencia.value.toLowerCase();
        const metodoValor = filtroMetodo.value.toLowerCase();
        
        let filasVisibles = 0;
        
        filas.forEach(fila => {
            const fechaFila = fila.querySelector('td[data-fecha]')?.getAttribute('data-fecha') || '';
            const tipoFila = fila.querySelector('td[data-tipo]')?.getAttribute('data-tipo')?.toLowerCase() || '';
            const referenciaFila = fila.querySelector('td[data-referencia]')?.getAttribute('data-referencia') || '';
            const metodoFila = fila.querySelector('td[data-metodo]')?.getAttribute('data-metodo')?.toLowerCase() || '';
            
            let coincide = true;
            
            // Filtro por fecha
            if (fechaValor && fechaFila !== fechaValor) {
                coincide = false;
            }
            
            // Filtro por tipo
            if (tipoValor && tipoFila !== tipoValor) {
                coincide = false;
            }
            
            // Filtro por referencia (búsqueda parcial)
            if (referenciaValor && !referenciaFila.includes(referenciaValor)) {
                coincide = false;
            }
            
            // Filtro por método
            if (metodoValor && metodoFila !== metodoValor) {
                coincide = false;
            }
            
            // Mostrar u ocultar fila según coincidencia
            fila.style.display = coincide ? '' : 'none';
            
            if (coincide) {
                filasVisibles++;
            }
        });
        
        // Mostrar mensaje si no hay resultados
        if (filasVisibles === 0) {
            toggleMensajeNoResultados(true);
            contador.textContent = 'No se encontraron registros';
        } else {
            toggleMensajeNoResultados(false);
            contador.textContent = `Mostrando ${filasVisibles} de ${filas.length} registros`;
        }
    }
    
    // Función para limpiar filtros
    function limpiarFiltros() {
        filtroFecha.value = '';
        filtroTipo.value = '';
        filtroReferencia.value = '';
        filtroMetodo.value = '';
        
        // Mostrar todas las filas
        filas.forEach(fila => {
            fila.style.display = '';
        });
        
        // Ocultar mensaje y mostrar tabla normal
        toggleMensajeNoResultados(false);
        contador.textContent = `Mostrando ${filas.length} registros`;
    }
    
    // Event Listeners para filtrado automático
    filtroFecha.addEventListener('input', aplicarFiltros);
    filtroTipo.addEventListener('change', aplicarFiltros);
    filtroReferencia.addEventListener('input', aplicarFiltros);
    filtroMetodo.addEventListener('change', aplicarFiltros);
    
    // Event Listeners para limpiar filtros
    btnLimpiarFiltros.addEventListener('click', limpiarFiltros);
    btnLimpiarDesdeMensaje.addEventListener('click', limpiarFiltros);
    
    // Aplicar filtros automáticamente al cargar la página si hay valores en los filtros
    aplicarFiltros();
});
</script>