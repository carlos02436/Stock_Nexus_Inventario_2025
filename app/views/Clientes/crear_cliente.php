<?php
// app/views/clientes/crear_cliente.php
?>
<div class="container-fluid px-4" style="margin-top: 180px; margin-bottom: 50px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2"><i class="fas fa-plus me-2"></i>Crear Nuevo Cliente</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=clientes" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Clientes
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="index.php?page=crear_cliente">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3 text-white">
                                    <label for="nombre_cliente" class="form-label">Nombre del Cliente *</label>
                                    <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="identificacion" class="form-label">Identificación</label>
                                    <input type="text" class="form-control" id="identificacion" name="identificacion">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3 text-white">
                                    <label for="correo" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="correo" name="correo">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="ciudad" class="form-label">Ciudad</label>
                                    <input type="text" class="form-control" id="ciudad" name="ciudad">
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success my-3">
                           <small>
                               <i class="fas fa-info-circle me-2"></i>
                               Los campos marcados con * son obligatorios.
                            </small>
                        </div> 

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                            <button type="submit" class="btn btn-neon">
                                <i class="fas fa-save me-2"></i>Guardar Cliente
                            </button>
                            <a href="index.php?page=clientes" class="btn btn-danger">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>