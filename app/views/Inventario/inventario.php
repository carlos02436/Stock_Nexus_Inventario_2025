<?php
// app/views/inventario/inventario.php
$productoController = new ProductoController($db);
$productos = $productoController->listar();
$inventarioController = new InventarioController($db);
$valorTotal = $inventarioController->getValorTotalInventario();
$stockBajo = $inventarioController->getProductosConStockBajo();

// Obtener categorías únicas para el filtro
$categoriasUnicas = [];
foreach ($productos as $producto) {
    $categoria = $producto['nombre_categoria'] ?: 'Sin categoría';
    if (!in_array($categoria, $categoriasUnicas)) {
        $categoriasUnicas[] = $categoria;
    }
}
sort($categoriasUnicas);
?>

<div class="container-fluid px-4 pb-5">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
            style="margin-top:180px;">
        <h1 class="h2"><i class="fas fa-warehouse me-2"></i>Gestión de Inventario</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=crear_producto" class="btn btn-neon me-2">
                <i class="fas fa-plus me-2"></i>Nuevo Producto
            </a>
            <a href="index.php?page=movimientos" class="btn btn-warning">
                <i class="fas fa-exchange-alt me-2"></i>Ver Movimientos
            </a>
        </div>
    </div>
    
    <!-- Resumen del Inventario -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2"
                 style="border-left: 4px solid #4e73df !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white mb-1">
                                Total Productos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                <?= count($productos) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2"
                style="border-left: 4px solid #00ff33ff !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white mb-1">
                                Stock Bajo
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                <?= count($stockBajo) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2"
                 style="border-left: 4px solid #ff0040ff !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white mb-1">
                                Valor Inventario
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                $<?= number_format($valorTotal, 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2"
                 style="border-left: 4px solid #dcff19ff !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white mb-1">
                                Productos Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-white">
                                <?= count(array_filter($productos, function($p) { return $p['estado'] == 'Activo'; })) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas de Stock Bajo -->
    <?php if (!empty($stockBajo)): ?>
    <div class="alert alert-warning mb-4">
        <h5><i class="fas fa-exclamation-triangle me-2"></i>Alertas de Stock Bajo</h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Stock Actual</th>
                        <th>Stock Mínimo</th>
                        <th>Diferencia</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stockBajo as $producto): ?>
                    <tr>
                        <td><?= $producto['nombre_producto'] ?></td>
                        <td><?= $producto['stock_actual'] ?></td>
                        <td><?= $producto['stock_minimo'] ?></td>
                        <td>
                            <span class="badge bg-danger"><?= $producto['diferencia'] ?></span>
                        </td>
                        <td>
                            <a href="index.php?page=editar_producto&id=<?= $producto['id_producto'] ?>" 
                               class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Reabastecer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filtros de Búsqueda -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title mb-0 text-white">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="filtroCodigo" class="form-label text-white">Código</label>
                    <input type="text" class="form-control" id="filtroCodigo" placeholder="Buscar por código...">
                </div>
                <div class="col-md-3">
                    <label for="filtroNombre" class="form-label text-white">Nombre</label>
                    <input type="text" class="form-control" id="filtroNombre" placeholder="Buscar por nombre...">
                </div>
                <div class="col-md-3">
                    <label for="filtroCategoria" class="form-label text-white">Categoría</label>
                    <select class="form-select" id="filtroCategoria">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categoriasUnicas as $categoria): ?>
                            <option value="<?= htmlspecialchars($categoria) ?>"><?= htmlspecialchars($categoria) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroEstado" class="form-label text-white">Estado</label>
                    <select class="form-select" id="filtroEstado">
                        <option value="">Todos los estados</option>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="button" class="btn btn-danger" id="btnLimpiarFiltros">
                        <i class="fas fa-undo me-1"></i>Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Productos -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-white">
                <i class="fas fa-list me-2 text-white"></i>Lista de Productos en Inventario
            </h5>
            <div class="text-muted small" id="contadorProductos">
                Mostrando <?= count($productos) ?> productos
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaInventario">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Stock</th>
                            <th>Precio Compra</th>
                            <th>Precio Venta</th>
                            <th>Valor en Inventario</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTabla">
                        <?php foreach ($productos as $producto): 
                            $valorInventario = $producto['stock_actual'] * $producto['precio_compra'];
                        ?>
                        <tr data-estado="<?= $producto['estado'] ?>">
                            <td><?= $producto['codigo_producto'] ?></td>
                            <td><?= $producto['nombre_producto'] ?></td>
                            <td><?= $producto['nombre_categoria'] ?: 'Sin categoría' ?></td>
                            <td>
                                <span class="badge bg-<?= $producto['stock_actual'] <= $producto['stock_minimo'] ? 'danger' : ($producto['stock_actual'] <= ($producto['stock_minimo'] * 2) ? 'warning' : 'success') ?>">
                                    <?= $producto['stock_actual'] ?>
                                </span>
                            </td>
                            <td>$<?= number_format($producto['precio_compra'], 2) ?></td>
                            <td>$<?= number_format($producto['precio_venta'], 2) ?></td>
                            <td><strong>$<?= number_format($valorInventario, 2) ?></strong></td>
                            <td>
                                <span class="badge bg-<?= $producto['estado'] == 'Activo' ? 'success' : 'secondary' ?>">
                                    <?= $producto['estado'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=editar_producto&id=<?= $producto['id_producto'] ?>" 
                                    class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- CAMBIA ESTE BOTÓN -->
                                    <button type="button" class="btn btn-danger btn-eliminar" 
                                            data-id="<?= $producto['id_producto'] ?>" 
                                            data-nombre="<?= htmlspecialchars($producto['nombre_producto']) ?>"
                                            title="Eliminar Permanentemente">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

<!-- Modal de Confirmación para Eliminación Física Permanente -->
<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-labelledby="modalConfirmarEliminarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header del Modal -->
            <div class="modal-header bg-danger text-white border-0">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-lg me-2"></i>
                    <h5 class="modal-title fw-bold" id="modalConfirmarEliminarLabel">Eliminación Permanente</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Body del Modal -->
            <div class="modal-body py-4">
                <div class="text-center mb-3">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i class="fas fa-trash-alt fa-2x text-danger"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-2">¿Eliminar permanentemente este producto?</h6>
                    <p class="text-muted mb-3">Producto: <span class="fw-bold text-dark" id="nombreProductoEliminar"></span></p>
                </div>
                
                <div class="alert alert-danger border-danger bg-danger bg-opacity-10 mb-0">
                    <div class="d-flex">
                        <i class="fas fa-exclamation-triangle text-danger me-2 mt-1"></i>
                        <div>
                            <small class="text-danger fw-semibold">ELIMINACIÓN PERMANENTE</small>
                            <p class="text-dark mb-0 small">Esta acción eliminará completamente el producto y todos sus movimientos del sistema. Esta operación no se puede deshacer y los datos no podrán recuperarse.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer del Modal -->
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger px-4" id="btnConfirmarEliminar">
                    <i class="fas fa-trash me-2"></i>Eliminar Permanentemente
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables para los filtros
    let filtroCodigo = '';
    let filtroNombre = '';
    let filtroCategoria = '';
    let filtroEstado = '';
    
    // Obtener todas las filas de la tabla
    const filas = document.querySelectorAll('#cuerpoTabla tr');
    
    // Función para aplicar filtros
    function aplicarFiltros() {
        let productosVisibles = 0;
        
        filas.forEach(fila => {
            const celdas = fila.querySelectorAll('td');
            const codigo = celdas[0].textContent.toLowerCase();
            const nombre = celdas[1].textContent.toLowerCase();
            const categoria = celdas[2].textContent;
            // Obtener el estado del data attribute
            const estado = fila.getAttribute('data-estado');
            
            let mostrarFila = true;
            
            // Filtrar por código (búsqueda parcial)
            if (filtroCodigo && !codigo.includes(filtroCodigo.toLowerCase())) {
                mostrarFila = false;
            }
            
            // Filtrar por nombre (búsqueda parcial)
            if (filtroNombre && !nombre.includes(filtroNombre.toLowerCase())) {
                mostrarFila = false;
            }
            
            // Filtrar por categoría (búsqueda exacta)
            if (filtroCategoria && categoria !== filtroCategoria) {
                mostrarFila = false;
            }
            
            // Filtrar por estado (búsqueda exacta)
            if (filtroEstado && estado !== filtroEstado) {
                mostrarFila = false;
            }
            
            // Mostrar u ocultar fila
            if (mostrarFila) {
                fila.style.display = '';
                productosVisibles++;
            } else {
                fila.style.display = 'none';
            }
        });
        
        // Actualizar contador
        actualizarContador(productosVisibles);
    }
    
    // Función para actualizar el contador
    function actualizarContador(visibles) {
        const total = filas.length;
        const contador = document.getElementById('contadorProductos');
        
        if (visibles === total) {
            contador.textContent = `Mostrando ${total} productos`;
        } else {
            contador.textContent = `Mostrando ${visibles} de ${total} productos`;
        }
    }
    
    // Event listeners para los filtros - INPUT (tiempo real)
    document.getElementById('filtroCodigo').addEventListener('input', function(e) {
        filtroCodigo = e.target.value;
        aplicarFiltros();
    });
    
    document.getElementById('filtroNombre').addEventListener('input', function(e) {
        filtroNombre = e.target.value;
        aplicarFiltros();
    });
    
    // Event listeners para los SELECT (cambio inmediato)
    document.getElementById('filtroCategoria').addEventListener('change', function(e) {
        filtroCategoria = e.target.value;
        aplicarFiltros();
    });
    
    document.getElementById('filtroEstado').addEventListener('change', function(e) {
        filtroEstado = e.target.value;
        aplicarFiltros();
    });
    
    // Limpiar filtros
    document.getElementById('btnLimpiarFiltros').addEventListener('click', function() {
        // Limpiar inputs
        document.getElementById('filtroCodigo').value = '';
        document.getElementById('filtroNombre').value = '';
        document.getElementById('filtroCategoria').value = '';
        document.getElementById('filtroEstado').value = '';
        
        // Resetear variables de filtro
        filtroCodigo = '';
        filtroNombre = '';
        filtroCategoria = '';
        filtroEstado = '';
        
        // Aplicar filtros (mostrar todo)
        aplicarFiltros();
    });

    // ===== CÓDIGO PARA ELIMINACIÓN FÍSICA DE PRODUCTOS =====
    let productoAEliminar = null;
    const modalEliminar = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));

    // Event listener para botones eliminar
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-eliminar')) {
            const boton = e.target.closest('.btn-eliminar');
            productoAEliminar = {
                id: boton.getAttribute('data-id'),
                nombre: boton.getAttribute('data-nombre')
            };
            
            document.getElementById('nombreProductoEliminar').textContent = productoAEliminar.nombre;
            modalEliminar.show();
        }
    });

    // Confirmar eliminación física
    document.getElementById('btnConfirmarEliminar').addEventListener('click', function() {
        if (productoAEliminar) {
            eliminarProducto(productoAEliminar.id);
        }
    });

    // Función para eliminar producto físicamente via AJAX
    function eliminarProducto(idProducto) {
        // Mostrar loading
        const btnConfirmar = document.getElementById('btnConfirmarEliminar');
        const originalText = btnConfirmar.innerHTML;
        btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Eliminando...';
        btnConfirmar.disabled = true;

        console.log("Iniciando eliminación física del producto ID:", idProducto);

        // Usar eliminación física (fisica=true)
        const url = `index.php?page=eliminar_producto&id=${idProducto}&fisica=true`;
        console.log("URL de eliminación:", url);

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log("Respuesta recibida, status:", response.status);
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log("Datos recibidos:", data);
            // Restaurar botón
            btnConfirmar.innerHTML = originalText;
            btnConfirmar.disabled = false;
            
            modalEliminar.hide();
            
            if (data.success) {
                console.log("Eliminación exitosa");
                mostrarMensaje('success', data.message);
                
                // Recargar la página después de 1.5 segundos
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                console.log("Error en eliminación:", data.message);
                mostrarMensaje('error', data.message || 'Error al eliminar el producto');
            }
        })
        .catch(error => {
            console.error("Error en fetch:", error);
            // Restaurar botón
            btnConfirmar.innerHTML = originalText;
            btnConfirmar.disabled = false;
            
            modalEliminar.hide();
            mostrarMensaje('error', 'Error de conexión con el servidor: ' + error.message);
        });
    }

    // Función para mostrar mensajes
    function mostrarMensaje(tipo, mensaje) {
        // Remover mensajes existentes
        const alertasExistentes = document.querySelectorAll('.alert');
        alertasExistentes.forEach(alerta => {
            if (alerta.parentNode) {
                alerta.remove();
            }
        });

        // Crear nueva alerta
        const alerta = document.createElement('div');
        alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
        alerta.innerHTML = `
            <i class="fas fa-${tipo === 'success' ? 'check' : 'exclamation'}-circle me-2"></i>
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insertar después del header
        const header = document.querySelector('.border-bottom');
        header.parentNode.insertBefore(alerta, header.nextSibling);
        
        // Auto-eliminar después de 5 segundos
        setTimeout(() => {
            if (alerta.parentNode) {
                alerta.remove();
            }
        }, 5000);
    }
});
</script>