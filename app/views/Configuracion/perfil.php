<?php
// app/views/configuracion/perfil.php
$usuarioController = new UsuarioController($db);
$usuario = $usuarioController->obtener($_SESSION['usuario_id']);
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"
     style="margin-top:120px;">
    <h1 class="h2"><i class="fas fa-user-edit me-2"></i>Mi Perfil</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información Personal</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?page=actualizar_perfil">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre_completo" class="form-label">Nombre Completo *</label>
                                <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" 
                                       value="<?= $usuario['nombre_completo'] ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="correo" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="correo" name="correo" 
                                       value="<?= $usuario['correo'] ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario *</label>
                                <input type="text" class="form-control" id="usuario" name="usuario" 
                                       value="<?= $usuario['usuario'] ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contrasena_actual" class="form-label">Contraseña Actual</label>
                                <input type="password" class="form-control" id="contrasena_actual" name="contrasena_actual" 
                                       placeholder="Dejar en blanco para mantener la actual">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nueva_contrasena" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="nueva_contrasena" name="nueva_contrasena">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Actualizar Perfil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información de la Cuenta</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-user-circle fa-5x text-primary mb-3"></i>
                    <h5><?= $usuario['nombre_completo'] ?></h5>
                    <p class="text-muted"><?= $usuario['rol'] ?></p>
                </div>
                
                <table class="table table-sm">
                    <tr>
                        <th>Usuario:</th>
                        <td><?= $usuario['usuario'] ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?= $usuario['correo'] ?></td>
                    </tr>
                    <tr>
                        <th>Rol:</th>
                        <td>
                            <span class="badge bg-<?= 
                                $usuario['rol'] == 'Administrador' ? 'danger' : 
                                ($usuario['rol'] == 'Vendedor' ? 'primary' : 'info')
                            ?>">
                                <?= $usuario['rol'] ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td>
                            <span class="badge bg-<?= $usuario['estado'] == 'Activo' ? 'success' : 'secondary' ?>">
                                <?= $usuario['estado'] ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Registrado:</th>
                        <td><?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>