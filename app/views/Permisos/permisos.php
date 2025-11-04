<?php
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}

$permisoController = new PermisoController($db);

// Obtener todos los permisos (activos e inactivos)
$permisos = $permisoController->listarTodosPermisosCompletos();
?>
<body>
    <div class="container-fluid px-4 mb-5" style="margin-top:180px;">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="fas fa-user-shield me-2"></i>Gestión de Permisos por Rol</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="index.php?page=crear_permisos" class="boton1 text-decoration-none">
                <div class="boton-top1"><i class="fas fa-plus me-2"></i>Nuevo Permiso</div>
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
                        <label for="filtroRol" class="form-label">Rol</label>
                        <select class="form-select filtro" id="filtroRol">
                            <option value="">Todos los roles</option>
                            <?php 
                            $rolesUnicos = [];
                            foreach ($permisos as $permiso) {
                                if (!in_array($permiso['nombre_rol'], $rolesUnicos)) {
                                    $rolesUnicos[] = $permiso['nombre_rol'];
                                    echo "<option value=\"{$permiso['nombre_rol']}\">{$permiso['nombre_rol']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 text-white">
                        <label for="filtroModulo" class="form-label">Módulo</label>
                        <select class="form-select filtro" id="filtroModulo">
                            <option value="">Todos los módulos</option>
                            <?php 
                            $modulosUnicos = [];
                            foreach ($permisos as $permiso) {
                                if (!in_array($permiso['nombre_modulo'], $modulosUnicos)) {
                                    $modulosUnicos[] = $permiso['nombre_modulo'];
                                    echo "<option value=\"{$permiso['nombre_modulo']}\">{$permiso['nombre_modulo']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 text-white">
                        <label for="filtroEstado" class="form-label">Estado</label>
                        <select class="form-select filtro" id="filtroEstado">
                            <option value="">Todos los estados</option>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-white" id="contadorResultados">
                                Mostrando <?= count($permisos) ?> permiso(s)
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

        <!-- TABLA DE PERMISOS -->
        <div class="card">
            <div id="tabla" class="card-header text-white py-3">
                <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Lista de Permisos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Rol</th>
                                <th>Módulo</th>
                                <th>Ver</th>
                                <th>Crear</th>
                                <th>Editar</th>
                                <th>Eliminar</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($permisos) > 0): ?>
                                <?php foreach ($permisos as $permiso): ?>
                                <tr>
                                    <td><?= htmlspecialchars($permiso['nombre_rol']) ?></td>
                                    <td><?= htmlspecialchars($permiso['nombre_modulo']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $permiso['puede_ver'] ? 'success' : 'secondary' ?>">
                                            <?= $permiso['puede_ver'] ? 'Sí' : 'No' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $permiso['puede_crear'] ? 'success' : 'secondary' ?>">
                                            <?= $permiso['puede_crear'] ? 'Sí' : 'No' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $permiso['puede_editar'] ? 'success' : 'secondary' ?>">
                                            <?= $permiso['puede_editar'] ? 'Sí' : 'No' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $permiso['puede_eliminar'] ? 'success' : 'secondary' ?>">
                                            <?= $permiso['puede_eliminar'] ? 'Sí' : 'No' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $permiso['estado'] == 'activo' ? 'success' : 'danger' ?>">
                                            <?= ucfirst($permiso['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?page=editar_permisos&id=<?= $permiso['id_permiso'] ?>" 
                                            class="boton4" style="min-width:auto;" title="Editar">
                                                <div class="boton-top4"><i class="fas fa-edit"></i></div>
                                                <div class="boton-bottom4"></div>
                                                <div class="boton-base4"></div>
                                            </a>
                                            <?php if ($permiso['estado'] == 'activo'): ?>
                                                <button type="button" title="Inactivar"
                                                        class="boton2 btnCambiarEstado" style="min-width:auto;" 
                                                        data-id="<?= $permiso['id_permiso'] ?>"
                                                        data-nombre="Permiso: <?= htmlspecialchars($permiso['nombre_rol']) ?> - <?= htmlspecialchars($permiso['nombre_modulo']) ?>"
                                                        data-accion="inactivar">
                                                    <div class="boton-top2"><i class="fas fa-trash"></i></div>
                                                    <div class="boton-bottom2"></div>
                                                    <div class="boton-base2"></div>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" title="Activar"
                                                        class="boton1 btnCambiarEstado" style="min-width:auto;" 
                                                        data-id="<?= $permiso['id_permiso'] ?>"
                                                        data-nombre="Permiso: <?= htmlspecialchars($permiso['nombre_rol']) ?> - <?= htmlspecialchars($permiso['nombre_modulo']) ?>"
                                                        data-accion="activar">
                                                    <div class="boton-top1"><i class="fas fa-check"></i></div>
                                                    <div class="boton-bottom1"></div>
                                                    <div class="boton-base1"></div>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-dark fw-bold py-3">
                                            <i class="fas fa-info-circle me-2"></i>No hay permisos registrados en el sistema.
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

    <!-- MODAL DE CONFIRMACIÓN PARA PERMISOS -->
    <div class="modal fade" id="modalCambiarEstadoPermiso" tabindex="-1" aria-labelledby="modalLabelPermiso" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header text-white" id="modalHeaderPermiso">
                    <h5 class="modal-title fw-bold" id="modalLabelPermiso">
                        <i class="fas fa-user-shield me-2"></i>
                        Confirmación de Acción
                    </h5>
                    <button type="button" class="btn-close btn-light" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div id="iconoAccionPermiso" class="mb-3 fs-1"></div>
                    <p class="fs-5 text-muted mb-2">
                        ¿Estás seguro de que deseas 
                        <span id="accionTextoPermiso" class="fw-bold text-uppercase"></span>
                        el permiso?
                    </p>
                    <div class="card border-light bg-light mt-3">
                        <div class="card-body">
                            <p class="text-dark mb-0 fw-medium" id="nombrePermisoCambiarEstado">
                                Permiso: Administrador - Categorías
                            </p>
                        </div>
                    </div>
                    <form id="formCambiarEstadoPermiso" method="POST" action="index.php?page=cambiar_estado_permiso">
                        <input type="hidden" name="id" id="permisoIdCambiarEstado">
                        <input type="hidden" name="accion" id="permisoAccionCambiarEstado">
                    </form>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" form="formCambiarEstadoPermiso" class="btn btn-success px-4" id="btnConfirmarCambioEstado">
                        <i class="fas fa-check me-2"></i>Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar modal de cambio de estado para permisos
        const modalCambiarEstado = document.getElementById('modalCambiarEstadoPermiso');
        const botonesCambiarEstado = document.querySelectorAll('.btnCambiarEstado');
        
        botonesCambiarEstado.forEach(boton => {
            boton.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');
                const accion = this.getAttribute('data-accion');
                
                document.getElementById('permisoIdCambiarEstado').value = id;
                document.getElementById('permisoAccionCambiarEstado').value = accion;
                document.getElementById('nombrePermisoCambiarEstado').textContent = nombre;
                
                // Configurar el modal según la acción
                const esInactivar = accion === 'inactivar';
                const accionTexto = esInactivar ? 'inactivar' : 'activar';
                const accionTextoCapitalized = esInactivar ? 'INACTIVAR' : 'ACTIVAR';
                const colorHeader = esInactivar ? 'bg-danger' : 'bg-success';
                const iconoPrincipal = esInactivar ? 'fa-ban text-danger' : 'fa-check-circle text-success';
                
                // Actualizar textos
                document.getElementById('accionTextoPermiso').textContent = accionTextoCapitalized;
                
                // Actualizar colores e iconos
                const header = document.getElementById('modalHeaderPermiso');
                header.className = `modal-header text-white ${colorHeader}`;
                
                const iconoPrincipalElement = document.getElementById('iconoAccionPermiso');
                iconoPrincipalElement.className = `fas ${iconoPrincipal}`;
                
                // Mostrar el modal
                const modal = new bootstrap.Modal(modalCambiarEstado);
                modal.show();
            });
        });

        // FILTRADO DE TABLA
        const filtroRol = document.getElementById('filtroRol');
        const filtroModulo = document.getElementById('filtroModulo');
        const filtroEstado = document.getElementById('filtroEstado');
        const filas = document.querySelectorAll('tbody tr');
        const contador = document.getElementById('contadorResultados');
        const mensajeSinResultados = document.getElementById('mensajeSinResultados');
        const btnLimpiar = document.getElementById('btnLimpiarFiltros');
        const tabla = document.querySelector('table');

        function filtrarTabla() {
            let contadorVisible = 0;
            const rol = filtroRol.value.toLowerCase();
            const modulo = filtroModulo.value.toLowerCase();
            const estado = filtroEstado.value.toLowerCase();

            filas.forEach(fila => {
                // Saltar filas vacías o de mensaje
                if (fila.cells.length < 8) return;
                
                const columnas = fila.querySelectorAll('td');
                if (columnas.length < 8) return;

                const nombreRol = columnas[0].textContent.toLowerCase();
                const nombreModulo = columnas[1].textContent.toLowerCase();
                const estadoPermiso = columnas[6].textContent.toLowerCase();

                const coincide =
                    (rol === '' || nombreRol.includes(rol)) &&
                    (modulo === '' || nombreModulo.includes(modulo)) &&
                    (estado === '' || estadoPermiso.includes(estado));

                fila.style.display = coincide ? '' : 'none';
                if (coincide) contadorVisible++;
            });

            contador.textContent = `Mostrando ${contadorVisible} permiso(s)`;
            
            if (contadorVisible === 0) {
                mensajeSinResultados.style.display = 'block';
                tabla.style.display = 'none';
            } else {
                mensajeSinResultados.style.display = 'none';
                tabla.style.display = 'table';
            }
        }

        // Event listeners para filtros
        [filtroRol, filtroModulo, filtroEstado].forEach(f => {
            f.addEventListener('change', filtrarTabla);
        });

        // Botón limpiar filtros
        btnLimpiar.addEventListener('click', function() {
            filtroRol.value = '';
            filtroModulo.value = '';
            filtroEstado.value = '';
            filtrarTabla();
        });

        // Filtrar tabla inicialmente
        filtrarTabla();
    });
    </script>
<main>