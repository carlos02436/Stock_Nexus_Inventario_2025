<?php require __DIR__ . '/../layouts/header.php'; ?>
<h2>Reportes</h2>
<p><a class="btn btn-sm btn-primary" href="/public/index.php?module=reports&action=generateInventoryPdf">Generar reporte inventario (PDF)</a></p>
<h3>Reportes generados</h3>
<table class="table"><thead><tr><th>ID</th><th>Nombre</th><th>Archivo</th><th>Fecha</th></tr></thead><tbody>
<?php foreach($logs as $l): ?><tr><td><?= $l['id'] ?></td><td><?= htmlspecialchars($l['report_name']) ?></td><td><?= htmlspecialchars(basename($l['file_path'])) ?></td><td><?= $l['created_at'] ?></td></tr><?php endforeach; ?>
</tbody></table>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
