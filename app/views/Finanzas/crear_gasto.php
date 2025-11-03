<?php
// app/views/finanzas/crear_gasto.php
?>
<div class="container-fluid px-4" style="margin-top: 180px; margin-bottom: 100px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2"><i class="fas fa-plus-circle me-2"></i>Crear Nuevo Gasto</h1>
        <a href="index.php?page=gastos" class="boton3 text-decoration-none" style="width: auto; min-width: 160px;">
            <div class="boton-top3">
                <i class="fas fa-arrow-left me-2"></i>Volver a Gastos
            </div>
            <div class="boton-bottom3"></div>
            <div class="boton-base3"></div>
        </a>
    </div>

    <div class="row justify-content-center mx-2">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header py-3 text-white">
                    <h5 class="card-title mb-0">Información del Gasto</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?page=crear_gasto">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="fecha" class="form-label">Fecha *</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" 
                                           value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="categoria" class="form-label">Categoría</label>
                                    <input type="text" class="form-control" id="categoria" name="categoria" 
                                           placeholder="Ej: Servicios, Nómina, Suministros...">
                                    <div class="form-text">Opcional. Ayuda a organizar los gastos.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 text-white">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" 
                                      rows="3" placeholder="Descripción detallada del gasto..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="valor" class="form-label">Valor *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="valor" name="valor" 
                                               step="0.01" min="0" placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success my-4">
                            <small>
                                <i class="fas fa-info-circle me-2"></i>
                                Los campos marcados con * son obligatorios.
                            </small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <button type="submit" class="boton1 text-decoration-none" style="width: auto; min-width: 150px;">
                                <span class="boton-top1">
                                    <i class="fas fa-save me-2"></i>Guardar Gasto
                                </span>
                                <span class="boton-bottom1"></span>
                                <span class="boton-base1"></span>
                            </button>
                            
                            <a href="index.php?page=gastos" class="boton2 text-decoration-none me-md-2" style="width: auto; min-width: 150px;">
                                <div class="boton-top2">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </div>
                                <div class="boton-bottom2"></div>
                                <div class="boton-base2"></div>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>