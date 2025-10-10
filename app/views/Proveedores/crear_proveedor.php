<?php
// app/views/proveedores/crear_proveedor.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$proveedorController = new ProveedorController($db);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre_proveedor' => trim($_POST['nombre_proveedor']),
        'nit' => trim($_POST['nit']),
        'telefono' => trim($_POST['telefono']),
        'correo' => trim($_POST['correo']),
        'direccion' => trim($_POST['direccion']),
        'ciudad' => trim($_POST['ciudad'])
    ];

    // Validaciones básicas
    if (empty($datos['nombre_proveedor'])) {
        $error = "El nombre del proveedor es obligatorio";
    } else {
        $creado = $proveedorController->crear($datos);
        
        if ($creado) {
            $_SESSION['mensaje'] = "Proveedor creado correctamente";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?page=proveedores");
            exit;
        } else {
            $error = "Error al crear el proveedor. Verifique los datos e intente nuevamente.";
        }
    }
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-plus-circle me-2 text-success"></i>Crear Nuevo Proveedor</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=proveedores" class="btn btn-secondary rounded-3 px-3 py-2">
                <i class="fas fa-arrow-left me-2"></i>Volver a Proveedores
            </a>
        </div>
    </div>

    <!-- Mostrar mensajes de error -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Información del Proveedor
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nombre_proveedor" class="form-label fw-bold text-white">Nombre del Proveedor *</label>
                                    <input type="text" class="form-control" id="nombre_proveedor" name="nombre_proveedor" 
                                           value="<?= htmlspecialchars($_POST['nombre_proveedor'] ?? '') ?>" 
                                           required maxlength="100" placeholder="Ingrese el nombre completo del proveedor">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nit" class="form-label fw-bold text-white">NIT</label>
                                    <input type="text" class="form-control" id="nit" name="nit" 
                                           value="<?= htmlspecialchars($_POST['nit'] ?? '') ?>" 
                                           maxlength="30" placeholder="Número de identificación tributaria">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefono" class="form-label fw-bold text-white">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>" 
                                           maxlength="20" placeholder="Número de contacto">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="correo" class="form-label fw-bold text-white">Email</label>
                                    <input type="email" class="form-control" id="correo" name="correo" 
                                           value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" 
                                           maxlength="100" placeholder="correo@ejemplo.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ciudad" class="form-label fw-bold text-white">Ciudad</label>
                                    <input type="text" class="form-control" id="ciudad" name="ciudad" 
                                           value="<?= htmlspecialchars($_POST['ciudad'] ?? '') ?>" 
                                           maxlength="100" placeholder="Ciudad del proveedor">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label fw-bold text-white">Dirección</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="3" 
                                      maxlength="150" placeholder="Dirección completa del proveedor"><?= htmlspecialchars($_POST['direccion'] ?? '') ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-neon px-4">
                                <i class="fas fa-save me-2"></i>Guardar Proveedor
                            </button>
                            <a href="index.php?page=proveedores" class="btn btn-danger me-2 px-4">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>