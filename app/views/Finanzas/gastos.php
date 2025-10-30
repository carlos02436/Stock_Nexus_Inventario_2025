<?php
// app/views/finanzas/gastos.php
require_once __DIR__ . '/../../helpers/PermisoHelper.php';
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
     style="margin-top:180px;">
    <h1 class="h2"><i class="fas fa-money-bill-wave me-2"></i>Gastos Operativos</h1>
    <?php if (PermisoHelper::puede('Finanzas', 'crear')): ?>
        <a href="index.php?page=crear_gasto" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Nuevo Gasto
        </a>
    <?php endif; ?>
</div>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['tipo_mensaje'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
        <?= $_SESSION['mensaje'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (empty($gastos)): ?>
            <div class="text-center py-5">
                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No hay gastos registrados</h4>
                <p class="text-muted">Comienza agregando tu primer gasto operativo.</p>
                <?php if (PermisoHelper::puede('Finanzas', 'crear')): ?>
                    <a href="index.php?page=crear_gasto" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Crear Primer Gasto
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Valor</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gastos as $gasto): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($gasto['fecha'])) ?></td>
                                <td>
                                    <span class="badge bg-info"><?= htmlspecialchars($gasto['categoria'] ?? 'Sin categoría') ?></span>
                                </td>
                                <td><?= htmlspecialchars($gasto['descripcion'] ?? '') ?></td>
                                <td class="text-end">
                                    <strong>$<?= number_format($gasto['valor'], 2) ?></strong>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <?php if (PermisoHelper::puede('Finanzas', 'editar')): ?>
                                            <a href="index.php?page=editar_gasto&id=<?= $gasto['id_gasto'] ?>" 
                                               class="btn btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (PermisoHelper::puede('Finanzas', 'eliminar')): ?>
                                            <button onclick="confirmarEliminacion(<?= $gasto['id_gasto'] ?>)" 
                                                    class="btn btn-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td class="text-end">
                                <strong>$<?= number_format(array_sum(array_column($gastos, 'valor')), 2) ?></strong>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmarEliminacion(idGasto) {
    if (confirm('¿Estás seguro de que deseas eliminar este gasto? Esta acción no se puede deshacer.')) {
        window.location.href = 'index.php?page=eliminar_gasto&id=' + idGasto;
    }
}
</script>