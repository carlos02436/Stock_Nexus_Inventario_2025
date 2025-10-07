<?php
// app/views/categorias/crear_categoria.php
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
     style="margin-top:120px;">
    <h1 class="h2"><i class="fas fa-plus me-2"></i>Crear Nueva Categoría</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=categorias" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?page=crear_categoria">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nombre_categoria" class="form-label">Nombre de la Categoría *</label>
                        <input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="Activo" selected>Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Guardar Categoría
                </button>
                <a href="index.php?page=categorias" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>