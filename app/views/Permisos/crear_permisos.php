<?php
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}

$permisoController = new PermisoController($db);
$rolController = new RolController($db);
$moduloController = new ModuloController($db);

// Obtener roles y módulos para los selects
$roles = $rolController->listar();
$modulos = $moduloController->listar();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_rol' => $_POST['id_rol'],
        'id_modulo' => $_POST['id_modulo'],
        'puede_ver' => isset($_POST['puede_ver']) ? 1 : 0,
        'puede_crear' => isset($_POST['puede_crear']) ? 1 : 0,
        'puede_editar' => isset($_POST['puede_editar']) ? 1 : 0,
        'puede_eliminar' => isset($_POST['puede_eliminar']) ? 1 : 0
    ];

    $resultado = $permisoController->crear($datos);
    
    if ($resultado) {
        $_SESSION['mensaje'] = 'Permiso creado exitosamente';
        $_SESSION['mensaje_tipo'] = 'success';
        header("Location: index.php?page=permisos");
        exit;
    } else {
        $error = "Error al crear el permiso";
    }
}
?>
<body>
    <div class="container-fluid px-4 mb-5" style="margin-top:180px;">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="fas fa-plus-circle me-2"></i>Crear Nuevo Permiso</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="index.php?page=permisos" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Permisos
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-key me-2"></i>Información del Permiso
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="formCrearPermiso">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="id_rol" class="form-label text-white">Rol <span class="text-danger">*</span></label>
                                    <select class="form-select" id="id_rol" name="id_rol" required>
                                        <option value="">Seleccionar rol...</option>
                                        <?php foreach ($roles as $rol): ?>
                                            <option value="<?= $rol['id_rol'] ?>" <?= isset($_POST['id_rol']) && $_POST['id_rol'] == $rol['id_rol'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($rol['nombre_rol']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="id_modulo" class="form-label text-white">Módulo <span class="text-danger">*</span></label>
                                    <select class="form-select" id="id_modulo" name="id_modulo" required>
                                        <option value="">Seleccionar módulo...</option>
                                        <?php foreach ($modulos as $modulo): ?>
                                            <option value="<?= $modulo['id_modulo'] ?>" <?= isset($_POST['id_modulo']) && $_POST['id_modulo'] == $modulo['id_modulo'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($modulo['nombre_modulo']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <hr class="text-white">
                                    <h6 class="text-white mb-3">Permisos del Módulo</h6>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="puede_ver" name="puede_ver" value="1" checked>
                                        <label class="form-check-label text-white" for="puede_ver">
                                            Puede Ver
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="puede_crear" name="puede_crear" value="1" checked>
                                        <label class="form-check-label text-white" for="puede_crear">
                                            Puede Crear
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="puede_editar" name="puede_editar" value="1" checked>
                                        <label class="form-check-label text-white" for="puede_editar">
                                            Puede Editar
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="puede_eliminar" name="puede_eliminar" value="1">
                                        <label class="form-check-label text-white" for="puede_eliminar">
                                            Puede Eliminar
                                        </label>
                                    </div>
                                </div>

                                <div class="alert alert-success">
                                    <small>
                                        <i class="fas fa-info-circle me-2"></i>
                                        Los campos marcados con * son obligatorios.
                                    </small>
                                </div>

                                <div class="col-12">
                                    <hr class="text-white">
                                    <div class="d-flex justify-content-between">
                                        <a href="index.php?page=permisos" class="btn btn-danger">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-neon">
                                            <i class="fas fa-save me-2"></i>Crear Permiso
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
        // Validación para evitar permisos duplicados
        const form = document.getElementById('formCrearPermiso');
        form.addEventListener('submit', function(e) {
            const idRol = document.getElementById('id_rol').value;
            const idModulo = document.getElementById('id_modulo').value;
            
            if (idRol && idModulo) {
                // Aquí podrías agregar validación AJAX para verificar si ya existe el permiso
            }
        });
    });
    </script>
<main>