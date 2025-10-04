<?php require __DIR__ . '/../layouts/header.php'; ?>
<h2>Proveedores</h2>
<p><a class="btn btn-sm btn-primary" href="/public/index.php?module=suppliers&action=create">Nuevo</a></p>
<table class="table"><thead><tr><th>ID</th><th>Nombre</th><th>Contacto</th><th>Teléfono</th><th>Email</th></tr></thead><tbody>
<?php foreach($suppliers as $s): ?><tr><td><?= $s['id'] ?></td><td><?= htmlspecialchars($s['nombre']) ?></td><td><?= htmlspecialchars($s['contacto']) ?></td><td><?= htmlspecialchars($s['telefono']) ?></td><td><?= htmlspecialchars($s['email']) ?></td></tr><?php endforeach; ?>
</tbody></table>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
