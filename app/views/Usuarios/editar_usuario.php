<?php
// app/views/usuarios/editar_usuario.php

// Verificar si se recibió un ID de usuario
$usuario_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($usuario_id === 0) {
    echo "<div class='alert alert-danger'>Error: ID de usuario no válido</div>";
    return;
}

// Aquí iría la lógica para obtener los datos del usuario de la base de datos
// Por ahora, simulamos datos vacíos
$usuario = [
    'id' => $usuario_id,
    'nombre' => '',
    'usuario' => '',
    'email' => '',
    'rol' => '',
    'estado' => 'Activo'
];
?>
<div class="container mt-4" style="margin-top:120px;">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Editar Usuario
                    </h4>
                </div>
                <div class="card-body">
                    <form id="formEditarUsuario" action="?action=actualizar_usuario" method="POST">
                        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Nombre Completo:</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="usuario">Nombre de Usuario:</label>
                                    <input type="text" class="form-control" id="usuario" name="usuario" 
                                           value="<?php echo htmlspecialchars($usuario['usuario']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rol">Rol:</label>
                                    <select class="form-control" id="rol" name="rol" required>
                                        <option value="">Seleccionar Rol</option>
                                        <option value="Administrador" <?php echo $usuario['rol'] === 'Administrador' ? 'selected' : ''; ?>>Administrador</option>
                                        <option value="Vendedor" <?php echo $usuario['rol'] === 'Vendedor' ? 'selected' : ''; ?>>Vendedor</option>
                                        <option value="Contador" <?php echo $usuario['rol'] === 'Contador' ? 'selected' : ''; ?>>Contador</option>
                                        <option value="Bodeguero" <?php echo $usuario['rol'] === 'Bodeguero' ? 'selected' : ''; ?>>Bodeguero</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estado">Estado:</label>
                                    <select class="form-control" id="estado" name="estado" required>
                                        <option value="Activo" <?php echo $usuario['estado'] === 'Activo' ? 'selected' : ''; ?>>Activo</option>
                                        <option value="Inactivo" <?php echo $usuario['estado'] === 'Inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Nueva Contraseña (dejar en blanco para no cambiar):</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="form-text text-muted">Mínimo 6 caracteres</small>
                        </div>
                        
                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-success mr-2">
                                <i class="fas fa-save"></i> Actualizar Usuario
                            </button>
                            <a href="?action=usuarios" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('formEditarUsuario').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    
    if (password && password.length < 6) {
        e.preventDefault();
        alert('La contraseña debe tener al menos 6 caracteres');
        return false;
    }
});
</script>