<?php
// app/views/movimientos/movimientos.php
$inventarioController = new InventarioController($db);
$movimientos = $inventarioController->listarMovimientos();

// Obtener datos únicos para los filtros
$productosUnicos = [];
$tiposMovimiento = [];
$usuariosUnicos = [];

foreach ($movimientos as $movimiento) {
    // Productos únicos
    $producto = $movimiento['nombre_producto'] . ' (' . $movimiento['codigo_producto'] . ')';
    if (!in_array($producto, $productosUnicos)) {
        $productosUnicos[] = $producto;
    }
    
    // Tipos de movimiento únicos
    $tipo = $movimiento['tipo_movimiento'];
    if (!in_array($tipo, $tiposMovimiento)) {
        $tiposMovimiento[] = $tipo;
    }
    
    // Usuarios únicos
    $usuario = $movimiento['usuario_nombre'];
    if (!in_array($usuario, $usuariosUnicos)) {
        $usuariosUnicos[] = $usuario;
    }
}

sort($productosUnicos);
sort($tiposMovimiento);
sort($usuariosUnicos);
?>

<div class="container-fluid px-4 pb-5">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
         style="margin-top:180px;">
        <h1 class="h2"><i class="fas fa-exchange-alt me-2"></i>Movimientos de Bodega</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <!-- Volver al Inventario - Gris (boton3) -->
            <a href="index.php?page=inventario" class="boton3 me-2 text-decoration-none" style="width: auto; min-width: 180px;">
                <div class="boton-top3">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Inventario
                </div>
                <div class="boton-bottom3"></div>
                <div class="boton-base3"></div>
            </a>

            <!-- Nuevo Movimiento - Verde (button) -->
            <a href="index.php?page=crear_movimiento" class="boton1 text-decoration-none" style="width: auto; min-width: 180px;">
                <span class="boton-top1">
                    <i class="fas fa-plus me-2"></i>Nuevo Movimiento
                </span>
                <span class="boton-bottom1"></span>
                <span class="boton-base1"></span>
            </a>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title mb-0 text-white py-3">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="filtroProducto" class="form-label text-white">Producto</label>
                    <input type="text" class="form-control" id="filtroProducto" placeholder="Buscar por producto...">
                </div>
                <div class="col-md-3">
                    <label for="filtroTipo" class="form-label text-white">Tipo de Movimiento</label>
                    <select class="form-select" id="filtroTipo">
                        <option value="">Todos los tipos</option>
                        <?php foreach ($tiposMovimiento as $tipo): ?>
                            <option value="<?= htmlspecialchars($tipo) ?>"><?= htmlspecialchars($tipo) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroUsuario" class="form-label text-white">Usuario</label>
                    <select class="form-select" id="filtroUsuario">
                        <option value="">Todos los usuarios</option>
                        <?php foreach ($usuariosUnicos as $usuario): ?>
                            <option value="<?= htmlspecialchars($usuario) ?>"><?= htmlspecialchars($usuario) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroFecha" class="form-label text-white">Fecha</label>
                    <input type="date" class="form-control" id="filtroFecha">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="button" class="boton2" id="btnLimpiarFiltros" style="width: auto; min-width: 140px;">
                        <div class="boton-top2">
                            <i class="fas fa-undo me-1"></i>Limpiar filtros
                        </div>
                        <div class="boton-bottom2"></div>
                        <div class="boton-base2"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-white py-3">
                <i class="fas fa-list me-2"></i>Historial de Movimientos
            </h5>
            <div class="text-muted small" id="contadorMovimientos">
                Mostrando <?= count($movimientos) ?> movimientos
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Descripción</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTablaMovimientos">
                        <?php foreach ($movimientos as $movimiento): ?>
                        <tr data-tipo="<?= $movimiento['tipo_movimiento'] ?>" 
                            data-usuario="<?= $movimiento['usuario_nombre'] ?>"
                            data-fecha="<?= date('Y-m-d', strtotime($movimiento['fecha_movimiento'])) ?>">
                            <td><?= date('d/m/Y H:i', strtotime($movimiento['fecha_movimiento'])) ?></td>
                            <td><?= $movimiento['nombre_producto'] ?> (<?= $movimiento['codigo_producto'] ?>)</td>
                            <td>
                                <span class="badge bg-<?= 
                                    $movimiento['tipo_movimiento'] == 'Entrada' ? 'success' : 
                                    ($movimiento['tipo_movimiento'] == 'Salida' ? 'danger' : 'warning')
                                ?>">
                                    <?= $movimiento['tipo_movimiento'] ?>
                                </span>
                            </td>
                            <td><?= $movimiento['cantidad'] ?></td>
                            <td><?= $movimiento['descripcion'] ?></td>
                            <td><?= $movimiento['usuario_nombre'] ?></td>
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
    let filtroProducto = '';
    let filtroTipo = '';
    let filtroUsuario = '';
    let filtroFecha = '';
    
    // Obtener todas las filas de la tabla
    const filas = document.querySelectorAll('#cuerpoTablaMovimientos tr');
    
    // Función para aplicar filtros
    function aplicarFiltros() {
        let movimientosVisibles = 0;
        
        filas.forEach(fila => {
            const celdas = fila.querySelectorAll('td');
            const producto = celdas[1].textContent.toLowerCase();
            const tipo = fila.getAttribute('data-tipo');
            const usuario = fila.getAttribute('data-usuario');
            const fecha = fila.getAttribute('data-fecha');
            
            let mostrarFila = true;
            
            // Filtrar por producto (búsqueda parcial)
            if (filtroProducto && !producto.includes(filtroProducto.toLowerCase())) {
                mostrarFila = false;
            }
            
            // Filtrar por tipo (búsqueda exacta)
            if (filtroTipo && tipo !== filtroTipo) {
                mostrarFila = false;
            }
            
            // Filtrar por usuario (búsqueda exacta)
            if (filtroUsuario && usuario !== filtroUsuario) {
                mostrarFila = false;
            }
            
            // Filtrar por fecha (búsqueda exacta)
            if (filtroFecha && fecha !== filtroFecha) {
                mostrarFila = false;
            }
            
            // Mostrar u ocultar fila
            if (mostrarFila) {
                fila.style.display = '';
                movimientosVisibles++;
            } else {
                fila.style.display = 'none';
            }
        });
        
        // Actualizar contador
        actualizarContador(movimientosVisibles);
    }
    
    // Función para actualizar el contador
    function actualizarContador(visibles) {
        const total = filas.length;
        const contador = document.getElementById('contadorMovimientos');
        
        if (visibles === total) {
            contador.textContent = `Mostrando ${total} movimientos`;
        } else {
            contador.textContent = `Mostrando ${visibles} de ${total} movimientos`;
        }
    }
    
    // Event listeners para los filtros - INPUT (tiempo real)
    document.getElementById('filtroProducto').addEventListener('input', function(e) {
        filtroProducto = e.target.value;
        aplicarFiltros();
    });
    
    // Event listeners para los SELECT (cambio inmediato)
    document.getElementById('filtroTipo').addEventListener('change', function(e) {
        filtroTipo = e.target.value;
        aplicarFiltros();
    });
    
    document.getElementById('filtroUsuario').addEventListener('change', function(e) {
        filtroUsuario = e.target.value;
        aplicarFiltros();
    });
    
    // Event listener para fecha
    document.getElementById('filtroFecha').addEventListener('change', function(e) {
        filtroFecha = e.target.value;
        aplicarFiltros();
    });
    
    // Limpiar filtros
    document.getElementById('btnLimpiarFiltros').addEventListener('click', function() {
        // Limpiar inputs
        document.getElementById('filtroProducto').value = '';
        document.getElementById('filtroTipo').value = '';
        document.getElementById('filtroUsuario').value = '';
        document.getElementById('filtroFecha').value = '';
        
        // Resetear variables de filtro
        filtroProducto = '';
        filtroTipo = '';
        filtroUsuario = '';
        filtroFecha = '';
        
        // Aplicar filtros (mostrar todo)
        aplicarFiltros();
    });
});
</script>