<?php
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}

$moduloController = new ModuloController($db);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre_modulo' => trim($_POST['nombre_modulo']),
        'descripcion' => trim($_POST['descripcion']),
        'icono' => trim($_POST['icono']),
        'ruta' => trim($_POST['ruta'])
    ];

    $resultado = $moduloController->crear($datos);
    
    if ($resultado) {
        $_SESSION['mensaje'] = 'Módulo creado exitosamente';
        $_SESSION['mensaje_tipo'] = 'success';
        header("Location: index.php?page=modulos");
        exit;
    } else {
        $error = "Error al crear el módulo. Puede que ya exista un módulo con ese nombre.";
    }
}
?>

<body>
    <div class="container-fluid px-4 mb-5" style="margin-top:180px;">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="fas fa-plus-circle me-2"></i>Crear Nuevo Módulo</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="index.php?page=modulos" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Módulos
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cube me-2"></i>Información del Módulo
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="formCrearModulo">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nombre_modulo" class="form-label text-white">Nombre del Módulo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre_modulo" name="nombre_modulo" 
                                           value="<?= isset($_POST['nombre_modulo']) ? htmlspecialchars($_POST['nombre_modulo']) : '' ?>" 
                                           required maxlength="100">
                                    <div class="form-text text-white">Ej: Usuarios, Inventario, Reportes</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="icono" class="form-label text-white">Icono <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="icono" name="icono" 
                                           value="<?= isset($_POST['icono']) ? htmlspecialchars($_POST['icono']) : '' ?>" 
                                           required maxlength="50">
                                    <div class="form-text text-white">Nombre del icono de FontAwesome (sin el "fa-")</div>
                                </div>

                                <div class="col-12">
                                    <label for="descripcion" class="form-label text-white">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                              maxlength="255"><?= isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : '' ?></textarea>
                                </div>

                                <div class="col-12">
                                    <label for="ruta" class="form-label text-white">Ruta</label>
                                    <input type="text" class="form-control" id="ruta" name="ruta" 
                                           value="<?= isset($_POST['ruta']) ? htmlspecialchars($_POST['ruta']) : '' ?>" 
                                           maxlength="255">
                                    <div class="form-text text-white">Ej: /usuarios, index.php?page=inventario</div>
                                </div>

                                <div class="alert alert-success">
                                    <small>
                                        <i class="fas fa-info-circle me-2"></i>
                                        Los campos marcados con * son obligatorios.
                                    </small>
                                </div>

                                <div class="col-12">
                                    <hr class="text-white">
                                    <div class="d-flex justify-content-center gap-3">
                                        <a href="index.php?page=modulos" class="btn btn-danger">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-neon">
                                            <i class="fas fa-save me-2"></i>Crear Módulo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preview del icono
        const iconoInput = document.getElementById('icono');
        const iconoPreview = document.createElement('div');
        iconoPreview.className = 'form-text text-white mt-1';
        iconoPreview.innerHTML = '<i class="fas fa-question me-1"></i> Vista previa del icono';
        iconoInput.parentNode.appendChild(iconoPreview);

        iconoInput.addEventListener('input', function() {
            const iconName = this.value.trim();
            if (iconName) {
                iconoPreview.innerHTML = `<i class="fas fa-${iconName} me-1"></i> Vista previa: fa-${iconName}`;
            } else {
                iconoPreview.innerHTML = '<i class="fas fa-question me-1"></i> Vista previa del icono';
            }
        });
    });
    </script>
<main>