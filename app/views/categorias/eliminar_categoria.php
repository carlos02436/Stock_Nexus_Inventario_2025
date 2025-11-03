<?php
// app/views/categorias/eliminar_categoria.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$categoriaController = new CategoriaController($db);
$categoriaModel = new Categoria($db);

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

// Obtener estadísticas de productos (SIN filtrar por estado)
$totalProductos = $categoriaModel->contarProductos($id);

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirmar']) && $_POST['confirmar'] === 'si') {
        $eliminado = $categoriaController->eliminar($id);
        
        if ($eliminado) {
            $_SESSION['mensaje'] = "Categoría eliminada correctamente";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "No se puede eliminar la categoría porque tiene productos asociados";
            $_SESSION['tipo_mensaje'] = "error";
        }
        
        header("Location: index.php?page=categorias");
        exit;
    } else {
        header("Location: index.php?page=categorias");
        exit;
    }
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-trash-alt me-2 text-danger"></i>Eliminar Categoría</h1>
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

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card shadow-sm border-danger">
                <div class="card-header text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <i class="fas fa-trash-alt fa-4x text-danger mb-3"></i>
                        <h4 class="text-white">¿Está seguro de eliminar esta categoría?</h4>
                    </div>

                    <?php if ($totalProductos > 0): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>No se puede eliminar:</strong> Esta categoría tiene <?= $totalProductos ?> producto(s) asociado(s).
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Advertencia:</strong> Esta acción no se puede deshacer.
                        </div>
                    <?php endif; ?>

                    <!-- Información de la categoría -->
                    <div class="bg-danger rounded-3 mb-4 border-warning">
                        <div class="text-white rounded-top px-3 py-2">
                            <h6 class="card-title mb-0"><strong>Información de la Categoría:</strong></h6>
                        </div>
                        <div class="card-body text-start">
                            <div class="row">
                                <div class="col-md-6 text-white">
                                    <strong>Nombre:</strong><br>
                                    <?= htmlspecialchars($categoria['nombre_categoria']) ?>
                                </div>
                                <div class="col-md-6 text-white">
                                    <strong>Estado:</strong><br>
                                    <span class="badge bg-<?= $categoria['estado'] == 'Activo' ? 'success' : 'secondary' ?>">
                                        <?= $categoria['estado'] ?>
                                    </span>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12 text-white">
                                    <strong>Descripción:</strong><br>
                                    <?= htmlspecialchars($categoria['descripcion'] ?: 'Sin descripción') ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12 text-white">
                                    <strong>Productos asociados:</strong><br>
                                    <span class="<?= $totalProductos > 0 ? 'text-white fw-bold' : 'text-white' ?>">
                                        <?= $totalProductos ?> producto(s)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="index.php?page=categorias" class="boton2 text-decoration-none me-2" style="width: auto; min-width: 140px;">
                                <div class="boton-top2">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </div>
                                <div class="boton-bottom2"></div>
                                <div class="boton-base2"></div>
                            </a>

                            <!-- BOTÓN ELIMINAR SIEMPRE VISIBLE -->
                            <button type="submit" name="confirmar" value="si" class="boton1 text-decoration-none" 
                                    <?= $totalProductos > 0 ? 'disabled' : '' ?> style="width: auto; min-width: 180px;">
                                <span class="boton-top1">
                                    <i class="fas fa-trash me-2"></i>Eliminar Categoría
                                </span>
                                <span class="boton-bottom1"></span>
                                <span class="boton-base1"></span>
                            </button>
                        </div>
                    </form>

                    <?php if ($totalProductos > 0): ?>
                        <div class="mt-3">
                            <small class="text-white">
                                <i class="fas fa-info-circle me-1"></i>
                                Para eliminar esta categoría, primero debe reasignar o eliminar los productos asociados a la misma.
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>