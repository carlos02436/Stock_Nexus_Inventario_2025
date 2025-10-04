<?php require __DIR__ . '/../layouts/header.php'; ?>
<h2>Crear Producto</h2>
<form method="POST" action="/public/index.php?module=products&action=store">
  <div class="mb-3"><label>SKU</label><input name="sku" class="form-control" required></div>
  <div class="mb-3"><label>Nombre</label><input name="nombre" class="form-control" required></div>
  <div class="mb-3"><label>Descripción</label><input name="descripcion" class="form-control"></div>
  <div class="mb-3"><label>Categoría</label>
    <select name="category_id" class="form-control">
      <option value="">--</option>
      <?php foreach($categories as $c): ?>
      <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3"><label>Proveedor</label>
    <select name="supplier_id" class="form-control">
      <option value="">--</option>
      <?php foreach($suppliers as $s): ?>
      <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3"><label>Costo unitario</label><input name="costo_unitario" type="number" step="0.01" class="form-control"></div>
  <div class="mb-3"><label>Precio venta</label><input name="precio_venta" type="number" step="0.01" class="form-control"></div>
  <div class="mb-3"><label>Stock inicial</label><input name="stock_actual" type="number" class="form-control"></div>
  <div class="mb-3"><label>Unidad</label><input name="unidad_medida" class="form-control"></div>
  <button class="btn btn-primary">Guardar</button>
</form>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
