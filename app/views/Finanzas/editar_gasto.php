<?php
// app/views/finanzas/editar_gasto.php

// Debug: Verificar si la variable $gasto existe
// echo "<pre>"; print_r($gasto); echo "</pre>"; // Descomenta para debug
?>
<div class="container-fluid px-4" style="margin-top: 180px; margin-bottom: 100px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2"><i class="fas fa-edit me-2"></i>Editar Gasto</h1>
        <a href="index.php?page=gastos" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver a Gastos
        </a>
    </div>

    <?php if (!isset($gasto) || empty($gasto)): ?>
        <div class="alert alert-danger mx-2">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Error: No se pudo cargar la información del gasto. 
            <a href="index.php?page=gastos" class="alert-link">Volver a la lista de gastos</a>
        </div>
    <?php else: ?>
        <div class="row justify-content-center mx-2">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header text-white py-3">
                        <h5 class="card-title mb-0">Editar Información del Gasto</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="index.php?page=editar_gasto&id=<?= $gasto['id_gasto'] ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 text-white">
                                        <label for="fecha" class="form-label">Fecha *</label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" 
                                               value="<?= htmlspecialchars($gasto['fecha'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 text-white">
                                        <label for="categoria" class="form-label">Categoría</label>
                                        <input type="text" class="form-control" id="categoria" name="categoria" 
                                               value="<?= htmlspecialchars($gasto['categoria'] ?? '') ?>" 
                                               placeholder="Ej: Servicios, Nómina, Suministros...">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 text-white">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" 
                                          rows="3" placeholder="Descripción detallada del gasto..."><?= htmlspecialchars($gasto['descripcion'] ?? '') ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 text-white">
                                        <label for="valor" class="form-label">Valor *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="valor" name="valor" 
                                                   step="0.01" min="0" value="<?= htmlspecialchars($gasto['valor'] ?? '0') ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <a href="index.php?page=gastos" class="btn btn-danger me-md-2">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-neon">
                                    <i class="fas fa-save me-2"></i>Actualizar Gasto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>