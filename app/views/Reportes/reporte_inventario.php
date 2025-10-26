<?php
// app/views/reportes/reporte_inventario.php
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

$reporteController = new ReporteController($db);
$productos = $reporteController->generarReporteInventario();
$productosMasVendidos = $reporteController->getProductosMasVendidos(10);

// Calcular estadísticas del inventario
$productosActivos = array_filter($productos, fn($p) => true); // Todos están activos por el query
$totalProductos = count($productosActivos);
$valorTotalInventario = array_sum(array_column($productosActivos, 'valor_inventario'));
$productosBajoStock = count(array_filter($productosActivos, fn($p) => $p['estado_stock'] == 'BAJO'));
$productosMedioStock = count(array_filter($productosActivos, fn($p) => $p['estado_stock'] == 'MEDIO'));
$productosNormalStock = count(array_filter($productosActivos, fn($p) => $p['estado_stock'] == 'NORMAL'));

// Calcular productos sin stock
$productosSinStock = count(array_filter($productosActivos, fn($p) => $p['stock_actual'] <= 0));
?>
<div class="container-fluid px-4 mb-5 text-white" style="margin-top:180px;">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom border-light">
        <h1 class="h2"><i class="fas fa-boxes me-2"></i>Reporte de Inventario</h1>
        <div class="btn-toolbar mb-2 mb-md-2">
            <a href="index.php?page=reportes" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver a Reportes
            </a>
            <a href="index.php?page=generar_pdf_inventario&tipo=inventario&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>" 
               class="btn btn-danger me-2">
                <i class="fas fa-file-pdf me-2"></i>PDF
            </a>
            <a href="index.php?page=generar_excel_inventario&tipo=inventario&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>" 
               class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Excel
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-left-primary shadow h-100 py-2 text-white"
                style="border-left: 4px solid #4e73df !important;">
                <div class="card-body text-center">
                    <h5>Total Productos</h5>
                    <h4 id="totalProductos"><?= $totalProductos ?></h4>
                    <small class="text-white">Activos</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-left-primary shadow h-100 py-2 text-white"
                style="border-left: 4px solid #09ff53ff !important;">
                <div class="card-body text-center">
                    <h5>Valor Inventario</h5>
                    <h4 id="valorTotalInventario">$<?= number_format($valorTotalInventario, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-left-primary shadow text-white"
                style="border-left: 4px solid #ff0000ff !important;">
                <div class="card-body text-center">
                    <h5>Bajo Stock</h5>
                    <h4 id="productosBajoStock"><?= $productosBajoStock ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-left-primary shadow text-white"
                style="border-left: 4px solid #ff9900ff !important;">
                <div class="card-body text-center">
                    <h5>Medio Stock</h5>
                    <h4 id="productosMedioStock"><?= $productosMedioStock ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-left-primary shadow text-white"
                style="border-left: 4px solid #c4ff01ff !important;">
                <div class="card-body text-center">
                    <h5>Normal Stock</h5>
                    <h4 id="productosNormalStock"><?= $productosNormalStock ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-left-primary shadow text-white"
                style="border-left: 4px solid #ff66ccff !important;">
                <div class="card-body text-center">
                    <h5>Sin Stock</h5>
                    <h4 id="productosSinStock"><?= $productosSinStock ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card py-3 mb-4">
        <div class="card-header text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3" id="formFiltros">
                <input type="hidden" name="page" value="reporte_inventario">
                <div class="col-md-2">
                    <label for="filtroCodigo" class="form-label text-white">Código</label>
                    <input type="text" id="filtroCodigo" class="form-control text-black border-0" 
                           placeholder="Código producto...">
                </div>
                <div class="col-md-3">
                    <label for="filtroNombre" class="form-label text-white">Nombre</label>
                    <input type="text" id="filtroNombre" class="form-control text-black border-0" 
                           placeholder="Nombre producto...">
                </div>
                <div class="col-md-2">
                    <label for="filtroCategoria" class="form-label text-white">Categoría</label>
                    <select id="filtroCategoria" class="form-select text-black border-0">
                        <option value="">Todas</option>
                        <?php
                        // Extraer categorías únicas de los productos
                        $categorias = array_unique(array_column($productos, 'nombre_categoria'));
                        foreach ($categorias as $categoria): 
                            if (!empty($categoria)):
                        ?>
                        <option value="<?= htmlspecialchars($categoria) ?>"><?= htmlspecialchars($categoria) ?></option>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filtroEstadoStock" class="form-label text-white">Estado Stock</label>
                    <select id="filtroEstadoStock" class="form-select text-black border-0">
                        <option value="">Todos</option>
                        <option value="SIN_STOCK">Sin Stock</option>
                        <option value="BAJO">Bajo Stock</option>
                        <option value="MEDIO">Medio Stock</option>
                        <option value="NORMAL">Normal Stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-white d-block">&nbsp;</label>
                    <button type="button" id="btnLimpiarFiltros" class="btn btn-danger w-100">
                        <i class="fas fa-undo me-1"></i>Limpiar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tablas -->
    <div class="row mb-3">
        <div class="col-lg-8">
            <div class="card shadow-sm py-3">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">Detalle de Productos en Inventario</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="tablaProductos">
                            <thead class="table-dark text-white">
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Stock Actual</th>
                                    <th>Stock Mínimo</th>
                                    <th>Precio Compra</th>
                                    <th>Precio Venta</th>
                                    <th>Valor Inventario</th>
                                    <th>Estado Stock</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyProductos">
                                <?php foreach ($productos as $producto): 
                                    // Determinar el estado de stock real
                                    $estadoStockReal = '';
                                    $claseStock = '';
                                    $iconoStock = '';
                                    
                                    if ($producto['stock_actual'] <= 0) {
                                        $estadoStockReal = 'SIN_STOCK';
                                        $claseStock = 'text-danger fw-bold';
                                        $iconoStock = '❌';
                                    } elseif ($producto['estado_stock'] == 'BAJO') {
                                        $estadoStockReal = 'BAJO';
                                        $claseStock = 'text-warning fw-bold';
                                        $iconoStock = '⚠️';
                                    } elseif ($producto['estado_stock'] == 'MEDIO') {
                                        $estadoStockReal = 'MEDIO';
                                        $claseStock = 'text-info';
                                        $iconoStock = 'ℹ️';
                                    } else {
                                        $estadoStockReal = 'NORMAL';
                                        $claseStock = 'text-success';
                                        $iconoStock = '✅';
                                    }
                                    
                                    // Texto para mostrar en la tabla
                                    $textoEstadoStock = $estadoStockReal == 'SIN_STOCK' ? 'SIN STOCK' : $producto['estado_stock'];
                                ?>
                                <tr class="fila-producto" 
                                    data-codigo="<?= $producto['codigo_producto'] ?>" 
                                    data-nombre="<?= htmlspecialchars($producto['nombre_producto']) ?>" 
                                    data-categoria="<?= htmlspecialchars($producto['nombre_categoria'] ?? 'Sin categoría') ?>" 
                                    data-stock="<?= $producto['stock_actual'] ?>" 
                                    data-stock-minimo="<?= $producto['stock_minimo'] ?>"
                                    data-precio-compra="<?= $producto['precio_compra'] ?>"
                                    data-precio-venta="<?= $producto['precio_venta'] ?>"
                                    data-valor-inventario="<?= $producto['valor_inventario'] ?>"
                                    data-estado-stock="<?= $estadoStockReal ?>"
                                    data-es-sin-stock="<?= $producto['stock_actual'] <= 0 ? '1' : '0' ?>"
                                    data-es-bajo-stock="<?= $producto['estado_stock'] == 'BAJO' ? '1' : '0' ?>"
                                    data-es-medio-stock="<?= $producto['estado_stock'] == 'MEDIO' ? '1' : '0' ?>"
                                    data-es-normal-stock="<?= $producto['estado_stock'] == 'NORMAL' ? '1' : '0' ?>">
                                    <td class="codigo-producto"><?= $producto['codigo_producto'] ?></td>
                                    <td class="nombre-producto"><?= $producto['nombre_producto'] ?></td>
                                    <td class="categoria-producto"><?= $producto['nombre_categoria'] ?: 'Sin categoría' ?></td>
                                    <td class="stock-actual <?= $claseStock ?>">
                                        <?= $iconoStock ?> <?= number_format($producto['stock_actual'], 2) ?>
                                    </td>
                                    <td class="stock-minimo"><?= number_format($producto['stock_minimo'], 2) ?></td>
                                    <td class="precio-compra">$<?= number_format($producto['precio_compra'], 2) ?></td>
                                    <td class="precio-venta">$<?= number_format($producto['precio_venta'], 2) ?></td>
                                    <td class="valor-inventario">$<?= number_format($producto['valor_inventario'], 2) ?></td>
                                    <td class="estado-stock">
                                        <span class="badge bg-<?= 
                                            $estadoStockReal == 'SIN_STOCK' ? 'danger' : 
                                            ($estadoStockReal == 'BAJO' ? 'warning' : 
                                            ($estadoStockReal == 'MEDIO' ? 'info' : 'success'))
                                        ?>">
                                            <?= $textoEstadoStock ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($productos)): ?>
                                <tr class="no-resultados">
                                    <td colspan="9" class="text-center text-muted">No hay productos registrados en el inventario.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Productos más vendidos -->
        <div class="col-lg-4">
            <div class="card shadow-sm py-3">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">Productos Más Vendidos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-dark text-white">
                                <tr>
                                    <th>Producto</th>
                                    <th>Vendido</th>
                                    <th>Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productosMasVendidos as $producto): ?>
                                <tr>
                                    <td><?= $producto['nombre_producto'] ?></td>
                                    <td class="text-center"><?= $producto['total_vendido'] ?></td>
                                    <td>$<?= number_format($producto['total_ingresos'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($productosMasVendidos)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No hay datos de ventas.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Resumen por categorías -->
            <div class="card shadow-sm py-3 mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">Resumen por Categorías</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-dark text-white">
                                <tr>
                                    <th>Categoría</th>
                                    <th>Productos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $categoriasCount = [];
                                foreach ($productos as $producto) {
                                    $categoria = $producto['nombre_categoria'] ?: 'Sin categoría';
                                    if (!isset($categoriasCount[$categoria])) {
                                        $categoriasCount[$categoria] = 0;
                                    }
                                    $categoriasCount[$categoria]++;
                                }
                                arsort($categoriasCount);
                                ?>
                                <?php foreach ($categoriasCount as $categoria => $cantidad): ?>
                                <tr>
                                    <td><?= htmlspecialchars($categoria) ?></td>
                                    <td class="text-center"><?= $cantidad ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filtroCodigo = document.getElementById('filtroCodigo');
    const filtroNombre = document.getElementById('filtroNombre');
    const filtroCategoria = document.getElementById('filtroCategoria');
    const filtroEstadoStock = document.getElementById('filtroEstadoStock');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    const filasProductos = document.querySelectorAll('.fila-producto');
    
    // Función para verificar si hay filtros activos
    function hayFiltrosActivos() {
        return filtroCodigo.value || filtroNombre.value || filtroCategoria.value || filtroEstadoStock.value;
    }
    
    // Función para actualizar estadísticas
    function actualizarEstadisticas(productosFiltrados) {
        const totalProductos = productosFiltrados.length;
        
        // Calcular valor total del inventario
        const valorTotalInventario = productosFiltrados.reduce((sum, fila) => {
            return sum + parseFloat(fila.getAttribute('data-valor-inventario'));
        }, 0);
        
        // Contar productos por estado de stock
        const productosBajoStock = productosFiltrados.filter(fila => 
            fila.getAttribute('data-estado-stock') === 'BAJO'
        ).length;
        
        const productosMedioStock = productosFiltrados.filter(fila => 
            fila.getAttribute('data-estado-stock') === 'MEDIO'
        ).length;
        
        const productosNormalStock = productosFiltrados.filter(fila => 
            fila.getAttribute('data-estado-stock') === 'NORMAL'
        ).length;
        
        const productosSinStock = productosFiltrados.filter(fila => 
            fila.getAttribute('data-estado-stock') === 'SIN_STOCK'
        ).length;
        
        // Actualizar las estadísticas en la interfaz
        document.getElementById('totalProductos').textContent = totalProductos;
        document.getElementById('valorTotalInventario').textContent = '$' + valorTotalInventario.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        document.getElementById('productosBajoStock').textContent = productosBajoStock;
        document.getElementById('productosMedioStock').textContent = productosMedioStock;
        document.getElementById('productosNormalStock').textContent = productosNormalStock;
        document.getElementById('productosSinStock').textContent = productosSinStock;
    }
    
    // Función para filtrar los productos
    function filtrarProductos() {
        const codigoVal = filtroCodigo.value.toLowerCase().trim();
        const nombreVal = filtroNombre.value.toLowerCase().trim();
        const categoriaVal = filtroCategoria.value;
        const estadoStockVal = filtroEstadoStock.value;
        
        let productosFiltrados = [];
        let hayCoincidencias = false;
        
        filasProductos.forEach(fila => {
            const codigo = fila.getAttribute('data-codigo').toLowerCase();
            const nombre = fila.getAttribute('data-nombre').toLowerCase();
            const categoria = fila.getAttribute('data-categoria');
            const estadoStock = fila.getAttribute('data-estado-stock');
            
            // Verificar filtros de campos
            const coincideCampos = 
                (!codigoVal || codigo.includes(codigoVal)) &&
                (!nombreVal || nombre.includes(nombreVal)) &&
                (!categoriaVal || categoria === categoriaVal) &&
                (!estadoStockVal || estadoStock === estadoStockVal);
            
            // Mostrar u ocultar la fila
            if (coincideCampos) {
                fila.style.display = '';
                productosFiltrados.push(fila);
                hayCoincidencias = true;
            } else {
                fila.style.display = 'none';
            }
        });
        
        // Actualizar estadísticas con productos filtrados
        actualizarEstadisticas(productosFiltrados);
        
        // Mostrar mensaje solo si se han aplicado filtros y no hay coincidencias
        const tbody = document.getElementById('tbodyProductos');
        const mensajeNoResultados = tbody.querySelector('.no-resultados');
        
        // Verificar si hay filtros activos
        const hayFiltros = hayFiltrosActivos();
        
        if (!hayCoincidencias && hayFiltros) {
            if (!mensajeNoResultados) {
                const tr = document.createElement('tr');
                tr.className = 'no-resultados';
                tr.innerHTML = '<td colspan="9" class="text-center text-muted">No se encontraron productos con los filtros aplicados.</td>';
                tbody.appendChild(tr);
            }
        } else if (mensajeNoResultados && (hayCoincidencias || !hayFiltros)) {
            // Remover mensaje si hay coincidencias o no hay filtros activos
            mensajeNoResultados.remove();
        }
    }
    
    // Eventos para todos los filtros
    filtroCodigo.addEventListener('input', filtrarProductos);
    filtroNombre.addEventListener('input', filtrarProductos);
    filtroCategoria.addEventListener('change', filtrarProductos);
    filtroEstadoStock.addEventListener('change', filtrarProductos);
    
    // Evento para limpiar filtros
    btnLimpiarFiltros.addEventListener('click', function() {
        // Limpiar todos los filtros
        filtroCodigo.value = '';
        filtroNombre.value = '';
        filtroCategoria.value = '';
        filtroEstadoStock.value = '';
        filtrarProductos();
    });
    
    // Inicializar estadísticas
    actualizarEstadisticas(Array.from(filasProductos));
});
</script>