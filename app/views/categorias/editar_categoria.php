<?php
// app/views/categorias/editar_categoria.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$categoriaController = new CategoriaController($db);

// Obtener ID de la categoría
$id = $_GET['id'] ?? 0;
if (!$id) {
    header("Location: index.php?page=categorias");
    exit;
}

// Obtener datos de la categoría
$categoria = $categoriaController->obtener($id);
if (!$categoria) {
    $_SESSION['mensaje'] = "Categoría no encontrada";
    $_SESSION['tipo_mensaje'] = "error";
    header("Location: index.php?page=categorias");
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre_categoria' => trim($_POST['nombre_categoria']),
        'descripcion' => trim($_POST['descripcion']),
        'estado' => $_POST['estado']
    ];

    // Validaciones básicas
    if (empty($datos['nombre_categoria'])) {
        $error = "El nombre de la categoría es obligatorio";
    } else {
        $actualizado = $categoriaController->actualizar($id, $datos);
        
        if ($actualizado) {
            $_SESSION['mensaje'] = "Categoría actualizada correctamente";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?page=categorias");
            exit;
        } else {
            $error = "Error al actualizar la categoría. Verifique los datos e intente nuevamente.";
        }
    }
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-edit me-2 text-warning"></i>Editar Categoría</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=categorias" class="btn btn-secondary rounded-3 px-3 py-2">
                <i class="fas fa-arrow-left me-2"></i>Volver a Categorías
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
                        <i class="fas fa-info-circle me-2"></i>Editar Información de la Categoría
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nombre_categoria" class="form-label fw-bold text-white">Nombre de la Categoría *</label>
                                    <input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria" 
                                           value="<?= htmlspecialchars($_POST['nombre_categoria'] ?? $categoria['nombre_categoria']) ?>" 
                                           required maxlength="100" placeholder="Ingrese el nombre de la categoría">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="estado" class="form-label fw-bold text-white">Estado</label>
                                    <select class="form-select" id="estado" name="estado">
                                        <option value="Activo" <?= ($_POST['estado'] ?? $categoria['estado']) === 'Activo' ? 'selected' : '' ?>>Activo</option>
                                        <option value="Inactivo" <?= ($_POST['estado'] ?? $categoria['estado']) === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-bold text-white">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                      maxlength="500" placeholder="Descripción de la categoría"><?= htmlspecialchars($_POST['descripcion'] ?? $categoria['descripcion']) ?></textarea>
                            <div class="form-text">Máximo 500 caracteres</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-neon px-4">
                                <i class="fas fa-save me-2"></i>Actualizar Categoría
                            </button>
                            <a href="index.php?page=categorias" class="btn btn-danger me-2 px-4">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>