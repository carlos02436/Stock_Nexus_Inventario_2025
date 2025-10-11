<?php
require_once __DIR__ . '/../../../config/database.php';

// Obtener datos del estado de resultados
$query = "SELECT fecha_balance, total_ingresos, total_egresos, utilidad 
          FROM balance_general 
          ORDER BY fecha_balance DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$balances = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Resultados - Stock Nexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Estado de Resultados</h2>
        
        <div class="mb-3">
            <a href="index.php?page=generar_pdf_estado_resultado.php" class="btn btn-neon">Generar PDF</a>
            <a href="index.php?page=dashboard.php" class="btn btn-secondary">Volver</a>
        </div>

        <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="tablaBalances">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Total Ingresos</th>
                                        <th>Total Egresos</th>
                                        <th>Utilidad Neta</th>
                                        <th>Margen %</th>
                                        </tr>
                </thead>
                <tbody>
                    <?php foreach ($balances as $fila): ?>
                    <tr>
                        <td><?php echo $fila['fecha_balance']; ?></td>
                        <td>$<?php echo number_format($fila['total_ingresos'], 2); ?></td>
                        <td>$<?php echo number_format($fila['total_egresos'], 2); ?></td>
                        <td><strong>$<?php echo number_format($fila['utilidad'], 2); ?></strong></td>
                        <td>
                            <?php 
                            $margen = ($fila['utilidad'] / $fila['total_ingresos']) * 100;
                            echo number_format($margen, 2) . '%';
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>