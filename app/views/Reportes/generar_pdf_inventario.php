<?php
// --- Cargar Dompdf ---
require_once __DIR__ . '/../../libs/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// --- Incluir conexión y controlador ---
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../controllers/ReporteController.php';

// Función para obtener nombre del mes en español
function obtenerMesEspanol($fecha) {
    $meses = [
        'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
        'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
        'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
        'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
    ];
    $mesIngles = date('F', strtotime($fecha));
    return $meses[$mesIngles] ?? $mesIngles;
}

try {
    // --- Capturar fecha y hora EXACTA de generación ---
    date_default_timezone_set('America/Bogota');
    $fechaGeneracion = date('d/m/Y h:i:s A');
    
    // --- Obtener datos del reporte ---
    $tipo_reporte = $_GET['tipo'] ?? 'inventario';
    $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
    $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

    $reporteController = new ReporteController($db);
    
    // Obtener datos específicos para inventario
    $productos = $reporteController->generarReporteInventario();
    $productosMasVendidos = $reporteController->getProductosMasVendidos(10);

    // Calcular estadísticas del inventario
    $productosActivos = array_filter($productos, fn($p) => true);
    $totalProductos = count($productosActivos);
    $valorTotalInventario = array_sum(array_column($productosActivos, 'valor_inventario'));
    $productosBajoStock = count(array_filter($productosActivos, fn($p) => $p['estado_stock'] == 'BAJO'));
    $productosMedioStock = count(array_filter($productosActivos, fn($p) => $p['estado_stock'] == 'MEDIO'));
    $productosNormalStock = count(array_filter($productosActivos, fn($p) => $p['estado_stock'] == 'NORMAL'));
    $productosSinStock = count(array_filter($productosActivos, fn($p) => $p['stock_actual'] <= 0));

    // Calcular resumen por categorías
    $categoriasCount = [];
    $valorPorCategoria = [];
    foreach ($productos as $producto) {
        $categoria = $producto['nombre_categoria'] ?: 'Sin categoría';
        if (!isset($categoriasCount[$categoria])) {
            $categoriasCount[$categoria] = 0;
            $valorPorCategoria[$categoria] = 0;
        }
        $categoriasCount[$categoria]++;
        $valorPorCategoria[$categoria] += $producto['valor_inventario'];
    }
    arsort($categoriasCount);

    // --- Capturar contenido HTML ---
    ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario - Stock Nexus</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px; 
            margin: 30px; 
            color: #000;
            position: relative;
            min-height: 100vh;
            padding-bottom: 60px;
        }
        h1, h2, h3 { text-align: center; color: #003366; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: center; }
        th { background-color: #003366; color: white; font-weight: bold; }
        .resumen { background: #f4f4f4; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .text-green { color: #28a745; }
        .text-red { color: #dc3545; }
        .text-blue { color: #007bff; }
        .text-warning { color: #ffc107; }
        .text-info { color: #17a2b8; }
        .text-purple { color: #6f42c1; }
        .page-break { page-break-before: always; }
        .total-acumulado { 
            background: #e8f4fd; 
            border-left: 4px solid #007bff; 
            padding: 10px; 
            margin: 10px 0; 
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .footer-content {
            max-width: 100%;
            margin: 0 auto;
        }
        .estadisticas-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 15px 0;
        }
        .estadistica-item {
            background: white;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .estadistica-valor {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .badge-secondary { background: #e2e3e5; color: #383d41; }
        .stock-bajo { background-color: #fff3cd; color: #856404; }
        .stock-medio { background-color: #d1ecf1; color: #0c5460; }
        .stock-normal { background-color: #d4edda; color: #155724; }
        .stock-sin { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
<div class="container">
    <h1>Stock Nexus - Reporte de Inventario</h1>
    <h3>Estado Actual del Inventario</h3>
    <p style="text-align:center;color:gray;">Fecha de reporte: <?= $fechaGeneracion ?></p>
    <hr>

    <!-- Estadísticas Resumen -->
    <div class="estadisticas-grid">
        <div class="estadistica-item">
            <div>Total Productos</div>
            <div class="estadistica-valor text-blue"><?= $totalProductos ?></div>
            <small>En inventario</small>
        </div>
        <div class="estadistica-item">
            <div>Valor Total Inventario</div>
            <div class="estadistica-valor text-green">$<?= number_format($valorTotalInventario, 2) ?></div>
            <small>Valor en stock</small>
        </div>
        <div class="estadistica-item">
            <div>Productos Sin Stock</div>
            <div class="estadistica-valor text-red"><?= $productosSinStock ?></div>
            <small>Requieren atención</small>
        </div>
        <div class="estadistica-item">
            <div>Bajo Stock</div>
            <div class="estadistica-valor text-warning"><?= $productosBajoStock ?></div>
            <small>Alerta stock mínimo</small>
        </div>
        <div class="estadistica-item">
            <div>Medio Stock</div>
            <div class="estadistica-valor text-info"><?= $productosMedioStock ?></div>
            <small>Stock moderado</small>
        </div>
        <div class="estadistica-item">
            <div>Normal Stock</div>
            <div class="estadistica-valor text-green"><?= $productosNormalStock ?></div>
            <small>Stock óptimo</small>
        </div>
    </div>

    <!-- Detalle de Inventario -->
    <h3>Detalle de Productos en Inventario</h3>
    <table>
        <thead>
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
        <tbody>
        <?php foreach ($productos as $producto): 
            // Determinar clase según estado de stock
            $claseStock = '';
            $textoEstado = '';
            if ($producto['stock_actual'] <= 0) {
                $claseStock = 'stock-sin';
                $textoEstado = 'SIN STOCK';
            } elseif ($producto['estado_stock'] == 'BAJO') {
                $claseStock = 'stock-bajo';
                $textoEstado = 'BAJO';
            } elseif ($producto['estado_stock'] == 'MEDIO') {
                $claseStock = 'stock-medio';
                $textoEstado = 'MEDIO';
            } else {
                $claseStock = 'stock-normal';
                $textoEstado = 'NORMAL';
            }
        ?>
            <tr>
                <td><?= $producto['codigo_producto'] ?></td>
                <td><?= $producto['nombre_producto'] ?></td>
                <td><?= $producto['nombre_categoria'] ?: 'Sin categoría' ?></td>
                <td class="<?= $claseStock ?>"><?= number_format($producto['stock_actual'], 2) ?></td>
                <td><?= number_format($producto['stock_minimo'], 2) ?></td>
                <td class="text-blue">$<?= number_format($producto['precio_compra'], 2) ?></td>
                <td class="text-green">$<?= number_format($producto['precio_venta'], 2) ?></td>
                <td class="text-purple">$<?= number_format($producto['valor_inventario'], 2) ?></td>
                <td class="<?= $claseStock ?>"><?= $textoEstado ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($productos)): ?>
            <tr>
                <td colspan="9" class="text-center text-muted">No hay productos registrados en el inventario.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Productos Más Vendidos -->
    <h3>Productos Más Vendidos</h3>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad Vendida</th>
                <th>Ingresos Generados</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($productosMasVendidos as $producto): ?>
            <tr>
                <td><?= $producto['nombre_producto'] ?></td>
                <td class="text-blue"><?= $producto['total_vendido'] ?> unidades</td>
                <td class="text-green">$<?= number_format($producto['total_ingresos'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($productosMasVendidos)): ?>
            <tr>
                <td colspan="3" class="text-center text-muted">No hay datos de ventas.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Resumen por Categorías -->
    <h3>Resumen por Categorías</h3>
    <table>
        <thead>
            <tr>
                <th>Categoría</th>
                <th>Cantidad Productos</th>
                <th>Valor Inventario</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($categoriasCount as $categoria => $cantidad): ?>
            <tr>
                <td><?= htmlspecialchars($categoria) ?></td>
                <td class="text-center"><?= $cantidad ?></td>
                <td class="text-purple text-right">$<?= number_format($valorPorCategoria[$categoria], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Resumen Final -->
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
        <h4 style="color: #003366; text-align: center;">Resumen Ejecutivo del Inventario</h4>
        <div style="font-size: 11px; color: #333; line-height: 1.5;">
            <p>El reporte de inventario actual muestra:</p>
            <ul>
                <li><strong><?= $totalProductos ?> productos</strong> activos en el sistema</li>
                <li><strong>$<?= number_format($valorTotalInventario, 2) ?></strong> en valor total de inventario</li>
                <li><strong><?= $productosBajoStock ?> productos</strong> con stock bajo (<?= $totalProductos > 0 ? number_format(($productosBajoStock/$totalProductos)*100, 1) : 0 ?>%)</li>
                <li><strong><?= $productosMedioStock ?> productos</strong> con stock medio (<?= $totalProductos > 0 ? number_format(($productosMedioStock/$totalProductos)*100, 1) : 0 ?>%)</li>
                <li><strong><?= $productosNormalStock ?> productos</strong> con stock normal (<?= $totalProductos > 0 ? number_format(($productosNormalStock/$totalProductos)*100, 1) : 0 ?>%)</li>
                <li><strong><?= $productosSinStock ?> productos</strong> sin stock (<?= $totalProductos > 0 ? number_format(($productosSinStock/$totalProductos)*100, 1) : 0 ?>%)</li>
            </ul>
            <?php if ($productosBajoStock > 0 || $productosSinStock > 0): ?>
            <p style="color: #dc3545; font-weight: bold;">
                ⚠️ Se recomienda revisar <?= $productosBajoStock + $productosSinStock ?> productos que requieren atención inmediata.
            </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer profesional -->
    <div class="footer">
        <div class="footer-content">
            Stock Nexus © <?= date('Y') ?> — Reporte de Inventario generado el <?= $fechaGeneracion ?>
        </div>
    </div>
</div>
</body>
</html>
<?php
    // --- CAPTURAR el contenido del buffer ---
    $html = ob_get_clean();
    
    // --- Generar PDF ---
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    // --- Limpiar cualquier salida previa ---
    if (ob_get_length()) ob_clean();
    
    // --- Enviar PDF ---
    $dompdf->stream("Reporte de Inventario ".date('d-m-Y').".pdf", ["Attachment" => true]);
    exit;

} catch (Exception $e) {
    // Manejar errores gracefuly
    die("Error al generar el PDF: " . $e->getMessage());
}
?>