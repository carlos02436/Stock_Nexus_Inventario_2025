<?php
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}

$moduloController = new ModuloController($db);
$modulos = $moduloController->listarTodos();
?>
<body>
    <div class="container-fluid px-4 mb-5" style="margin-top:180px;">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="fas fa-cubes me-2"></i>Gestión de Módulos del Sistema</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="index.php?page=crear_modulo" class="btn btn-neon">
                    <i class="fas fa-plus me-2"></i>Nuevo Módulo
                </a>
            </div>
        </div>

        <!-- FILTRO DE BÚSQUEDA -->
        <div class="card mb-4">
            <div class="card-header text-white py-3">
                <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filtrar Módulos</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 text-white">
                        <label for="filtroNombre" class="form-label small">Nombre</label>
                        <input type="text" id="filtroNombre" class="form-control form-control-sm" 
                               placeholder="Buscar por nombre...">
                    </div>
                    <div class="col-md-3 text-white">
                        <label for="filtroDescripcion" class="form-label small">Descripción</label>
                        <input type="text" id="filtroDescripcion" class="form-control form-control-sm" 
                               placeholder="Buscar por descripción...">
                    </div>
                    <div class="col-md-3 text-white">
                        <label for="filtroRuta" class="form-label small">Ruta</label>
                        <input type="text" id="filtroRuta" class="form-control form-control-sm" 
                               placeholder="Buscar por ruta...">
                    </div>
                    <div class="col-md-3 text-white">
                        <label for="filtroEstado" class="form-label small">Estado</label>
                        <select id="filtroEstado" class="form-select form-select-sm">
                            <option value="">Todos los estados</option>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
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

        <!-- TABLA DE MÓDULOS -->
        <div class="card">
            <div class="card-header text-white py-3">
                <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Lista de Módulos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle" id="tablaModulos">
                        <thead class="table-dark">
                            <tr>
                                <th>Orden</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Icono</th>
                                <th>Ruta</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($modulos) > 0): ?>
                                <?php foreach ($modulos as $modulo): ?>
                                <tr>
                                    <td><?= $modulo['orden'] ?></td>
                                    <td><?= htmlspecialchars($modulo['nombre_modulo']) ?></td>
                                    <td><?= htmlspecialchars($modulo['descripcion']) ?></td>
                                    <td>
                                        <i class="fas fa-<?= $modulo['icono'] ?> me-1"></i>
                                        <?= $modulo['icono'] ?>
                                    </td>
                                    <td><?= htmlspecialchars($modulo['ruta']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $modulo['estado'] == 'Activo' ? 'success' : 'secondary' ?>">
                                            <?= $modulo['estado'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?page=editar_modulo&id=<?= $modulo['id_modulo'] ?>" 
                                               class="btn btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-<?= $modulo['estado'] == 'Activo' ? 'danger' : 'success' ?> btnCambiarEstado" 
                                                    data-id="<?= $modulo['id_modulo'] ?>"
                                                    data-nombre="<?= htmlspecialchars($modulo['nombre_modulo']) ?>"
                                                    data-accion="<?= $modulo['estado'] == 'Activo' ? 'inactivar' : 'activar' ?>">
                                                <i class="fas <?= $modulo['estado'] == 'Activo' ? 'fa-ban' : 'fa-check' ?>"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-dark fw-bold py-3">
                                            <i class="fas fa-info-circle me-2"></i>No hay módulos registrados en el sistema.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Mensaje cuando no hay resultados -->
                <div id="mensajeNoResultados" class="alert alert-warning text-center d-none mt-3">
                    <i class="fas fa-search me-2"></i>No se encontraron módulos que coincidan con los filtros aplicados.
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE CONFIRMACIÓN -->
    <div class="modal fade" id="modalConfirmar" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header text-white" id="modalHeader">
                    <h5 class="modal-title fw-bold" id="modalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmación de Acción
                    </h5>
                    <button type="button" class="btn-close btn-light" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div id="iconoAccion" class="mb-3 fs-1"></div>
                    <p class="fs-5 text-muted mb-2">
                        ¿Estás seguro de que deseas 
                        <span id="accionTexto" class="fw-bold text-uppercase"></span>
                        el módulo <strong class="text-dark" id="nombreModulo"></strong>?
                    </p>
                    <form id="formEstado" method="POST" action="index.php?page=cambiar_estado_modulo">
                        <input type="hidden" name="id" id="moduloId">
                        <input type="hidden" name="accion" id="accionModulo">
                    </form>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" form="formEstado" class="btn btn-success px-4" id="btnConfirmar">
                        <i class="fas fa-check me-2"></i>Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalConfirmar = document.getElementById('modalConfirmar');
        const botonesEstado = document.querySelectorAll('.btnCambiarEstado');
        
        botonesEstado.forEach(boton => {
            boton.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');
                const accion = this.getAttribute('data-accion');
                
                document.getElementById('moduloId').value = id;
                document.getElementById('accionModulo').value = accion;
                document.getElementById('nombreModulo').textContent = nombre;
                document.getElementById('accionTexto').textContent = accion;
                
                // Configurar colores según la acción
                const header = document.getElementById('modalHeader');
                const icono = document.getElementById('iconoAccion');
                const btnConfirmar = document.getElementById('btnConfirmar');
                
                if (accion === 'activar') {
                    header.style.background = 'linear-gradient(90deg, #198754, #28a745)';
                    icono.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
                    btnConfirmar.innerHTML = '<i class="fas fa-check me-2"></i>Confirmar Activación';
                } else {
                    header.style.background = 'linear-gradient(90deg, #dc3545, #b02a37)';
                    icono.innerHTML = '<i class="fas fa-ban text-danger"></i>';
                    btnConfirmar.innerHTML = '<i class="fas fa-ban me-2"></i>Confirmar Inactivación';
                }
                
                // Mostrar el modal
                const modal = new bootstrap.Modal(modalConfirmar);
                modal.show();
            });
        });

        // FILTRADO DE LA TABLA
        const filtroNombre = document.getElementById('filtroNombre');
        const filtroDescripcion = document.getElementById('filtroDescripcion');
        const filtroRuta = document.getElementById('filtroRuta');
        const filtroEstado = document.getElementById('filtroEstado');
        const btnLimpiar = document.getElementById('btnLimpiarFiltros');
        const tabla = document.getElementById('tablaModulos');
        const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        const mensajeNoResultados = document.getElementById('mensajeNoResultados');

        function filtrarTabla() {
            const nombreFiltro = filtroNombre.value.toLowerCase();
            const descripcionFiltro = filtroDescripcion.value.toLowerCase();
            const rutaFiltro = filtroRuta.value.toLowerCase();
            const estadoFiltro = filtroEstado.value;
            
            let resultadosEncontrados = 0;
            let hayFilasVisibles = false;

            for (let i = 0; i < filas.length; i++) {
                const celdas = filas[i].getElementsByTagName('td');
                let mostrarFila = true;
                
                // Verificar si es la fila de "no hay módulos"
                if (celdas.length === 1 && celdas[0].colSpan === 7) {
                    filas[i].style.display = 'none';
                    continue;
                }
                
                if (celdas.length >= 6) {
                    const nombre = celdas[1].textContent.toLowerCase();
                    const descripcion = celdas[2].textContent.toLowerCase();
                    const ruta = celdas[4].textContent.toLowerCase();
                    const estado = celdas[5].textContent.trim();
                    
                    // Aplicar filtros
                    if (nombreFiltro && !nombre.includes(nombreFiltro)) {
                        mostrarFila = false;
                    }
                    if (mostrarFila && descripcionFiltro && !descripcion.includes(descripcionFiltro)) {
                        mostrarFila = false;
                    }
                    if (mostrarFila && rutaFiltro && !ruta.includes(rutaFiltro)) {
                        mostrarFila = false;
                    }
                    if (mostrarFila && estadoFiltro && estado !== estadoFiltro) {
                        mostrarFila = false;
                    }
                }
                
                filas[i].style.display = mostrarFila ? '' : 'none';
                if (mostrarFila) {
                    resultadosEncontrados++;
                    hayFilasVisibles = true;
                }
            }

            // Mostrar/ocultar mensaje de no resultados
            if (resultadosEncontrados === 0) {
                mensajeNoResultados.classList.remove('d-none');
            } else {
                mensajeNoResultados.classList.add('d-none');
            }
        }

        // Event listeners para los filtros
        filtroNombre.addEventListener('keyup', filtrarTabla);
        filtroDescripcion.addEventListener('keyup', filtrarTabla);
        filtroRuta.addEventListener('keyup', filtrarTabla);
        filtroEstado.addEventListener('change', filtrarTabla);

        // Botón limpiar filtros
        btnLimpiar.addEventListener('click', function() {
            filtroNombre.value = '';
            filtroDescripcion.value = '';
            filtroRuta.value = '';
            filtroEstado.value = '';
            filtrarTabla();
        });

        // Inicializar la tabla
        filtrarTabla();
    });
    </script>
<main>