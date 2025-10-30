<?php
// app/views/finanzas/editar_gasto.php
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
     style="margin-top:180px;">
    <h1 class="h2"><i class="fas fa-edit me-2"></i>Editar Gasto</h1>
    <a href="index.php?page=gastos" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver a Gastos
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Editar Información del Gasto</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?page=editar_gasto&id=<?= $gasto['id_gasto'] ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha *</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" 
                                       value="<?= $gasto['fecha'] ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoría</label>
                                <input type="text" class="form-control" id="categoria" name="categoria" 
                                       value="<?= htmlspecialchars($gasto['categoria'] ?? '') ?>" 
                                       placeholder="Ej: Servicios, Nómina, Suministros...">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                  rows="3" placeholder="Descripción detallada del gasto..."><?= htmlspecialchars($gasto['descripcion'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="valor" class="form-label">Valor *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="valor" name="valor" 
                                           step="0.01" min="0" value="<?= $gasto['valor'] ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php?page=gastos" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-2"></i>Actualizar Gasto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>