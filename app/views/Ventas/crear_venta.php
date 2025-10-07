<?php
// app/views/ventas/crear_venta.php
$clienteController = new ClienteController($db);
$productoController = new ProductoController($db);

$clientes = $clienteController->listar();
$productos = $productoController->listar();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-plus me-2"></i>Registrar Nueva Venta</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=ventas" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?page=crear_venta" id="formVenta">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="codigo_venta" class="form-label">Código de Venta *</label>
                        <input type="text" class="form-control" id="codigo_venta" name="codigo_venta" 
                               value="V<?= date('YmdHis') ?>" required readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="id_cliente" class="form-label">Cliente</label>
                        <select class="form-select" id="id_cliente" name="id_cliente">
                            <option value="">Cliente General</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['id_cliente'] ?>"><?= $cliente['nombre_cliente'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="metodo_pago" class="form-label">Método de Pago *</label>
                        <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Crédito">Crédito</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Productos -->
            <div class="mb-4">
                <h5 class="mb-3"><i class="fas fa-boxes me-2"></i>Productos de la Venta</h5>
                <div id="productos-container">
                    <div class="producto-item row mb-3">
                        <div class="col-md-4">
                            <select class="form-select producto-select" name="productos[0][id_producto]" required>
                                <option value="">Seleccionar producto</option>
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?= $producto['id_producto'] ?>" 
                                            data-precio="<?= $producto['precio_venta'] ?>"
                                            data-stock="<?= $producto['stock_actual'] ?>">
                                        <?= $producto['nombre_producto'] ?> - Stock: <?= $producto['stock_actual'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control cantidad" name="productos[0][cantidad]" 
                                   placeholder="Cantidad" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control precio" name="productos[0][precio_unitario]" 
                                   placeholder="Precio unitario" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control subtotal" readonly placeholder="Subtotal">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn