<?php
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}

$permisoController = new PermisoController($db);

// Obtener permisos con información de roles y módulos
$permisos = $permisoController->listarPermisosCompletos();
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
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?page=editar_permisos&id=<?= $permiso['id_permiso'] ?>" 
                                               class="btn btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-danger btnEliminarPermiso" 
                                                    data-id="<?= $permiso['id_permiso'] ?>"
                                                    data-nombre="Permiso: <?= htmlspecialchars($permiso['nombre_rol']) ?> - <?= htmlspecialchars($permiso['nombre_modulo']) ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
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

    <!-- MODAL DE CONFIRMACIÓN PARA ELIMINAR -->
    <div class="modal fade" id="modalEliminarPermiso" tabindex="-1" aria-labelledby="modalLabelEliminar" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4 animate__animated animate__zoomIn">
                <div class="modal-header text-white bg-danger py-3">
                    <h5 class="modal-title fw-bold" id="modalLabelEliminar">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-light" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3 fs-1">
                        <i class="fas fa-trash-alt text-danger"></i>
                    </div>
                    <p class="fs-5 text-muted mb-2">
                        ¿Estás seguro de que deseas eliminar el permiso 
                        <strong class="text-dark" id="nombrePermisoEliminar"></strong>?
                    </p>
                    <p class="text-warning">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Esta acción no se puede deshacer.
                    </p>
                    <form id="formEliminarPermiso" method="POST" action="index.php?page=eliminar_permiso">
                        <input type="hidden" name="id" id="permisoIdEliminar">
                    </form>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" form="formEliminarPermiso" class="btn btn-danger px-4">
                        <i class="fas fa-trash me-2"></i>Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filtroRol = document.getElementById('filtroRol');
        const filtroModulo = document.getElementById('filtroModulo');
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

            filas.forEach(fila => {
                if (fila.cells.length === 1) return;
                
                const columnas = fila.querySelectorAll('td');
                if (columnas.length === 0) return;

                const nombreRol = columnas[0].textContent.toLowerCase();
                const nombreModulo = columnas[1].textContent.toLowerCase();

                const coincide =
                    (rol === '' || nombreRol.includes(rol)) &&
                    (modulo === '' || nombreModulo.includes(modulo));

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

        [filtroRol, filtroModulo].forEach(f => {
            f.addEventListener('change', filtrarTabla);
        });

        btnLimpiar.addEventListener('click', () => {
            filtroRol.value = '';
            filtroModulo.value = '';
            filtrarTabla();
        });

        // Configurar modal de eliminación
        const modalEliminar = document.getElementById('modalEliminarPermiso');
        const botonesEliminar = document.querySelectorAll('.btnEliminarPermiso');
        
        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');
                
                document.getElementById('permisoIdEliminar').value = id;
                document.getElementById('nombrePermisoEliminar').textContent = nombre;
                
                // Mostrar el modal
                const modal = new bootstrap.Modal(modalEliminar);
                modal.show();
            });
        });
    });
    </script>
<main>