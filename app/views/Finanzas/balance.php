<?php
// app/views/finanzas/balance.php
$balanceModel = new BalanceGeneral($db);
$balances = $balanceModel->listarBalances(12);
$balanceActual = $balanceModel->obtenerBalanceActual();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
     style="margin-top:120px;">
    <h1 class="h2"><i class="fas fa-balance-scale me-2"></i>Balance General</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print me-2"></i>Imprimir
        </button>
    </div>
</div>

<?php if ($balanceActual): ?>
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="text-center">
                    <h5>Total Ingresos</h5>
                    <h3>$<?= number_format($balanceActual['total_ingresos'], 2) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="text-center">
                    <h5>Total Egresos</h5>
                    <h3>$<?= number_format($balanceActual['total_egresos'], 2) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="text-center">
                    <h5>Utilidad Neta</h5>
                    <h3>$<?= number_format($balanceActual['utilidad'], 2) ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-chart-bar me-2"></i>Historial de Balances (Últimos 12 meses)
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Ingresos</th>
                        <th>Egresos</th>
                        <th>Utilidad</th>
                        <th>Margen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($balances as $balance): ?>
                    <?php 
                    $margen = $balance['total_ingresos'] > 0 ? 
                        ($balance['utilidad'] / $balance['total_ingresos']) * 100 : 0;
                    ?>
                    <tr>
                        <td><?= date('m/Y', strtotime($balance['fecha_balance'])) ?></td>
                        <td class="text-success">$<?= number_format($balance['total_ingresos'], 2) ?></td>
                        <td class="text-danger">$<?= number_format($balance['total_egresos'], 2) ?></td>
                        <td class="text-primary"><strong>$<?= number_format($balance['utilidad'], 2) ?></strong></td>
                        <td>
                            <span class="badge bg-<?= $margen >= 20 ? 'success' : ($margen >= 10 ? 'warning' : 'danger') ?>">
                                <?= number_format($margen, 1) ?>%
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Gráfico de Tendencia</h5>
    </div>
    <div class="card-body">
        <canvas id="balanceChart" width="400" height="150"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('balanceChart').getContext('2d');
    const balanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [<?= implode(',', array_map(function($b) { 
                return "'" . date('m/Y', strtotime($b['fecha_balance'])) . "'"; 
            }, array_reverse($balances))) ?>],
            datasets: [
                {
                    label: 'Ingresos',
                    data: [<?= implode(',', array_map(function($b) { 
                        return $b['total_ingresos']; 
                    }, array_reverse($balances))) ?>],
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    fill: true
                },
                {
                    label: 'Egresos',
                    data: [<?= implode(',', array_map(function($b) { 
                        return $b['total_egresos']; 
                    }, array_reverse($balances))) ?>],
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    fill: true
                },
                {
                    label: 'Utilidad',
                    data: [<?= implode(',', array_map(function($b) { 
                        return $b['utilidad']; 
                    }, array_reverse($balances))) ?>],
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>