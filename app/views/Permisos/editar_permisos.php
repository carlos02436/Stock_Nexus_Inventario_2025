<?php
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?page=permisos");
    exit;
}

$permisoController = new PermisoController($db);
$rolController = new RolController($db);
$moduloController = new ModuloController($db);

// Obtener el permiso a editar
$id_permiso = $_GET['id'];
$permiso = $permisoController->obtenerPorId($id_permiso);

if (!$permiso) {
    header("Location: index.php?page=permisos");
    exit;
}

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
        'puede_eliminar' => isset($_POST['puede_eliminar']) ? 1 : 0,
        'estado' => isset($_POST['estado']) && $_POST['estado'] == 'activo' ? 'activo' : 'inactivo'
    ];

    $resultado = $permisoController->actualizar($id_permiso, $datos);
    
    if ($resultado) {
        $_SESSION['mensaje'] = 'Permiso actualizado exitosamente';
        $_SESSION['mensaje_tipo'] = 'success';
        header("Location: index.php?page=permisos");
        exit;
    } else {
        $error = "Error al actualizar el permiso";
    }
}
?>
<body>
    <div class="container-fluid px-4 mb-5" style="margin-top:180px;">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="fas fa-edit me-2"></i>Editar Permiso</h1>
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
                            <i class="fas fa-key me-2"></i>Editar Información del Permiso
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="formEditarPermiso">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="id_rol" class="form-label text-white">Rol <span class="text-danger">*</span></label>
                                    <select class="form-select" id="id_rol" name="id_rol" required>
                                        <option value="">Seleccionar rol...</option>
                                        <?php foreach ($roles as $rol): ?>
                                            <option value="<?= $rol['id_rol'] ?>" 
                                                <?= (isset($_POST['id_rol']) && $_POST['id_rol'] == $rol['id_rol']) || $permiso['id_rol'] == $rol['id_rol'] ? 'selected' : '' ?>>
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
                                            <option value="<?= $modulo['id_modulo'] ?>" 
                                                <?= (isset($_POST['id_modulo']) && $_POST['id_modulo'] == $modulo['id_modulo']) || $permiso['id_modulo'] == $modulo['id_modulo'] ? 'selected' : '' ?>>
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
                                        <input class="form-check-input" type="checkbox" id="puede_ver" name="puede_ver" value="1" 
                                            <?= $permiso['puede_ver'] ? 'checked' : '' ?>>
                                        <label class="form-check-label text-white" for="puede_ver">
                                            Puede Ver
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="puede_crear" name="puede_crear" value="1" 
                                            <?= $permiso['puede_crear'] ? 'checked' : '' ?>>
                                        <label class="form-check-label text-white" for="puede_crear">
                                            Puede Crear
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="puede_editar" name="puede_editar" value="1" 
                                            <?= $permiso['puede_editar'] ? 'checked' : '' ?>>
                                        <label class="form-check-label text-white" for="puede_editar">
                                            Puede Editar
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="puede_eliminar" name="puede_eliminar" value="1" 
                                            <?= $permiso['puede_eliminar'] ? 'checked' : '' ?>>
                                        <label class="form-check-label text-white" for="puede_eliminar">
                                            Puede Eliminar
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="estado" name="estado" value="activo" 
                                            <?= $permiso['estado'] == 'activo' ? 'checked' : '' ?>>
                                        <label class="form-check-label text-white" for="estado">
                                            Estado Activo
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="text-white">
                                    <div class="d-flex justify-content-between">
                                        <a href="index.php?page=permisos" class="btn btn-danger">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-neon">
                                            <i class="fas fa-save me-2"></i>Actualizar Permiso
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
<main>