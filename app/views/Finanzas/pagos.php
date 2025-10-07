<?php
// app/views/finanzas/pagos.php
$finanzaController = new FinanzaController($db);
$pagos = $finanzaController->listarPagos();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-money-bill-wave me-2"></i>Registro de Pagos</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="index.php?page=crear_pago&tipo=Ingreso" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Nuevo Ingreso
            </a>
            <a href="index.php?page=crear_pago&tipo=Egreso" class="btn btn-danger">
                <i class="fas fa-minus me-2"></i>Nuevo Egreso
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Historial de Pagos
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Referencia</th>
                        <th>Descripción</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagos as $pago): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                        <td>
                            <span class="badge bg-<?= $pago['tipo_pago'] == 'Ingreso' ? 'success' : 'danger' ?>">
                                <?= $pago['tipo_pago'] ?>
                            </span>
                        </td>
                        <td><?= $pago['referencia'] ?></td>
                        <td><?= $pago['descripcion'] ?></td>
                        <td class="<?= $pago['tipo_pago'] == 'Ingreso' ? 'text-success' : 'text-danger' ?>">
                            <strong>$<?= number_format($pago['monto'], 2) ?></strong>
                        </td>
                        <td>
                            <span class="badge bg-info"><?= $pago['metodo_pago'] ?></span>
                        </td>
                        <td><?= $pago['usuario_nombre'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>