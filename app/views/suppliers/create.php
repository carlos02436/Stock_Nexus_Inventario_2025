<?php require __DIR__ . '/../layouts/header.php'; ?>
<h2>Crear Proveedor</h2>
<form method="POST" action="/public/index.php?module=suppliers&action=store">
  <div class="mb-3"><label>Nombre</label><input name="nombre" class="form-control" required></div>
  <div class="mb-3"><label>Contacto</label><input name="contacto" class="form-control"></div>
  <div class="mb-3"><label>Teléfono</label><input name="telefono" class="form-control"></div>
  <div class="mb-3"><label>Email</label><input name="email" type="email" class="form-control"></div>
  <button class="btn btn-primary">Guardar</button>
</form>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
