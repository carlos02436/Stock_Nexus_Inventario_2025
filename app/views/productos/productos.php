<?php
// app/views/productos/productos.php
$productoController = new ProductoController($db);
$productos = $productoController->listar();

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
        <h1 class="h2"><i class="fas fa-boxes me-2"></i>Gestión de Productos</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=inventario" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver al Inventario
            </a>
            <a href="index.php?page=crear_producto" class="btn btn-neon">
                <i class="fas fa-plus me-2"></i>Nuevo Producto
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
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
                        <i class="fas fa-undo me-1"></i>Limpiar filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-white">
                <i class="fas fa-list me-2"></i>Lista de Productos
            </h5>
            <div class="text-white small" id="contadorProductos">
                Mostrando <?= count($productos) ?> productos
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaProductos">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Stock</th>
                            <th>Precio Compra</th>
                            <th>Precio Venta</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTabla">
                        <?php foreach ($productos as $producto): ?>
                        <tr data-estado="<?= $producto['estado'] ?>">
                            <td><?= $producto['codigo_producto'] ?></td>
                            <td><?= $producto['nombre_producto'] ?></td>
                            <td><?= $producto['nombre_categoria'] ?: 'Sin categoría' ?></td>
                            <td>
                                <span class="badge bg-<?= $producto['stock_actual'] <= $producto['stock_minimo'] ? 'danger' : 'success' ?>">
                                    <?= $producto['stock_actual'] ?>
                                </span>
                            </td>
                            <td>$<?= number_format($producto['precio_compra'], 2) ?></td>
                            <td>$<?= number_format($producto['precio_venta'], 2) ?></td>
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
                                    <a href="index.php?page=eliminar_producto&id=<?= $producto['id_producto'] ?>" 
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
            // Obtener el estado del data attribute en lugar del texto del badge
            const estado = fila.getAttribute('data-estado');
            
            let mostrarFila = true;
            
            // Filtrar por código
            if (filtroCodigo && !codigo.includes(filtroCodigo.toLowerCase())) {
                mostrarFila = false;
            }
            
            // Filtrar por nombre
            if (filtroNombre && !nombre.includes(filtroNombre.toLowerCase())) {
                mostrarFila = false;
            }
            
            // Filtrar por categoría
            if (filtroCategoria && categoria !== filtroCategoria) {
                mostrarFila = false;
            }
            
            // Filtrar por estado
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
        document.getElementById('contadorProductos').textContent = 
            `Mostrando ${productosVisibles} de ${filas.length} productos`;
    }
    
    // Event listeners para los filtros
    document.getElementById('filtroCodigo').addEventListener('input', function(e) {
        filtroCodigo = e.target.value;
        aplicarFiltros();
    });
    
    document.getElementById('filtroNombre').addEventListener('input', function(e) {
        filtroNombre = e.target.value;
        aplicarFiltros();
    });
    
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
});
</script>