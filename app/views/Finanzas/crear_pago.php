<?php
// app/views/finanzas/crear_pago.php
$tipo = $_GET['tipo'] ?? 'Ingreso';
$color = $tipo == 'Ingreso' ? 'success' : 'danger';
$icono = $tipo == 'Ingreso' ? 'plus-circle' : 'minus-circle';
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-<?= $icono ?> me-2 text-<?= $color ?>"></i>
        Registrar <?= $tipo ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=pagos" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?page=crear_pago">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tipo_pago" class="form-label">Tipo de Pago *</label>
                        <select class="form-select" id="tipo_pago" name="tipo_pago" required>
                            <option value="Ingreso" <?= $tipo == 'Ingreso' ? 'selected' : '' ?>>Ingreso</option>
                            <option value="Egreso" <?= $tipo == 'Egreso' ? 'selected' : '' ?>>Egreso</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="metodo_pago" class="form-label">Método de Pago *</label>
                        <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Tarjeta">Tarjeta</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="referencia" class="form-label">Referencia</label>
                        <input type="text" class="form-control" id="referencia" name="referencia" 
                               placeholder="Número de factura, recibo, etc.">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto *</label>
                        <input type="number" class="form-control" id="monto" name="monto" 
                               step="0.01" min="0" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción *</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                          placeholder="Descripción detallada del pago" required></textarea>
            </div>

            <input type="hidden" name="id_usuario" value="<?= $_SESSION['usuario_id'] ?>">

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-<?= $color ?>">
                    <i class="fas fa-save me-2"></i>Registrar <?= $tipo ?>
                </button>
                <a href="index.php?page=pagos" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>