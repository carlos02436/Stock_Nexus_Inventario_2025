<?php require __DIR__ . '/../layouts/header.php'; ?>
<h2>Registrar Movimiento (Ingreso/Salida/Ajuste)</h2>
<form method="POST" action="/public/index.php?module=movements&action=store">
  <div class="mb-3"><label>Producto</label>
    <select name="product_id" class="form-control">
      <?php foreach($products as $p): ?><option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?> (<?= $p['sku'] ?>)</option><?php endforeach; ?>
    </select></div>
  <div class="mb-3"><label>Tipo</label>
    <select name="type" class="form-control"><option value="ingreso">Ingreso</option><option value="salida">Salida</option><option value="ajuste">Ajuste</option></select></div>
  <div class="mb-3"><label>Cantidad</label><input name="quantity" type="number" class="form-control"></div>
  <div class="mb-3"><label>Costo unitario</label><input name="unit_cost" type="number" step="0.01" class="form-control"></div>
  <div class="mb-3"><label>Nota</label><input name="note" class="form-control"></div>
  <button class="btn btn-primary">Registrar</button>
</form>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
