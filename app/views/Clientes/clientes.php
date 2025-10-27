<?php
// app/views/clientes/clientes.php
$clienteController = new ClienteController($db);
$clientes = $clienteController->listar(); // Esto ahora solo trae clientes activos
?>
<div class="container-fluid px-4" style="margin-top: 180px; margin-bottom: 50px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2"><i class="fas fa-users me-2"></i>Gestión de Clientes</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=crear_cliente" class="btn btn-neon">
                <i class="fas fa-plus me-2"></i>Nuevo Cliente
            </a>
            <a href="index.php?page=clientes_inactivos" class="btn btn-warning ms-2">
                <i class="fas fa-archive me-2"></i>Clientes Inactivos
            </a>
        </div>
    </div>

    <!-- Mostrar mensajes de éxito/error -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white py-3">
            <h6 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3 text-white">
                    <label for="filtroNombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="filtroNombre" placeholder="Buscar por nombre...">
                </div>
                <div class="col-md-2 text-white">
                    <label for="filtroIdentificacion" class="form-label">Identificación</label>
                    <input type="text" class="form-control" id="filtroIdentificacion" placeholder="Número...">
                </div>
                <div class="col-md-2 text-white">
                    <label for="filtroTelefono" class="form-label">Teléfono</label>
                    <input type="text" class="form-control" id="filtroTelefono" placeholder="Teléfono...">
                </div>
                <div class="col-md-3 text-white">
                    <label for="filtroEmail" class="form-label">Email</label>
                    <input type="text" class="form-control" id="filtroEmail" placeholder="Correo electrónico...">
                </div>
                <div class="col-md-2 text-white">
                    <label for="filtroCiudad" class="form-label">Ciudad</label>
                    <input type="text" class="form-control" id="filtroCiudad" placeholder="Ciudad...">
                </div>
                <div class="col-md-12 text-start">
                    <button type="button" class="btn btn-danger" id="limpiarFiltros">
                        <i class="fas fa-undo me-1"></i>Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Clientes -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0 text-white py-3">
                <i class="fas fa-list me-2"></i>Lista de Clientes Activos
                <span class="badge bg-success ms-2"><?= count($clientes) ?> clientes</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaClientes">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Identificación</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th>Ciudad</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyClientes">
                        <?php foreach ($clientes as $cliente): ?>
                        <tr data-cliente-id="<?= $cliente['id_cliente'] ?>" 
                            data-cliente-nombre="<?= htmlspecialchars($cliente['nombre_cliente']) ?>"
                            data-cliente-identificacion="<?= htmlspecialchars($cliente['identificacion']) ?>"
                            data-cliente-telefono="<?= htmlspecialchars($cliente['telefono']) ?>"
                            data-cliente-email="<?= htmlspecialchars($cliente['correo']) ?>"
                            data-cliente-direccion="<?= htmlspecialchars($cliente['direccion']) ?>"
                            data-cliente-ciudad="<?= htmlspecialchars($cliente['ciudad']) ?>">
                            <td><?= htmlspecialchars($cliente['nombre_cliente']) ?></td>
                            <td><?= htmlspecialchars($cliente['identificacion']) ?></td>
                            <td><?= htmlspecialchars($cliente['telefono']) ?></td>
                            <td><?= htmlspecialchars($cliente['correo']) ?></td>
                            <td><?= htmlspecialchars($cliente['direccion']) ?></td>
                            <td><?= htmlspecialchars($cliente['ciudad']) ?></td>
                            <td><?= date('d/m/Y', strtotime($cliente['fecha_registro'])) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=editar_cliente&id=<?= $cliente['id_cliente'] ?>" 
                                       class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-eliminar" 
                                            title="Desactivar Cliente" 
                                            data-cliente-id="<?= $cliente['id_cliente'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Mensaje cuando no hay resultados -->
                <div id="mensajeSinResultados" class="alert alert-info text-center d-none">
                    <i class="fas fa-search me-2"></i>
                    <strong>No se encontraron clientes</strong> que coincidan con los criterios de búsqueda.
                    <br>
                    <small class="text-muted">Intenta ajustar los filtros o limpiarlos para ver todos los clientes activos.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="modalEliminarCliente" tabindex="-1" aria-labelledby="modalEliminarClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarClienteLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Desactivar Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>¿Está seguro que desea desactivar este cliente?</strong>
                    <br>El cliente se marcará como inactivo y ya no aparecerá en la lista principal.
                </div>
                
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <h6 class="card-title text-dark mb-3">
                            <i class="fas fa-user me-2"></i>Información del Cliente
                        </h6>
                        
                        <div class="row border-bottom pb-2 mb-2">
                            <div class="col-6">
                                <strong class="text-dark">Nombre:</strong><br>
                                <span id="clienteNombre" class="text-muted"></span>
                            </div>
                            <div class="col-6">
                                <strong class="text-dark">Identificación:</strong><br>
                                <span id="clienteIdentificacion" class="text-muted"></span>
                            </div>
                        </div>
                        
                        <div class="row border-bottom pb-2 mb-2">
                            <div class="col-6">
                                <strong class="text-dark">Teléfono:</strong><br>
                                <span id="clienteTelefono" class="text-muted"></span>
                            </div>
                            <div class="col-6">
                                <strong class="text-dark">Email:</strong><br>
                                <span id="clienteEmail" class="text-muted"></span>
                            </div>
                        </div>
                        
                        <div class="row border-bottom pb-2 mb-2">
                            <div class="col-6">
                                <strong class="text-dark">Dirección:</strong><br>
                                <span id="clienteDireccion" class="text-muted"></span>
                            </div>
                            <div class="col-6">
                                <strong class="text-dark">Ciudad:</strong><br>
                                <span id="clienteCiudad" class="text-muted"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <a href="#" class="btn btn-danger" id="btnConfirmarEliminar">
                    <i class="fas fa-ban me-2"></i>Desactivar Cliente
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabla = document.getElementById('tablaClientes');
    const tbody = document.getElementById('tbodyClientes');
    const filas = tbody.getElementsByTagName('tr');
    const mensajeSinResultados = document.getElementById('mensajeSinResultados');
    
    // Inputs de filtro
    const filtroNombre = document.getElementById('filtroNombre');
    const filtroIdentificacion = document.getElementById('filtroIdentificacion');
    const filtroTelefono = document.getElementById('filtroTelefono');
    const filtroEmail = document.getElementById('filtroEmail');
    const filtroCiudad = document.getElementById('filtroCiudad');
    const btnLimpiar = document.getElementById('limpiarFiltros');
    
    // Modal elements
    const modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminarCliente'));
    const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
    
    // Función para verificar si hay filtros activos
    function hayFiltrosActivos() {
        return filtroNombre.value || filtroIdentificacion.value || filtroTelefono.value || 
               filtroEmail.value || filtroCiudad.value;
    }
    
    // Función para aplicar filtros
    function aplicarFiltros() {
        const nombre = filtroNombre.value.toLowerCase();
        const identificacion = filtroIdentificacion.value.toLowerCase();
        const telefono = filtroTelefono.value.toLowerCase();
        const email = filtroEmail.value.toLowerCase();
        const ciudad = filtroCiudad.value.toLowerCase();
        
        let resultadosVisibles = 0;
        
        for (let fila of filas) {
            const celdas = fila.getElementsByTagName('td');
            const textoNombre = celdas[0].textContent.toLowerCase();
            const textoIdentificacion = celdas[1].textContent.toLowerCase();
            const textoTelefono = celdas[2].textContent.toLowerCase();
            const textoEmail = celdas[3].textContent.toLowerCase();
            const textoDireccion = celdas[4].textContent.toLowerCase();
            const textoCiudad = celdas[5].textContent.toLowerCase();
            
            const coincideNombre = textoNombre.includes(nombre);
            const coincideIdentificacion = textoIdentificacion.includes(identificacion);
            const coincideTelefono = textoTelefono.includes(telefono);
            const coincideEmail = textoEmail.includes(email);
            const coincideDireccion = textoDireccion.includes(ciudad);
            const coincideCiudad = textoCiudad.includes(ciudad);
            
            // Mostrar fila solo si coincide con todos los filtros activos
            if (coincideNombre && coincideIdentificacion && coincideTelefono && 
                coincideEmail && (coincideDireccion || coincideCiudad)) {
                fila.style.display = '';
                resultadosVisibles++;
            } else {
                fila.style.display = 'none';
            }
        }
        
        // Mostrar u ocultar mensaje según resultados
        if (resultadosVisibles === 0 && hayFiltrosActivos()) {
            mensajeSinResultados.classList.remove('d-none');
        } else {
            mensajeSinResultados.classList.add('d-none');
        }
    }
    
    // Event listeners para filtros automáticos
    filtroNombre.addEventListener('input', aplicarFiltros);
    filtroIdentificacion.addEventListener('input', aplicarFiltros);
    filtroTelefono.addEventListener('input', aplicarFiltros);
    filtroEmail.addEventListener('input', aplicarFiltros);
    filtroCiudad.addEventListener('input', aplicarFiltros);
    
    // Limpiar filtros
    btnLimpiar.addEventListener('click', function() {
        filtroNombre.value = '';
        filtroIdentificacion.value = '';
        filtroTelefono.value = '';
        filtroEmail.value = '';
        filtroCiudad.value = '';
        aplicarFiltros();
        mensajeSinResultados.classList.add('d-none');
    });
    
    // Manejar eliminación de clientes
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function() {
            const clienteId = this.getAttribute('data-cliente-id');
            const fila = this.closest('tr');
            
            // Obtener datos del cliente
            const nombre = fila.getAttribute('data-cliente-nombre');
            const identificacion = fila.getAttribute('data-cliente-identificacion') || 'No especificada';
            const telefono = fila.getAttribute('data-cliente-telefono') || 'No especificado';
            const email = fila.getAttribute('data-cliente-email') || 'No especificado';
            const direccion = fila.getAttribute('data-cliente-direccion') || 'No especificada';
            const ciudad = fila.getAttribute('data-cliente-ciudad') || 'No especificada';
            
            // Actualizar modal con datos del cliente
            document.getElementById('clienteNombre').textContent = nombre;
            document.getElementById('clienteIdentificacion').textContent = identificacion;
            document.getElementById('clienteTelefono').textContent = telefono;
            document.getElementById('clienteEmail').textContent = email;
            document.getElementById('clienteDireccion').textContent = direccion;
            document.getElementById('clienteCiudad').textContent = ciudad;
            
            // Actualizar enlace de eliminación
            btnConfirmarEliminar.href = `index.php?page=eliminar_cliente&id=${clienteId}`;
            
            // Mostrar modal
            modalEliminar.show();
        });
    });
    
    // Aplicar filtros inicialmente para verificar estado
    aplicarFiltros();
});
</script>