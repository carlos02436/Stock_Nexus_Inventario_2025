<?php require __DIR__ . '/../layouts/header.php'; ?>
<h2>Crear Usuario</h2>
<form method="POST" action="/public/index.php?module=users&action=store">
  <div class="mb-3"><label>Nombre</label><input name="nombre" class="form-control" required></div>
  <div class="mb-3"><label>Email</label><input name="email" type="email" class="form-control" required></div>
  <div class="mb-3"><label>Usuario</label><input name="usuario" class="form-control" required></div>
  <div class="mb-3"><label>Contraseña</label><input name="password" type="password" class="form-control" required></div>
  <div class="mb-3"><label>Rol</label>
    <select name="role" class="form-control">
      <?php foreach($roles as $r): ?>
        <option value="<?= htmlspecialchars($r['nombre']) ?>"><?= htmlspecialchars($r['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <button class="btn btn-primary">Guardar</button>
</form>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
