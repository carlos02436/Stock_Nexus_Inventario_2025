<?php
// app/views/proveedores/eliminar_proveedor.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$proveedorController = new ProveedorController($db);

// Obtener ID del proveedor
$id = $_GET['id'] ?? 0;
if (!$id) {
    header("Location: index.php?page=proveedores");
    exit;
}

// Obtener datos del proveedor
$proveedor = $proveedorController->obtener($id);
if (!$proveedor) {
    $_SESSION['mensaje'] = "Proveedor no encontrado";
    $_SESSION['tipo_mensaje'] = "error";
    header("Location: index.php?page=proveedores");
    exit;
}

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirmar']) && $_POST['confirmar'] === 'si') {
        $eliminado = $proveedorController->eliminar($id);
        
        if ($eliminado) {
            $_SESSION['mensaje'] = "Proveedor eliminado correctamente";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "No se puede eliminar el proveedor porque tiene compras asociadas";
            $_SESSION['tipo_mensaje'] = "error";
        }
        
        header("Location: index.php?page=proveedores");
        exit;
    } else {
        header("Location: index.php?page=proveedores");
        exit;
    }
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-trash-alt me-2 text-danger"></i>Eliminar Proveedor</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=proveedores" class="btn btn-secondary px-3 py-2">
                <i class="fas fa-arrow-left me-2"></i>Volver a Proveedores
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header text-white">
                    <h5 class="card-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <i class="fas fa-trash-alt fa-4x text-danger mb-3"></i>
                        <h4 class="text-white">¿Está seguro de eliminar este proveedor?</h4>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Advertencia:</strong> Esta acción no se puede deshacer.
                    </div>

                    <!-- Información del proveedor -->
                    <div class="bg-danger mb-4 rounded-3 text-white">
                        <div class="card-body text-start">
                            <h6 class="card-title">Información del Proveedor:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Nombre:</strong><br>
                                    <?= htmlspecialchars($proveedor['nombre_proveedor']) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>NIT:</strong><br>
                                    <?= htmlspecialchars($proveedor['nit'] ?? 'No especificado') ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <strong>Teléfono:</strong><br>
                                    <?= htmlspecialchars($proveedor['telefono'] ?? 'No especificado') ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Ciudad:</strong><br>
                                    <?= htmlspecialchars($proveedor['ciudad'] ?? 'No especificado') ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="index.php?page=proveedores" class="btn btn-danger me-2 px-4">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" name="confirmar" value="si" class="btn btn-neon px-4">
                                <i class="fas fa-trash me-2"></i>Eliminar Proveedor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>