<?php require __DIR__ . '/../layouts/header.php'; ?>
<h2>Usuarios</h2>
<p><a class="btn btn-sm btn-primary" href="/public/index.php?module=users&action=create">Nuevo</a></p>
<table class="table table-striped">
<thead><tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>Email</th><th>Rol</th><th>Activo</th><th>Acciones</th></tr></thead>
<tbody>
<?php foreach($users as $u): ?>
<tr>
  <td><?= $u['id'] ?></td>
  <td><?= htmlspecialchars($u['nombre']) ?></td>
  <td><?= htmlspecialchars($u['usuario'] ?? '') ?></td>
  <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
  <td><?= htmlspecialchars($u['role'] ?? '') ?></td>
  <td><?= $u['active'] ? 'Sí' : 'No' ?></td>
  <td><a href="/public/index.php?module=users&action=deactivate&id=<?= $u['id'] ?>" class="btn btn-sm btn-danger">Inactivar</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
