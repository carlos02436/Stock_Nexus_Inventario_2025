<?php
// app/views/proveedores/editar_proveedor.php
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
        $actualizado = $proveedorController->actualizar($id, $datos);
        
        if ($actualizado) {
            $_SESSION['mensaje'] = "Proveedor actualizado correctamente";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?page=proveedores");
            exit;
        } else {
            $error = "Error al actualizar el proveedor. Verifique los datos e intente nuevamente.";
        }
    }
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-edit me-2 text-warning"></i>Editar Proveedor</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=proveedores" class="boton3 text-decoration-none">
                <div class="boton-top3">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Proveedores
                </div>
                <div class="boton-bottom3"></div>
                <div class="boton-base3"></div>
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
                        <i class="fas fa-info-circle me-2"></i>Editar Información del Proveedor
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nombre_proveedor" class="form-label fw-bold text-white">Nombre del Proveedor *</label>
                                    <input type="text" class="form-control" id="nombre_proveedor" name="nombre_proveedor" 
                                           value="<?= htmlspecialchars($_POST['nombre_proveedor'] ?? $proveedor['nombre_proveedor']) ?>" 
                                           required maxlength="100" placeholder="Ingrese el nombre completo del proveedor">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nit" class="form-label fw-bold text-white">NIT</label>
                                    <input type="text" class="form-control" id="nit" name="nit" 
                                           value="<?= htmlspecialchars($_POST['nit'] ?? $proveedor['nit']) ?>" 
                                           maxlength="30" placeholder="Número de identificación tributaria">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefono" class="form-label fw-bold text-white">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           value="<?= htmlspecialchars($_POST['telefono'] ?? $proveedor['telefono']) ?>" 
                                           maxlength="20" placeholder="Número de contacto">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="correo" class="form-label fw-bold text-white">Email</label>
                                    <input type="email" class="form-control" id="correo" name="correo" 
                                           value="<?= htmlspecialchars($_POST['correo'] ?? $proveedor['correo']) ?>" 
                                           maxlength="100" placeholder="correo@ejemplo.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ciudad" class="form-label fw-bold text-white">Ciudad</label>
                                    <input type="text" class="form-control" id="ciudad" name="ciudad" 
                                           value="<?= htmlspecialchars($_POST['ciudad'] ?? $proveedor['ciudad']) ?>" 
                                           maxlength="100" placeholder="Ciudad del proveedor">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label fw-bold text-white">Dirección</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="3" 
                                      maxlength="150" placeholder="Dirección completa del proveedor"><?= htmlspecialchars($_POST['direccion'] ?? $proveedor['direccion']) ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4 pt-3 border-top">
                            <!-- Botón Actualizar - Verde -->
                            <button type="submit" class="boton1 text-decoration-none">
                                <div class="boton-top1">
                                    <i class="fas fa-save me-2"></i>Actualizar Proveedor
                                </div>
                                <div class="boton-bottom1"></div>
                                <div class="boton-base1"></div>
                            </button>

                            <!-- Botón Cancelar - Rojo -->
                            <a href="index.php?page=proveedores" class="boton2 me-2 text-decoration-none">
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