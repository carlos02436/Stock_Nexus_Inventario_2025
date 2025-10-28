<?php
// app/views/finanzas/crear_pago.php
$tipo = $_GET['tipo'] ?? 'Ingreso';
$color = $tipo == 'Ingreso' ? 'success' : 'danger';
$icono = $tipo == 'Ingreso' ? 'plus-circle' : 'minus-circle';
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-<?= $icono ?> me-2 text-<?= $color ?>"></i>
            Registrar <?= $tipo ?>
        </h1>
        <div class="d-flex gap-2 mb-md-2">
            <a href="index.php?page=pagos" class="btn btn-secondary rounded-3 px-3 py-2">
                <i class="fas fa-arrow-left me-2"></i>Volver a Pagos
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="index.php?page=crear_pago">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipo_pago" class="form-label fw-bold text-white">Tipo de Pago *</label>
                                    <select class="form-select" id="tipo_pago" name="tipo_pago" required>
                                        <option value="Ingreso" <?= $tipo == 'Ingreso' ? 'selected' : '' ?>>Ingreso</option>
                                        <option value="Egreso" <?= $tipo == 'Egreso' ? 'selected' : '' ?>>Egreso</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="metodo_pago" class="form-label fw-bold text-white">Método de Pago *</label>
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
                                    <label for="referencia" class="form-label fw-bold text-white">Referencia</label>
                                    <input type="text" class="form-control" id="referencia" name="referencia" 
                                           placeholder="Número de factura, recibo, etc.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="monto" class="form-label fw-bold text-white">Monto *</label>
                                    <input type="number" class="form-control" id="monto" name="monto" 
                                           step="0.01" min="0" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="form-label fw-bold text-white">Descripción *</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                      placeholder="Descripción detallada del pago" required></textarea>
                        </div>

                        <div class="alert alert-success">
                            <small>
                                <i class="fas fa-info-circle me-2"></i>
                                Los campos marcados con * son obligatorios.
                            </small>
                        </div>

                        <input type="hidden" name="id_usuario" value="<?= $_SESSION['usuario_id'] ?>">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-neon px-4">
                                <i class="fas fa-save me-2"></i>Registrar <?= $tipo ?>
                            </button>
                            <a href="index.php?page=pagos" class="btn btn-danger me-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>