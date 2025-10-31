<?php
// app/views/proveedores/proveedores.php
$proveedorController = new ProveedorController($db);
$proveedores = $proveedorController->listar();
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-truck me-2"></i>Gestión de Proveedores</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=crear_proveedor" class="btn btn-neon rounded-3 px-3 py-2">
                <i class="fas fa-plus me-2"></i>Nuevo Proveedor
            </a>
        </div>
    </div>
        <!-- Filtros para Proveedores -->
        <div class="card py-3 mb-4">
            <div class="card-header text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                </h5>
            </div>
            <div class="card-body">
                <form class="row g-3" id="formFiltrosProveedores">
                    <div class="col-md-3">
                        <label for="filtroNombre" class="form-label text-white">Nombre</label>
                        <input type="text" id="filtroNombre" class="form-control text-black border-0" 
                            placeholder="Nombre proveedor...">
                    </div>
                    <div class="col-md-2">
                        <label for="filtroNIT" class="form-label text-white">NIT</label>
                        <input type="text" id="filtroNIT" class="form-control text-black border-0" 
                            placeholder="NIT...">
                    </div>
                    <div class="col-md-2">
                        <label for="filtroTelefono" class="form-label text-white">Teléfono</label>
                        <input type="text" id="filtroTelefono" class="form-control text-black border-0" 
                            placeholder="Teléfono...">
                    </div>
                    <div class="col-md-3">
                        <label for="filtroEmail" class="form-label text-white">Email</label>
                        <input type="text" id="filtroEmail" class="form-control text-black border-0" 
                            placeholder="Email...">
                    </div>
                    <div class="col-md-2">
                        <label for="filtroCiudad" class="form-label text-white">Ciudad</label>
                        <input type="text" id="filtroCiudad" class="form-control text-black border-0" 
                            placeholder="Ciudad...">
                    </div>
                    <div class="col-md-12 d-flex justify-content-start">
                        <button type="button" id="btnLimpiarFiltrosProveedores" class="btn btn-danger">
                            <i class="fas fa-undo me-1"></i>Limpiar filtros
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <div class="card shadow-sm">
        <div class="card-header text-white py-4">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Lista de Proveedores
            </h5>
        </div>

        <!-- Tabla de Proveedores -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaProveedores">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>NIT</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Ciudad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proveedores as $proveedor): ?>
                        <tr>
                            <td><?= $proveedor['nombre_proveedor'] ?></td>
                            <td><?= $proveedor['nit'] ?></td>
                            <td><?= $proveedor['telefono'] ?></td>
                            <td><?= $proveedor['correo'] ?></td>
                            <td><?= $proveedor['ciudad'] ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=editar_proveedor&id=<?= $proveedor['id_proveedor'] ?>" 
                                       class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?page=eliminar_proveedor&id=<?= $proveedor['id_proveedor'] ?>" 
                                       class="btn btn-danger btn-delete" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener elementos de filtro
    const filtroNombre = document.getElementById('filtroNombre');
    const filtroNIT = document.getElementById('filtroNIT');
    const filtroTelefono = document.getElementById('filtroTelefono');
    const filtroEmail = document.getElementById('filtroEmail');
    const filtroCiudad = document.getElementById('filtroCiudad');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltrosProveedores');
    const tablaProveedores = document.getElementById('tablaProveedores');
    const tbodyProveedores = tablaProveedores.getElementsByTagName('tbody')[0];
    const filasProveedores = tbodyProveedores.getElementsByTagName('tr');
    
    // Obtener el número de columnas de la tabla
    const thead = tablaProveedores.getElementsByTagName('thead')[0];
    const columnCount = thead.getElementsByTagName('th').length;
    
    // Crear elemento para mensaje de no resultados
    const mensajeNoResultados = document.createElement('tr');
    mensajeNoResultados.id = 'mensajeNoResultados';
    mensajeNoResultados.style.display = 'none';
    mensajeNoResultados.innerHTML = `
        <td colspan="${columnCount}" style="text-align: center; padding: 20px;
            font-size: 16px; font-weight: bold; color: #666;
            background-color: #f9f9f9; border: 1px solid #ddd;">
            No hay resultados para la Búsqueda
        </td>
    `;
    tbodyProveedores.appendChild(mensajeNoResultados);
    
    // Función para aplicar filtros
    function aplicarFiltros() {
        const valorNombre = filtroNombre.value.toLowerCase();
        const valorNIT = filtroNIT.value.toLowerCase();
        const valorTelefono = filtroTelefono.value.toLowerCase();
        const valorEmail = filtroEmail.value.toLowerCase();
        const valorCiudad = filtroCiudad.value.toLowerCase();
        
        let resultadosVisibles = 0;
        
        // Recorrer todas las filas de la tabla (excluyendo el mensaje)
        for (let i = 0; i < filasProveedores.length; i++) {
            const fila = filasProveedores[i];
            
            // Saltar la fila del mensaje de no resultados
            if (fila.id === 'mensajeNoResultados') continue;
            
            const celdas = fila.getElementsByTagName('td');
            
            const nombre = celdas[0].textContent.toLowerCase();
            const nit = celdas[1].textContent.toLowerCase();
            const telefono = celdas[2].textContent.toLowerCase();
            const email = celdas[3].textContent.toLowerCase();
            const ciudad = celdas[4].textContent.toLowerCase();
            
            // Verificar si la fila coincide con los filtros
            const coincideNombre = nombre.includes(valorNombre);
            const coincideNIT = nit.includes(valorNIT);
            const coincideTelefono = telefono.includes(valorTelefono);
            const coincideEmail = email.includes(valorEmail);
            const coincideCiudad = ciudad.includes(valorCiudad);
            
            // Mostrar u ocultar la fila según los filtros
            if (coincideNombre && coincideNIT && coincideTelefono && coincideEmail && coincideCiudad) {
                fila.style.display = '';
                resultadosVisibles++;
            } else {
                fila.style.display = 'none';
            }
        }
        
        // Mostrar u ocultar mensaje de no resultados
        const mensaje = document.getElementById('mensajeNoResultados');
        if (resultadosVisibles === 0) {
            mensaje.style.display = '';
        } else {
            mensaje.style.display = 'none';
        }
    }
    
    // Agregar event listeners a los campos de filtro
    filtroNombre.addEventListener('input', aplicarFiltros);
    filtroNIT.addEventListener('input', aplicarFiltros);
    filtroTelefono.addEventListener('input', aplicarFiltros);
    filtroEmail.addEventListener('input', aplicarFiltros);
    filtroCiudad.addEventListener('input', aplicarFiltros);
    
    // Limpiar filtros
    btnLimpiarFiltros.addEventListener('click', function() {
        filtroNombre.value = '';
        filtroNIT.value = '';
        filtroTelefono.value = '';
        filtroEmail.value = '';
        filtroCiudad.value = '';
        aplicarFiltros();
    });
});
</script>