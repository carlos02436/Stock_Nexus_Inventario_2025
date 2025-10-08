<?php
// app/views/movimientos/crear_movimiento.php
$productoController = new ProductoController($db);
$productos = $productoController->listar();
?>

<div class="container-fluid px-4 pb-5">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
         style="margin-top:120px;">
        <h1 class="h2"><i class="fas fa-plus me-2"></i>Registrar Movimiento</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=movimientos" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="index.php?page=crear_movimiento">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="id_producto" class="form-label text-white">Producto *</label>
                                    <select class="form-select" id="id_producto" name="id_producto" required>
                                        <option value="">Seleccionar producto</option>
                                        <?php foreach ($productos as $producto): ?>
                                            <option value="<?= $producto['id_producto'] ?>">
                                                <?= $producto['nombre_producto'] ?> (Stock: <?= $producto['stock_actual'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipo_movimiento" class="form-label text-white">Tipo de Movimiento *</label>
                                    <select class="form-select" id="tipo_movimiento" name="tipo_movimiento" required>
                                        <option value="Entrada">Entrada</option>
                                        <option value="Salida">Salida</option>
                                        <option value="Ajuste">Ajuste</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cantidad" class="form-label text-white">Cantidad *</label>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" 
                                           step="0.01" min="0.01" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label text-white">Descripción *</label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion" 
                                   placeholder="Motivo del movimiento" required>
                        </div>

                        <input type="hidden" name="id_usuario" value="<?= $_SESSION['usuario_id'] ?>">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Registrar Movimiento
                            </button>
                            <a href="index.php?page=movimientos" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>