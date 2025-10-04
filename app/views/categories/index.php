<?php require __DIR__ . '/../layouts/header.php'; ?>
<h2>Categorías</h2>
<p><a class="btn btn-sm btn-primary" href="/public/index.php?module=categories&action=create">Nuevo</a></p>
<table class="table"><thead><tr><th>ID</th><th>Nombre</th><th>Descripción</th></tr></thead><tbody>
<?php foreach($categories as $c): ?><tr><td><?= $c['id'] ?></td><td><?= htmlspecialchars($c['nombre']) ?></td><td><?= htmlspecialchars($c['descripcion']) ?></td></tr><?php endforeach; ?>
</tbody></table>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
