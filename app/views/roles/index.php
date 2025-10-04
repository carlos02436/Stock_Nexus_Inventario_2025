<?php require __DIR__ . '/../layouts/header.php'; ?>
<h2>Roles</h2>
<table class="table"><thead><tr><th>ID</th><th>Nombre</th></tr></thead><tbody>
<?php foreach($roles as $r): ?><tr><td><?= $r['id'] ?></td><td><?= htmlspecialchars($r['nombre']) ?></td></tr><?php endforeach; ?>
</tbody></table>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
