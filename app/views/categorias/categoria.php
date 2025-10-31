<?php
// app/views/categorias/categorias.php
$categoriaController = new CategoriaController($db);
$categorias = $categoriaController->listar();

// Obtener valores únicos para los filtros
$estadosUnicos = array_unique(array_column($categorias, 'estado'));
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-tags me-2"></i>Gestión de Categorías</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=crear_categoria" class="btn btn-neon rounded-3 px-3 py-2">
                <i class="fas fa-plus me-2"></i>Nueva Categoría
            </a>
        </div>
    </div>

    <!-- Card de Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2 text-white"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Filtro por Nombre -->
                <div class="col-md-4">
                    <label for="filtroNombre" class="form-label fw-bold text-white">Nombre</label>
                    <input type="text" class="form-control" id="filtroNombre" placeholder="Buscar por nombre...">
                </div>
                
                <!-- Filtro por Estado -->
                <div class="col-md-4">
                    <label for="filtroEstado" class="form-label fw-bold text-white">Estado</label>
                    <select class="form-select" id="filtroEstado">
                        <option value="">Todos los estados</option>
                        <?php foreach ($estadosUnicos as $estado): ?>
                            <option value="<?= $estado ?>"><?= $estado ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Filtro por Descripción -->
                <div class="col-md-4">
                    <label for="filtroDescripcion" class="form-label fw-bold text-white">Descripción</label>
                    <input type="text" class="form-control" id="filtroDescripcion" placeholder="Buscar en descripción...">
                </div>
            </div>
            
            <!-- Botón Limpiar Filtros -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="d-flex gap-2 justify-content-start">
                        <button type="button" id="btnLimpiarFiltros" class="btn btn-danger rounded-3 px-3 py-2">
                            <i class="fas fa-undo me-1"></i>Limpiar filtros
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
                <i class="fas fa-list me-2"></i>Lista de Categorías
            </h5>
            <div class="text-light small" id="contadorResultados">
                Mostrando <?= count($categorias) ?> registros
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaCategorias">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyCategorias">
                        <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td data-nombre="<?= strtolower($categoria['nombre_categoria']) ?>">
                                <?= $categoria['nombre_categoria'] ?>
                            </td>
                            <td data-descripcion="<?= strtolower($categoria['descripcion'] ?? '') ?>">
                                <?= $categoria['descripcion'] ?: 'Sin descripción' ?>
                            </td>
                            <td data-estado="<?= $categoria['estado'] ?>">
                                <span class="badge bg-<?= $categoria['estado'] == 'Activo' ? 'success' : 'secondary' ?>">
                                    <?= $categoria['estado'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=editar_categoria&id=<?= $categoria['id_categoria'] ?>" 
                                       class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?page=eliminar_categoria&id=<?= $categoria['id_categoria'] ?>" 
                                       class="btn btn-danger btn-delete" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
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
                    <button type="button" id="btnLimpiarDesdeMensaje" class="btn btn-danger mt-2">
                        <i class="fas fa-broom me-2"></i>Limpiar todos los filtros
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabla = document.getElementById('tablaCategorias');
    const tbody = document.getElementById('tbodyCategorias');
    const filas = Array.from(tbody.querySelectorAll('tr'));
    const contador = document.getElementById('contadorResultados');
    const mensajeNoResultados = document.getElementById('mensajeNoResultados');
    const btnLimpiarDesdeMensaje = document.getElementById('btnLimpiarDesdeMensaje');
    
    // Elementos de filtro
    const filtroNombre = document.getElementById('filtroNombre');
    const filtroEstado = document.getElementById('filtroEstado');
    const filtroDescripcion = document.getElementById('filtroDescripcion');
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
        const nombreValor = filtroNombre.value.toLowerCase();
        const estadoValor = filtroEstado.value;
        const descripcionValor = filtroDescripcion.value.toLowerCase();
        
        let filasVisibles = 0;
        
        filas.forEach(fila => {
            const nombreFila = fila.querySelector('td[data-nombre]')?.getAttribute('data-nombre') || '';
            const estadoFila = fila.querySelector('td[data-estado]')?.getAttribute('data-estado') || '';
            const descripcionFila = fila.querySelector('td[data-descripcion]')?.getAttribute('data-descripcion') || '';
            
            let coincide = true;
            
            // Filtro por nombre (búsqueda parcial)
            if (nombreValor && !nombreFila.includes(nombreValor)) {
                coincide = false;
            }
            
            // Filtro por estado
            if (estadoValor && estadoFila !== estadoValor) {
                coincide = false;
            }
            
            // Filtro por descripción (búsqueda parcial)
            if (descripcionValor && !descripcionFila.includes(descripcionValor)) {
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
        filtroNombre.value = '';
        filtroEstado.value = '';
        filtroDescripcion.value = '';
        
        // Mostrar todas las filas
        filas.forEach(fila => {
            fila.style.display = '';
        });
        
        // Ocultar mensaje y mostrar tabla normal
        toggleMensajeNoResultados(false);
        contador.textContent = `Mostrando ${filas.length} registros`;
    }
    
    // Event Listeners para filtrado automático
    filtroNombre.addEventListener('input', aplicarFiltros);
    filtroEstado.addEventListener('change', aplicarFiltros);
    filtroDescripcion.addEventListener('input', aplicarFiltros);
    
    // Event Listeners para limpiar filtros
    btnLimpiarFiltros.addEventListener('click', limpiarFiltros);
    btnLimpiarDesdeMensaje.addEventListener('click', limpiarFiltros);
    
    // Aplicar filtros automáticamente al cargar la página
    aplicarFiltros();
});
</script>