<?php require __DIR__ . '/../layouts/header.php'; ?>
<h2>Crear Categoría</h2>
<form method="POST" action="/public/index.php?module=categories&action=store">
  <div class="mb-3"><label>Nombre</label><input name="nombre" class="form-control" required></div>
  <div class="mb-3"><label>Descripción</label><input name="descripcion" class="form-control"></div>
  <button class="btn btn-primary">Guardar</button>
</form>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
