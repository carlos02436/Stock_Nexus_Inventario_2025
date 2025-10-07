<?php
// app/views/clientes/crear_cliente.php
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-plus me-2"></i>Crear Nuevo Cliente</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=clientes" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?page=crear_cliente">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nombre_cliente" class="form-label">Nombre del Cliente *</label>
                        <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="identificacion" class="form-label">Identificación</label>
                        <input type="text" class="form-control" id="identificacion" name="identificacion">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="correo" class="form-label">Email</label>
                        <input type="email" class="form-control" id="correo" name="correo">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="ciudad" class="form-label">Ciudad</label>
                        <input type="text" class="form-control" id="ciudad" name="ciudad">
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Guardar Cliente
                </button>
                <a href="index.php?page=clientes" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>