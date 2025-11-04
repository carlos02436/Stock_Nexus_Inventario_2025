<?php
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}

$rolController = new RolController($db);

// Obtener roles
$roles = $rolController->listar();
?>
<body>
    <div class="container-fluid px-4 mb-5" style="margin-top:180px;">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="fas fa-user-shield me-2"></i>Gestión de Roles</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="index.php?page=crear_rol" class="boton1 text-decoration-none">
                <div class="boton-top1"><i class="fas fa-plus me-2"></i>Nuevo Rol</div>
                <div class="boton-bottom1"></div>
                <div class="boton-base1"></div>
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-header text-white py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4 text-white">
                        <label for="filtroNombre" class="form-label">Nombre del Rol</label>
                        <input type="text" class="form-control filtro" id="filtroNombre" placeholder="Buscar por nombre...">
                    </div>
                    <div class="col-md-4 text-white">
                        <label for="filtroEstado" class="form-label">Estado</label>
                        <select class="form-select filtro" id="filtroEstado">
                            <option value="">Todos</option>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-4 text-white">
                        <label for="filtroFecha" class="form-label">Fecha de Creación</label>
                        <input type="text" class="form-control filtro" id="filtroFecha" placeholder="Buscar por fecha (dd/mm/aaaa)...">
                    </div>
                    <div class="col-md-12">
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-white" id="contadorResultados">
                                Mostrando <?= count($roles) ?> rol(es)
                            </small>
                            <button type="button" class="boton2" id="btnLimpiarFiltros">
                                <div class="boton-top2"><i class="fas fa-undo me-1"></i>Limpiar filtros</div>
                                <div class="boton-bottom2"></div>
                                <div class="boton-base2"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLA DE ROLES -->
        <div class="card">
            <div id="tabla" class="card-header text-white py-3">
                <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Lista de Roles</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Nombre del Rol</th>
                                <th>Estado</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($roles) > 0): ?>
                                <?php foreach ($roles as $rol): ?>
                                <tr>
                                    <td><?= htmlspecialchars($rol['nombre_rol']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $rol['estado'] == 1 ? 'success' : 'secondary' ?>">
                                            <?= $rol['estado'] == 1 ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($rol['fecha_creacion'])) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?page=editar_rol&id=<?= $rol['id_rol'] ?>" 
                                            class="boton4" style="min-width:auto;" title="Editar">
                                                <div class="boton-top4"><i class="fas fa-edit"></i></div>
                                                <div class="boton-bottom4"></div>
                                                <div class="boton-base4"></div>
                                            </a>
                                            <button type="button"  style="min-width:auto;"
                                                    class="<?= $rol['estado'] == 1 ? 'boton2' : 'boton1' ?> btnModalEstado" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalConfirmar" 
                                                    data-id="<?= $rol['id_rol'] ?>"
                                                    data-nombre="<?= htmlspecialchars($rol['nombre_rol']) ?>"
                                                    data-accion="<?= $rol['estado'] == 1 ? 'inactivar' : 'activar' ?>">
                                                <div class="<?= $rol['estado'] == 1 ? 'boton-top2' : 'boton-top1' ?>">
                                                    <i class="fas <?= $rol['estado'] == 1 ? 'fa-ban' : 'fa-check' ?>"></i>
                                                </div>
                                                <div class="<?= $rol['estado'] == 1 ? 'boton-bottom2' : 'boton-bottom1' ?>"></div>
                                                <div class="<?= $rol['estado'] == 1 ? 'boton-base2' : 'boton-base1' ?>"></div>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Mostrar mensaje cuando no hay roles en la base de datos -->
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="text-dark fw-bold py-3">
                                            <i class="fas fa-info-circle me-2"></i>No hay roles registrados en el sistema.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Mensaje "sin resultados" - OCULTO POR DEFECTO -->
                    <div id="mensajeSinResultados" class="text-center bg-white rounded-3 text-dark fw-bold py-3" style="display: none;">
                        ⚠️ No se encontraron resultados para la búsqueda realizada.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE CONFIRMACIÓN MEJORADO -->
    <div class="modal fade" id="modalConfirmar" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4 animate__animated animate__zoomIn">
                
                <!-- Encabezado dinámico -->
                <div class="modal-header text-white" id="modalHeader">
                    <h5 class="modal-title fw-bold" id="modalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmación de Acción
                    </h5>
                    <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"></button>
                </div>

                <!-- Cuerpo del modal -->
                <div class="modal-body text-center py-4">
                    <div id="iconoAccion" class="mb-3 fs-1"></div>
                    <p class="fs-5 text-muted mb-2">
                        ¿Estás seguro de que deseas 
                        <span id="accionTexto" class="fw-bold text-uppercase"></span>
                        el rol <strong class="text-dark" id="nombreRol"></strong>?
                    </p>
                    <form id="formEstado" method="POST" action="index.php?page=cambiar_estado_rol">
                        <input type="hidden" name="id" id="rolId">
                        <input type="hidden" name="accion" id="accionRol">
                    </form>
                </div>

                <!-- Pie de botones -->
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

    <!-- Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtrado (ya existente)
        const filtroNombre = document.getElementById('filtroNombre');
        const filtroEstado = document.getElementById('filtroEstado');
        const filtroFecha = document.getElementById('filtroFecha');
        const filas = document.querySelectorAll('tbody tr');
        const contador = document.getElementById('contadorResultados');
        const mensajeSinResultados = document.getElementById('mensajeSinResultados');
        const btnLimpiar = document.getElementById('btnLimpiarFiltros');
        const tabla = document.querySelector('table');

        function filtrarTabla() {
            let contadorVisible = 0;
            const nombre = filtroNombre.value.toLowerCase();
            const estado = filtroEstado.value.toLowerCase();
            const fecha = filtroFecha.value.toLowerCase();

            filas.forEach(fila => {
                // Saltar la fila del mensaje "no hay roles"
                if (fila.cells.length === 1) return;
                
                const columnas = fila.querySelectorAll('td');
                if (columnas.length === 0) return;

                const nombreRol = columnas[0].textContent.toLowerCase();
                const estadoRol = columnas[1].textContent.toLowerCase();
                const fechaRol = columnas[2].textContent.toLowerCase();

                const coincide =
                    (nombre === '' || nombreRol.includes(nombre)) &&
                    (estado === '' || estadoRol.includes(estado)) &&
                    (fecha === '' || fechaRol.includes(fecha));

                fila.style.display = coincide ? '' : 'none';
                if (coincide) contadorVisible++;
            });

            contador.textContent = `Mostrando ${contadorVisible} rol(es)`;
            
            // Mostrar/ocultar mensaje y tabla según resultados
            if (contadorVisible === 0) {
                mensajeSinResultados.style.display = 'block';
                tabla.style.display = 'none';
            } else {
                mensajeSinResultados.style.display = 'none';
                tabla.style.display = 'table';
            }
        }

        [filtroNombre, filtroEstado, filtroFecha].forEach(f => {
            f.addEventListener('input', filtrarTabla);
            f.addEventListener('change', filtrarTabla);
        });

        btnLimpiar.addEventListener('click', () => {
            filtroNombre.value = '';
            filtroEstado.value = '';
            filtroFecha.value = '';
            filtrarTabla();
        });

        // Inicializar estado al cargar la página
        filtrarTabla();

        // --- Configurar modal dinámicamente ---
        const modalConfirmar = document.getElementById('modalConfirmar');
        modalConfirmar.addEventListener('show.bs.modal', function(event) {
            const boton = event.relatedTarget;
            const id = boton.getAttribute('data-id');
            const nombre = boton.getAttribute('data-nombre');
            const accion = boton.getAttribute('data-accion');

            const modalTitle = modalConfirmar.querySelector('#modalLabel');
            const accionTexto = modalConfirmar.querySelector('#accionTexto');
            const nombreRol = modalConfirmar.querySelector('#nombreRol');
            const rolId = modalConfirmar.querySelector('#rolId');
            const accionRol = modalConfirmar.querySelector('#accionRol');
            const btnConfirmar = modalConfirmar.querySelector('#btnConfirmar');
            const iconoAccion = modalConfirmar.querySelector('#iconoAccion');
            const header = modalConfirmar.querySelector('#modalHeader');

            rolId.value = id;
            accionRol.value = accion;
            nombreRol.textContent = nombre;
            accionTexto.textContent = accion === 'activar' ? 'activar' : 'inactivar';

            if (accion === 'activar') {
                header.style.background = 'linear-gradient(90deg, #198754, #28a745)';
                iconoAccion.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
                btnConfirmar.classList.remove('inactivar');
                btnConfirmar.classList.add('activar');
                btnConfirmar.innerHTML = '<i class="fas fa-check me-2"></i>Confirmar Activación';
            } else {
                header.style.background = 'linear-gradient(90deg, #dc3545, #b02a37)';
                iconoAccion.innerHTML = '<i class="fas fa-ban text-danger"></i>';
                btnConfirmar.classList.remove('activar');
                btnConfirmar.classList.add('inactivar');
                btnConfirmar.innerHTML = '<i class="fas fa-ban me-2"></i>Confirmar Inactivación';
            }
        });
    });
    </script>
</main>