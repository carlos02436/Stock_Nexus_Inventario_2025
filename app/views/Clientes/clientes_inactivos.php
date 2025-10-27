<?php
// app/views/clientes/clientes_inactivos.php
$clienteController = new ClienteController($db);
$clientesInactivos = $clienteController->listarInactivos();
?>
<div class="container-fluid px-4" style="margin-top: 180px; margin-bottom: 50px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2"><i class="fas fa-archive me-2"></i>Clientes Inactivos</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=clientes" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Clientes
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

    <!-- Tabla de Clientes Inactivos -->
    <div class="card shadow-sm">
        <div class="card-header text-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Lista de Clientes Inactivos
                <span class="badge bg-warning ms-2" id="contadorClientes"><?= count($clientesInactivos) ?> clientes</span>
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($clientesInactivos)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>No hay clientes inactivos</strong>
                    <br>
                    <small class="text-muted">Todos los clientes están activos en el sistema.</small>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaClientesInactivos">
                        <thead class="table-dark">
                            <tr>
                                <th>Nombre</th>
                                <th>Identificación</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Dirección</th>
                                <th>Ciudad</th>
                                <th>Fecha Registro</th>
                                <th>Última Actualización</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyClientesInactivos">
                            <?php foreach ($clientesInactivos as $cliente): ?>
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
                                    <?php 
                                    // Mostrar fecha de última actualización si existe
                                    if (isset($cliente['fecha_actualizacion']) && !empty($cliente['fecha_actualizacion'])) {
                                        echo date('d/m/Y H:i', strtotime($cliente['fecha_actualizacion']));
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="index.php?page=activar_cliente&id=<?= $cliente['id_cliente'] ?>" 
                                           class="btn btn-success btn-activar" 
                                           title="Activar Cliente">                                           
                                            <i class="fas fa-redo me-1"></i>Activar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mensaje cuando no hay resultados en la búsqueda -->
                <div id="mensajeSinResultados" class="alert alert-warning text-center d-none mt-3">
                    <i class="fas fa-search me-2"></i>
                    <strong>No se encontraron clientes inactivos</strong> que coincidan con los filtros aplicados.
                    <br>
                    <small class="text-muted">Intenta ajustar los filtros o limpialos para ver todos los clientes inactivos.</small>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Activación -->
<div class="modal fade" id="modalActivarCliente" tabindex="-1" aria-labelledby="modalActivarClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalActivarClienteLabel">
                    <i class="fas fa-redo me-2"></i>Confirmar Activación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>¿Está seguro que desea reactivar este cliente?</strong>
                    <br>El cliente volverá a aparecer en la lista de clientes activos.
                </div>
                
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <h6 class="card-title text-dark mb-3">
                            <i class="fas fa-user me-2"></i>Información del Cliente
                        </h6>
                        
                        <div class="row border-bottom pb-2 mb-2">
                            <div class="col-6">
                                <strong class="text-dark">Nombre:</strong><br>
                                <span id="clienteInactivoNombre" class="text-muted"></span>
                            </div>
                            <div class="col-6">
                                <strong class="text-dark">Identificación:</strong><br>
                                <span id="clienteInactivoIdentificacion" class="text-muted"></span>
                            </div>
                        </div>
                        
                        <div class="row border-bottom pb-2 mb-2">
                            <div class="col-6">
                                <strong class="text-dark">Teléfono:</strong><br>
                                <span id="clienteInactivoTelefono" class="text-muted"></span>
                            </div>
                            <div class="col-6">
                                <strong class="text-dark">Email:</strong><br>
                                <span id="clienteInactivoEmail" class="text-muted"></span>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-6">
                                <strong class="text-dark">Ciudad:</strong><br>
                                <span id="clienteInactivoCiudad" class="text-muted"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <a href="#" class="btn btn-success" id="btnConfirmarActivacion">
                    <i class="fas fa-redo me-2"></i>Activar Cliente
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inputs de filtro
    const filtroNombre = document.getElementById('filtroNombre');
    const filtroIdentificacion = document.getElementById('filtroIdentificacion');
    const filtroTelefono = document.getElementById('filtroTelefono');
    const filtroEmail = document.getElementById('filtroEmail');
    const filtroCiudad = document.getElementById('filtroCiudad');
    const btnLimpiar = document.getElementById('limpiarFiltros');
    
    // Elementos de la tabla
    const tbody = document.getElementById('tbodyClientesInactivos');
    const filas = tbody ? tbody.getElementsByTagName('tr') : [];
    const mensajeSinResultados = document.getElementById('mensajeSinResultados');
    const contadorClientes = document.getElementById('contadorClientes');
    
    // Modal elements para activación
    const modalActivar = new bootstrap.Modal(document.getElementById('modalActivarCliente'));
    const btnConfirmarActivacion = document.getElementById('btnConfirmarActivacion');
    
    // Función para verificar si hay filtros activos
    function hayFiltrosActivos() {
        return filtroNombre.value || filtroIdentificacion.value || filtroTelefono.value || 
               filtroEmail.value || filtroCiudad.value;
    }
    
    // Función para aplicar filtros
    function aplicarFiltros() {
        if (filas.length === 0) return;
        
        const nombre = filtroNombre.value.toLowerCase();
        const identificacion = filtroIdentificacion.value.toLowerCase();
        const telefono = filtroTelefono.value.toLowerCase();
        const email = filtroEmail.value.toLowerCase();
        const ciudad = filtroCiudad.value.toLowerCase();
        
        let resultadosVisibles = 0;
        
        for (let fila of filas) {
            const celdas = fila.getElementsByTagName('td');
            
            // Obtener valores de cada columna
            const textoNombre = celdas[0].textContent.toLowerCase();
            const textoIdentificacion = celdas[1].textContent.toLowerCase();
            const textoTelefono = celdas[2].textContent.toLowerCase();
            const textoEmail = celdas[3].textContent.toLowerCase();
            const textoDireccion = celdas[4].textContent.toLowerCase();
            const textoCiudad = celdas[5].textContent.toLowerCase();
            
            // Aplicar filtros (AND entre diferentes campos)
            const coincideNombre = !nombre || textoNombre.includes(nombre);
            const coincideIdentificacion = !identificacion || textoIdentificacion.includes(identificacion);
            const coincideTelefono = !telefono || textoTelefono.includes(telefono);
            const coincideEmail = !email || textoEmail.includes(email);
            const coincideCiudad = !ciudad || textoCiudad.includes(ciudad) || textoDireccion.includes(ciudad);
            
            // Mostrar fila solo si coincide con todos los filtros activos
            if (coincideNombre && coincideIdentificacion && coincideTelefono && 
                coincideEmail && coincideCiudad) {
                fila.style.display = '';
                resultadosVisibles++;
            } else {
                fila.style.display = 'none';
            }
        }
        
        // Actualizar contador
        if (contadorClientes) {
            contadorClientes.textContent = `${resultadosVisibles} clientes`;
            // Cambiar color del badge según resultados
            if (resultadosVisibles === 0 && hayFiltrosActivos()) {
                contadorClientes.className = 'badge bg-danger ms-2';
            } else {
                contadorClientes.className = 'badge bg-warning ms-2';
            }
        }
        
        // Mostrar u ocultar mensaje de no resultados
        if (mensajeSinResultados) {
            if (resultadosVisibles === 0 && hayFiltrosActivos()) {
                mensajeSinResultados.classList.remove('d-none');
            } else {
                mensajeSinResultados.classList.add('d-none');
            }
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
        filtroNombre.focus();
    });
    
    // Permitir limpiar con Escape en cualquier campo
    const inputsFiltro = [filtroNombre, filtroIdentificacion, filtroTelefono, filtroEmail, filtroCiudad];
    inputsFiltro.forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                aplicarFiltros();
            }
        });
    });
    
    // Manejar activación de clientes con modal
    document.querySelectorAll('.btn-activar').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const clienteId = this.getAttribute('href').split('=')[2];
            const fila = this.closest('tr');
            
            // Obtener datos del cliente
            const nombre = fila.getAttribute('data-cliente-nombre');
            const identificacion = fila.getAttribute('data-cliente-identificacion') || 'No especificada';
            const telefono = fila.getAttribute('data-cliente-telefono') || 'No especificado';
            const email = fila.getAttribute('data-cliente-email') || 'No especificado';
            const ciudad = fila.getAttribute('data-cliente-ciudad') || 'No especificada';
            
            // Actualizar modal con datos del cliente
            document.getElementById('clienteInactivoNombre').textContent = nombre;
            document.getElementById('clienteInactivoIdentificacion').textContent = identificacion;
            document.getElementById('clienteInactivoTelefono').textContent = telefono;
            document.getElementById('clienteInactivoEmail').textContent = email;
            document.getElementById('clienteInactivoCiudad').textContent = ciudad;
            
            // Actualizar enlace de activación
            btnConfirmarActivacion.href = `index.php?page=activar_cliente&id=${clienteId}`;
            
            // Mostrar modal
            modalActivar.show();
        });
    });
    
    // Aplicar filtros inicialmente para verificar estado
    aplicarFiltros();
});
</script>