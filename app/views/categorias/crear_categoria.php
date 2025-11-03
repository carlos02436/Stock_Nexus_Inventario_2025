<?php
// app/views/categorias/crear_categoria.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$categoriaController = new CategoriaController($db);

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
        $creado = $categoriaController->crear($datos);
        
        if ($creado) {
            $_SESSION['mensaje'] = "Categoría creada correctamente";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?page=categorias");
            exit;
        } else {
            $error = "Error al crear la categoría. Verifique los datos e intente nuevamente.";
        }
    }
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-plus-circle me-2 text-success"></i>Crear Nueva Categoría</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=categorias" class="boton3 text-decoration-none" style="width: auto; min-width: 160px;">
                <div class="boton-top3">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Categorías
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
                        <i class="fas fa-info-circle me-2"></i>Información de la Categoría
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nombre_categoria" class="form-label fw-bold text-white">Nombre de la Categoría *</label>
                                    <input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria" 
                                           value="<?= htmlspecialchars($_POST['nombre_categoria'] ?? '') ?>" 
                                           required maxlength="100" placeholder="Ingrese el nombre de la categoría">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="estado" class="form-label fw-bold text-white">Estado</label>
                                    <select class="form-select" id="estado" name="estado">
                                        <option value="Activo" <?= ($_POST['estado'] ?? 'Activo') === 'Activo' ? 'selected' : '' ?>>Activo</option>
                                        <option value="Inactivo" <?= ($_POST['estado'] ?? '') === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-bold text-white">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                      maxlength="500" placeholder="Descripción de la categoría"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
                            <div class="form-text text-white">Máximo 500 caracteres</div>
                        </div>

                        <div class="alert alert-success my-4">
                            <small>
                                <i class="fas fa-info-circle me-2"></i>
                                Los campos marcados con * son obligatorios. La contraseña solo es necesaria si desea cambiarla.
                            </small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4 pt-3 border-top">
                            <!-- Botón Guardar Categoría - Verde (button) -->
                            <button type="submit" class="boton1 text-decoration-none" style="width: auto; min-width: 160px;">
                                <span class="boton-top1">
                                    <i class="fas fa-save me-2"></i>Guardar Categoría
                                </span>
                                <span class="boton-bottom1"></span>
                                <span class="boton-base1"></span>
                            </button>

                            <!-- Botón Cancelar - Rojo (boton2) -->
                            <a href="index.php?page=categorias" class="boton2 text-decoration-none me-2" style="width: auto; min-width: 140px;">
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