<!doctype html>
<html><head><meta charset="utf-8"><title>Reporte Inventario</title></head><body>
<h1>Reporte de Inventario</h1>
<table border="1" cellpadding="5"><thead><tr><th>ID</th><th>SKU</th><th>Nombre</th><th>Stock</th><th>Costo</th></tr></thead><tbody>
<?php foreach($products as $p): ?><tr><td><?= $p['id'] ?></td><td><?= htmlspecialchars($p['sku']) ?></td><td><?= htmlspecialchars($p['nombre']) ?></td><td><?= $p['stock_actual'] ?></td><td><?= $p['costo_unitario'] ?></td></tr><?php endforeach; ?>
</tbody></table>
</body></html>
