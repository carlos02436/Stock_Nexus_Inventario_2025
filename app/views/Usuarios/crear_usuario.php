<?php
// app/views/usuarios/crear_usuario.php
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
     style="margin-top:120px;">
    <h1 class="h2"><i class="fas fa-plus me-2"></i>Crear Nuevo Usuario</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=usuarios" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?page=crear_usuario">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nombre_completo" class="form-label">Nombre Completo *</label>
                        <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario *</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="correo" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña *</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol *</label>
                        <select class="form-select" id="rol" name="rol" required>
                            <option value="Administrador">Administrador</option>
                            <option value="Vendedor" selected>Vendedor</option>
                            <option value="Contador">Contador</option>
                            <option value="Bodeguero">Bodeguero</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado *</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="Activo" selected>Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Guardar Usuario
                </button>
                <a href="index.php?page=usuarios" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>