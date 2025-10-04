<?php require __DIR__ . '/../layouts/header.php'; ?>
<h2>Productos</h2>
<p><a class="btn btn-sm btn-primary" href="/public/index.php?module=products&action=create">Nuevo</a></p>
<table class="table table-striped">
<thead><tr><th>ID</th><th>SKU</th><th>Nombre</th><th>Categoría</th><th>Proveedor</th><th>Stock</th></tr></thead>
<tbody>
<?php foreach($items as $p): ?>
<tr>
  <td><?= $p['id'] ?></td>
  <td><?= htmlspecialchars($p['sku']) ?></td>
  <td><?= htmlspecialchars($p['nombre']) ?></td>
  <td><?= htmlspecialchars($p['category_name']) ?></td>
  <td><?= htmlspecialchars($p['supplier_name']) ?></td>
  <td><?= htmlspecialchars($p['stock_actual']) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
