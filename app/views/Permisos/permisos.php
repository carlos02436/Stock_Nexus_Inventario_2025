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
                <a href="index.php?page=crear_permisos" class="btn btn-neon">
                    <i class="fas fa-plus me-2"></i>Nuevo Permiso
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
                            <button type="button" class="btn btn-danger" id="btnLimpiarFiltros">
                                <i class="fas fa-undo me-1"></i>Limpiar filtros
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
                                               class="btn btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($permiso['estado'] == 'activo'): ?>
                                                <button type="button" 
                                                        class="btn btn-danger btnCambiarEstado" 
                                                        data-id="<?= $permiso['id_permiso'] ?>"
                                                        data-nombre="Permiso: <?= htmlspecialchars($permiso['nombre_rol']) ?> - <?= htmlspecialchars($permiso['nombre_modulo']) ?>"
                                                        data-accion="inactivar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" 
                                                        class="btn btn-success btnCambiarEstado" 
                                                        data-id="<?= $permiso['id_permiso'] ?>"
                                                        data-nombre="Permiso: <?= htmlspecialchars($permiso['nombre_rol']) ?> - <?= htmlspecialchars($permiso['nombre_modulo']) ?>"
                                                        data-accion="activar">
                                                    <i class="fas fa-check"></i>
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

    <!-- MODAL DE CONFIRMACIÓN PROFESIONAL -->
    <div class="modal fade" id="modalCambiarEstadoPermiso" tabindex="-1" aria-labelledby="modalLabelCambiarEstado" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <!-- Header dinámico -->
                <div class="modal-header text-white py-4" id="modalHeaderCambiarEstado">
                    <div class="d-flex align-items-center w-100">
                        <div class="flex-shrink-0">
                            <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                                <i class="fas fa-shield-alt fa-lg" id="modalHeaderIcon"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="modal-title fw-bold mb-1" id="modalLabelCambiarEstado">
                                Confirmar Cambio de Estado
                            </h4>
                            <p class="mb-0 opacity-75" id="modalSubtitle">Gestión de Permisos del Sistema</p>
                        </div>
                        <button type="button" class="btn-close btn-close-white flex-shrink-0" data-bs-dismiss="modal"></button>
                    </div>
                </div>
                
                <!-- Body del modal -->
                <div class="modal-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                                <i class="fas fa-trash-alt fa-2x text-danger" id="modalStatusIcon"></i>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <h5 class="fw-bold text-dark mb-3" id="modalActionTitle">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                Inactivar Permiso
                            </h5>
                            
                            <p class="text-muted mb-4">
                                Está a punto de <span class="fw-bold text-dark" id="accionTexto">inactivar</span> un permiso del sistema. 
                                Por favor confirme esta acción.
                            </p>
                            
                            <div class="card border-warning mb-4">
                                <div class="card-body bg-warning bg-opacity-10">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-key text-warning fa-lg me-3"></i>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">Detalles del Permiso</h6>
                                            <p class="text-dark mb-0" id="nombrePermisoCambiarEstado">
                                                Permiso: Administrador - Categorías
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info border-0 bg-light">
                                <div class="d-flex">
                                    <i class="fas fa-info-circle text-info mt-1 me-3"></i>
                                    <div>
                                        <small class="text-muted">
                                            <span class="fw-bold">Nota importante:</span> 
                                            Los permisos inactivos no estarán disponibles en el sistema. 
                                            Puede reactivar este permiso en cualquier momento desde la lista de permisos.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <form id="formCambiarEstadoPermiso" method="POST" action="index.php?page=cambiar_estado_permiso">
                        <input type="hidden" name="id" id="permisoIdCambiarEstado">
                        <input type="hidden" name="accion" id="permisoAccionCambiarEstado">
                    </form>
                </div>
                
                <!-- Footer del modal -->
                <div class="modal-footer border-top py-4">
                    <div class="w-100 d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Esta acción es reversible
                            </small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-secondary btn-lg px-4 me-2" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </button>
                            <button type="submit" form="formCambiarEstadoPermiso" class="btn btn-lg px-4" id="btnConfirmarCambioEstado">
                                <i class="fas fa-check me-2"></i>
                                <span id="btnConfirmText">Inactivar</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... código de filtrado anterior ...

        // Configurar modal de cambio de estado PROFESIONAL
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
                const accionTextoCapitalized = esInactivar ? 'Inactivar' : 'Activar';
                const colorHeader = esInactivar ? 'bg-danger' : 'bg-success';
                const colorBtn = esInactivar ? 'btn-danger' : 'btn-success';
                const iconoPrincipal = esInactivar ? 'fa-trash-alt text-danger' : 'fa-check-circle text-success';
                const iconoHeader = esInactivar ? 'fa-exclamation-triangle' : 'fa-check-circle';
                const tituloAccion = esInactivar ? 'Inactivar Permiso' : 'Activar Permiso';
                const subtitulo = esInactivar ? 'Deshabilitar acceso temporalmente' : 'Habilitar acceso al sistema';
                
                // Actualizar textos
                document.getElementById('accionTexto').textContent = accionTexto;
                document.getElementById('btnConfirmText').textContent = accionTextoCapitalized;
                document.getElementById('modalActionTitle').innerHTML = `<i class="fas ${iconoHeader} me-2"></i>${tituloAccion}`;
                document.getElementById('modalSubtitle').textContent = subtitulo;
                
                // Actualizar colores e iconos
                const header = document.getElementById('modalHeaderCambiarEstado');
                header.className = `modal-header text-white py-4 ${colorHeader}`;
                
                const iconoPrincipalElement = document.getElementById('modalStatusIcon');
                iconoPrincipalElement.className = `fas ${iconoPrincipal} fa-2x`;
                
                const btnConfirmar = document.getElementById('btnConfirmarCambioEstado');
                btnConfirmar.className = `btn btn-lg px-4 ${colorBtn}`;
                
                // Actualizar mensaje informativo según la acción
                const infoAlert = document.querySelector('.alert-info small');
                if (esInactivar) {
                    infoAlert.innerHTML = `
                        <span class="fw-bold">Nota importante:</span> 
                        Los permisos inactivos no estarán disponibles en el sistema. 
                        Puede reactivar este permiso en cualquier momento desde la lista de permisos.
                    `;
                } else {
                    infoAlert.innerHTML = `
                        <span class="fw-bold">Nota importante:</span> 
                        Los permisos activos estarán disponibles inmediatamente en el sistema. 
                        Puede desactivar este permiso en cualquier momento si es necesario.
                    `;
                }
                
                // Mostrar el modal
                const modal = new bootstrap.Modal(modalCambiarEstado);
                modal.show();
            });
        });
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filtroRol = document.getElementById('filtroRol');
        const filtroModulo = document.getElementById('filtroModulo');
        const filtroEstado = document.getElementById('filtroEstado');
        const filas = document.querySelectorAll('tbody tr');
        const contador = document.getElementById('contadorResultados');
        const mensajeSinResultados = document.getElementById('mensajeSinResultados');
        const btnLimpiar = document.getElementById('btnLimpiarFiltros');
        const tabla = document.querySelector('table');

        // Filtrado de tabla
        function filtrarTabla() {
            let contadorVisible = 0;
            const rol = filtroRol.value.toLowerCase();
            const modulo = filtroModulo.value.toLowerCase();
            const estado = filtroEstado.value.toLowerCase();

            filas.forEach(fila => {
                if (fila.cells.length === 1) return;
                
                const columnas = fila.querySelectorAll('td');
                if (columnas.length === 0) return;

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

        [filtroRol, filtroModulo, filtroEstado].forEach(f => {
            f.addEventListener('change', filtrarTabla);
        });

        btnLimpiar.addEventListener('click', () => {
            filtroRol.value = '';
            filtroModulo.value = '';
            filtroEstado.value = '';
            filtrarTabla();
        });

        // Configurar modal de cambio de estado
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
                const accionTexto = accion === 'inactivar' ? 'inactivar' : 'activar';
                const textoBoton = accion === 'inactivar' ? 'Inactivar' : 'Activar';
                const colorHeader = accion === 'inactivar' ? 'bg-danger' : 'bg-success';
                const iconoHeader = accion === 'inactivar' ? 'fa-exclamation-triangle' : 'fa-check-circle';
                const iconoPrincipal = accion === 'inactivar' ? 'fa-trash text-danger' : 'fa-check text-success';
                
                document.getElementById('accionTexto').textContent = accionTexto;
                document.getElementById('btnConfirmarCambioEstado').innerHTML = `<i class="fas fa-check me-2"></i>${textoBoton}`;
                
                // Cambiar colores según la acción
                const header = document.getElementById('modalHeaderCambiarEstado');
                header.className = `modal-header text-white ${colorHeader} py-3`;
                
                const iconoHeaderElement = header.querySelector('i');
                iconoHeaderElement.className = `${iconoHeader} me-2`;
                
                const iconoPrincipalElement = document.getElementById('modalIconCambiarEstado').querySelector('i');
                iconoPrincipalElement.className = iconoPrincipal;
                
                // Mostrar el modal
                const modal = new bootstrap.Modal(modalCambiarEstado);
                modal.show();
            });
        });
    });
    </script>
<main>