<?php
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}

$rolController = new RolController($db);

// Validar ID del rol
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?page=roles");
    exit;
}

$id_rol = (int) $_GET['id'];
$rol = $rolController->obtener($id_rol);

if (!$rol) {
    header("Location: index.php?page=roles");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_rol = trim($_POST['nombre_rol']);
    $estado = isset($_POST['estado']) ? (int) $_POST['estado'] : 1;

    $resultado = $rolController->editar($id_rol, $nombre_rol, $estado);

    if (isset($resultado['exito'])) {
        header("Location: index.php?page=roles");
        exit;
    } else {
        $error = $resultado['error'];
    }
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2 text-white"><i class="fas fa-edit me-2"></i>Editar Rol</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=roles" class="boton3 text-decoration-none">
                <div class="boton-top3"><i class="fas fa-arrow-left me-2"></i>Volver a Roles</div>
                <div class="boton-bottom3"></div>
                <div class="boton-base3"></div>
            </a>
        </div>
    </div>

    <!-- 🟢 Tarjeta centrada y angosta -->
    <div class="d-flex justify-content-center">
        <div class="card shadow-lg w-100" 
             style="max-width: 550px; border: 2px solid #28a745; box-shadow: 0 0 12px rgba(40, 167, 69, 0.4); border-radius: 10px;">

            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="nombre_rol" class="form-label text-white">Nombre del Rol</label>
                        <input type="text" 
                               class="form-control border-success shadow-sm" 
                               id="nombre_rol" 
                               name="nombre_rol" 
                               value="<?= htmlspecialchars($rol['nombre_rol']) ?>" 
                               required>
                    </div>

                    <div class="mb-4">
                        <label for="estado" class="form-label text-white">Estado</label>
                        <select class="form-select border-success shadow-sm" id="estado" name="estado" required>
                            <option value="1" <?= $rol['estado'] == 1 ? 'selected' : '' ?>>Activo</option>
                            <option value="0" <?= $rol['estado'] == 0 ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-center">
                        <a href="index.php?page=roles" class="boton2 me-2 text-decoration-none">
                            <div class="boton-top2"><i class="fas fa-times me-1"></i>Cancelar</div>
                            <div class="boton-bottom2"></div>
                            <div class="boton-base2"></div>
                        </a>
                        <button type="submit" class="boton1 text-decoration-none">
                            <div class="boton-top1"><i class="fas fa-save me-2"></i>Guardar Cambios</div>
                            <div class="boton-bottom1"></div>
                            <div class="boton-base1"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>