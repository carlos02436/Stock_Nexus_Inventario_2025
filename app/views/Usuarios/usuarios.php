<?php
// app/views/usuarios/usuarios.php
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}

$usuarioController = new UsuarioController($db);
$usuarios = $usuarioController->listar();
?>
<div class="container-fluid px-4 mb-5" style="margin-top:180px;">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-user-cog me-2"></i>Gestión de Usuarios</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=crear_usuario" class="btn btn-neon">
                <i class="fas fa-plus me-2"></i>Nuevo Usuario
            </a>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card mb-4">
        <div class="card-header text-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3 text-white">
                    <label for="filtroNombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="filtroNombre" placeholder="Buscar por nombre...">
                </div>
                <div class="col-md-2 text-white">
                    <label for="filtroUsuario" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="filtroUsuario" placeholder="Buscar usuario...">
                </div>
                <div class="col-md-3 text-white">
                    <label for="filtroEmail" class="form-label">Email</label>
                    <input type="text" class="form-control" id="filtroEmail" placeholder="Buscar por email...">
                </div>
                <div class="col-md-2 text-white">
                    <label for="filtroRol" class="form-label">Rol</label>
                    <select class="form-select" id="filtroRol">
                        <option value="">Todos</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Vendedor">Vendedor</option>
                        <option value="Contador">Contador</option>
                        <option value="Bodeguero">Bodeguero</option>
                    </select>
                </div>
                <div class="col-md-2 text-white">
                    <label for="filtroEstado" class="form-label">Estado</label>
                    <select class="form-select" id="filtroEstado">
                        <option value="">Todos</option>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-white" id="contadorResultados">
                            Mostrando <?= count($usuarios) ?> usuario(s)
                        </small>
                        <button type="button" class="btn btn-danger" id="btnLimpiarFiltros">
                            <i class="fas fa-undo me-1"></i>Limpiar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header text-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Lista de Usuarios
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaUsuarios">
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr class="fila-usuario" 
                            data-nombre="<?= htmlspecialchars(strtolower($usuario['nombre_completo'])) ?>"
                            data-usuario="<?= htmlspecialchars(strtolower($usuario['usuario'])) ?>"
                            data-email="<?= htmlspecialchars(strtolower($usuario['correo'])) ?>"
                            data-rol="<?= htmlspecialchars($usuario['rol']) ?>"
                            data-estado="<?= htmlspecialchars($usuario['estado']) ?>">
                            <td><?= htmlspecialchars($usuario['nombre_completo']) ?></td>
                            <td><?= htmlspecialchars($usuario['usuario']) ?></td>
                            <td><?= htmlspecialchars($usuario['correo']) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $usuario['rol'] == 'Administrador' ? 'danger' : 
                                    ($usuario['rol'] == 'Vendedor' ? 'primary' : 
                                    ($usuario['rol'] == 'Contador' ? 'info' : 'warning'))
                                ?>">
                                    <?= htmlspecialchars($usuario['rol']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $usuario['estado'] == 'Activo' ? 'success' : 'secondary' ?>">
                                    <?= htmlspecialchars($usuario['estado']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=editar_usuario&id=<?= $usuario['id_usuario'] ?>" 
                                       class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($usuario['id_usuario'] != $_SESSION['usuario_id']): ?>
                                    <button type="button" class="btn btn-danger btn-inactivar" 
                                            data-bs-toggle="modal" data-bs-target="#modalInactivar"
                                            data-usuario-id="<?= $usuario['id_usuario'] ?>"
                                            data-usuario-nombre="<?= htmlspecialchars($usuario['nombre_completo']) ?>"
                                            data-usuario-estado="<?= htmlspecialchars($usuario['estado']) ?>"
                                            title="<?= $usuario['estado'] == 'Activo' ? 'Inactivar Usuario' : 'Activar Usuario' ?>">
                                        <i class="fas <?= $usuario['estado'] == 'Activo' ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <!-- Mensaje cuando no hay resultados -->
                        <tr id="mensajeSinResultados" style="display: none;">
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-search fa-2x mb-3"></i>
                                    <h5>No se encontraron usuarios</h5>
                                    <p>No hay resultados que coincidan con los filtros aplicados.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Inactivar/Activar Usuario -->
<div class="modal fade" id="modalInactivar" tabindex="-1" aria-labelledby="modalInactivarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="modalInactivarLabel">Cambiar Estado del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4 text-dark">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 id="modalMensaje">¿Está seguro que desea inactivar este usuario?</h5>
                    <p class="text-muted" id="modalDetalles">
                        El usuario <strong id="usuarioNombre"></strong> será <span id="accionEstado">inactivado</span> 
                        y no podrá acceder al sistema.
                    </p>
                </div>
                <div class="alert alert-info">
                    <small>
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="modalInfo">
                            Al inactivar un usuario, este no podrá iniciar sesión en el sistema 
                            pero todos sus datos se mantendrán en la base de datos.
                        </span>
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <form id="formInactivar" method="POST" action="index.php?page=inactivar_usuario">
                    <input type="hidden" name="id" id="usuarioId">
                    <input type="hidden" name="accion" id="accionTipo">
                    <button type="submit" class="btn btn-danger" id="btnConfirmarAccion">
                        <i class="fas fa-check me-2"></i>Confirmar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const filtroNombre = document.getElementById('filtroNombre');
    const filtroUsuario = document.getElementById('filtroUsuario');
    const filtroEmail = document.getElementById('filtroEmail');
    const filtroRol = document.getElementById('filtroRol');
    const filtroEstado = document.getElementById('filtroEstado');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    const contadorResultados = document.getElementById('contadorResultados');
    const mensajeSinResultados = document.getElementById('mensajeSinResultados');
    const filasUsuarios = document.querySelectorAll('.fila-usuario');
    
    // Modal elements
    const modalInactivar = document.getElementById('modalInactivar');
    const modalMensaje = document.getElementById('modalMensaje');
    const modalDetalles = document.getElementById('modalDetalles');
    const usuarioNombre = document.getElementById('usuarioNombre');
    const accionEstado = document.getElementById('accionEstado');
    const modalInfo = document.getElementById('modalInfo');
    const usuarioId = document.getElementById('usuarioId');
    const accionTipo = document.getElementById('accionTipo');
    const btnConfirmarAccion = document.getElementById('btnConfirmarAccion');
    const formInactivar = document.getElementById('formInactivar');

    // Función para configurar el modal de inactivar/activar
    function configurarModalInactivar(button) {
        const usuarioId = button.getAttribute('data-usuario-id');
        const usuarioNombre = button.getAttribute('data-usuario-nombre');
        const usuarioEstado = button.getAttribute('data-usuario-estado');
        
        const esActivo = usuarioEstado === 'Activo';
        const nuevaAccion = esActivo ? 'inactivar' : 'activar';
        const textoAccion = esActivo ? 'inactivar' : 'activar';
        const textoEstado = esActivo ? 'inactivado' : 'activado';
        const textoBoton = esActivo ? 'Inactivar' : 'Activar';
        const claseBoton = esActivo ? 'btn-danger' : 'btn-success';
        
        // Actualizar contenido del modal
        modalMensaje.textContent = `¿Está seguro que desea ${textoAccion} este usuario?`;
        usuarioNombre.textContent = usuarioNombre;
        accionEstado.textContent = textoEstado;
        
        modalInfo.innerHTML = esActivo ? 
            'Al inactivar un usuario, este no podrá iniciar sesión en el sistema pero todos sus datos se mantendrán en la base de datos.' :
            'Al activar un usuario, este podrá volver a iniciar sesión en el sistema con sus credenciales actuales.';
        
        // Actualizar formulario
        document.getElementById('usuarioId').value = usuarioId;
        document.getElementById('accionTipo').value = nuevaAccion;
        
        // Actualizar botón de confirmación
        btnConfirmarAccion.textContent = textoBoton;
        btnConfirmarAccion.className = `btn ${claseBoton}`;
        btnConfirmarAccion.innerHTML = `<i class="fas fa-check me-2"></i>${textoBoton}`;
    }

    // Event listeners para botones de inactivar/activar
    const botonesInactivar = document.querySelectorAll('.btn-inactivar');
    botonesInactivar.forEach(button => {
        button.addEventListener('click', function() {
            configurarModalInactivar(this);
        });
    });

    // Función para filtrar usuarios
    function filtrarUsuarios() {
        const filtroNombreVal = filtroNombre.value.toLowerCase();
        const filtroUsuarioVal = filtroUsuario.value.toLowerCase();
        const filtroEmailVal = filtroEmail.value.toLowerCase();
        const filtroRolVal = filtroRol.value;
        const filtroEstadoVal = filtroEstado.value;
        
        let usuariosVisibles = 0;
        
        filasUsuarios.forEach(fila => {
            const nombre = fila.getAttribute('data-nombre');
            const usuario = fila.getAttribute('data-usuario');
            const email = fila.getAttribute('data-email');
            const rol = fila.getAttribute('data-rol');
            const estado = fila.getAttribute('data-estado');
            
            // Aplicar filtros
            const coincideNombre = !filtroNombreVal || nombre.includes(filtroNombreVal);
            const coincideUsuario = !filtroUsuarioVal || usuario.includes(filtroUsuarioVal);
            const coincideEmail = !filtroEmailVal || email.includes(filtroEmailVal);
            const coincideRol = !filtroRolVal || rol === filtroRolVal;
            const coincideEstado = !filtroEstadoVal || estado === filtroEstadoVal;
            
            if (coincideNombre && coincideUsuario && coincideEmail && coincideRol && coincideEstado) {
                fila.style.display = '';
                usuariosVisibles++;
            } else {
                fila.style.display = 'none';
            }
        });
        
        // Actualizar contador de resultados
        actualizarContadorResultados(usuariosVisibles);
        
        // Mostrar/ocultar mensaje de no resultados
        if (usuariosVisibles === 0) {
            mensajeSinResultados.style.display = '';
        } else {
            mensajeSinResultados.style.display = 'none';
        }
    }
    
    // Función para actualizar el contador de resultados
    function actualizarContadorResultados(visibles) {
        const total = filasUsuarios.length;
        
        if (visibles === total) {
            contadorResultados.textContent = `Mostrando ${total} usuario(s)`;
            contadorResultados.className = 'text-white';
        } else if (visibles === 0) {
            contadorResultados.textContent = 'No se encontraron usuarios';
            contadorResultados.className = 'text-white';
        } else {
            contadorResultados.textContent = `Mostrando ${visibles} de ${total} usuario(s)`;
            contadorResultados.className = 'text-white';
        }
    }
    
    // Función para limpiar filtros
    function limpiarFiltros() {
        filtroNombre.value = '';
        filtroUsuario.value = '';
        filtroEmail.value = '';
        filtroRol.value = '';
        filtroEstado.value = '';
        filtrarUsuarios();
    }
    
    // Event listeners para filtros (búsqueda automática)
    filtroNombre.addEventListener('input', filtrarUsuarios);
    filtroUsuario.addEventListener('input', filtrarUsuarios);
    filtroEmail.addEventListener('input', filtrarUsuarios);
    filtroRol.addEventListener('change', filtrarUsuarios);
    filtroEstado.addEventListener('change', filtrarUsuarios);
    
    // Event listener para botón limpiar filtros
    btnLimpiarFiltros.addEventListener('click', limpiarFiltros);
    
    // Inicializar contador
    actualizarContadorResultados(filasUsuarios.length);
});
</script>