<?php
// app/views/usuarios/crear_usuario.php
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-plus me-2"></i>Crear Nuevo Usuario</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=usuarios" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Usuarios
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card">
                <div class="card-header text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-plus me-2"></i>Información del Usuario
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?page=crear_usuario">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3 text-white">
                                    <label for="nombre_completo" class="form-label">Nombre Completo *</label>
                                    <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="usuario" class="form-label">Usuario *</label>
                                    <input type="text" class="form-control" id="usuario" name="usuario" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="correo" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="correo" name="correo" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="contrasena" class="form-label">Contraseña *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                                        <button type="button" class="btn btn-neon toggle-password" data-target="contrasena">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text text-white">Mínimo 6 caracteres</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" required>
                                        <button type="button" class="btn btn-neon toggle-password" data-target="confirmar_contrasena">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="rol" class="form-label">Rol *</label>
                                    <select class="form-select" id="rol" name="rol" required>
                                        <option value="">Seleccionar rol...</option>
                                        <option value="Administrador">Administrador</option>
                                        <option value="Vendedor" selected>Vendedor</option>
                                        <option value="Contador">Contador</option>
                                        <option value="Bodeguero">Bodeguero</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="estado" class="form-label">Estado *</label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="Activo" selected>Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success my-4">
                            <small>
                                <i class="fas fa-info-circle me-2"></i>
                                Los campos marcados con * son obligatorios.
                            </small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                            <button type="submit" class="btn btn-neon">
                                <i class="fas fa-save me-2"></i>Guardar Usuario
                            </button>
                            <a href="index.php?page=usuarios" class="btn btn-danger me-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para mostrar/ocultar contraseña
    function togglePasswordVisibility(targetId, button) {
        const passwordInput = document.getElementById(targetId);
        const icon = button.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            button.setAttribute('title', 'Ocultar contraseña');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            button.setAttribute('title', 'Mostrar contraseña');
        }
    }
    
    // Agregar event listeners a los botones de mostrar/ocultar contraseña
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        // Agregar tooltip inicial
        button.setAttribute('title', 'Mostrar contraseña');
        
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            togglePasswordVisibility(targetId, this);
        });
    });
    
    // Validación de contraseñas
    const contrasena = document.getElementById('contrasena');
    const confirmarContrasena = document.getElementById('confirmar_contrasena');
    const form = document.querySelector('form');
    
    function validarContrasenas() {
        if (contrasena.value !== confirmarContrasena.value) {
            confirmarContrasena.setCustomValidity('Las contraseñas no coinciden');
            confirmarContrasena.classList.add('is-invalid');
        } else {
            confirmarContrasena.setCustomValidity('');
            confirmarContrasena.classList.remove('is-invalid');
        }
    }
    
    contrasena.addEventListener('input', validarContrasenas);
    confirmarContrasena.addEventListener('input', validarContrasenas);
    
    // Validación de longitud de contraseña
    contrasena.addEventListener('input', function() {
        if (this.value.length < 6 && this.value.length > 0) {
            this.setCustomValidity('La contraseña debe tener al menos 6 caracteres');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    });
    
    // Validar formulario al enviar
    form.addEventListener('submit', function(e) {
        // Forzar validación de contraseñas
        validarContrasenas();
        
        if (contrasena.value.length > 0 && contrasena.value.length < 6) {
            contrasena.setCustomValidity('La contraseña debe tener al menos 6 caracteres');
            contrasena.classList.add('is-invalid');
        }
        
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            form.classList.add('was-validated');
        }
    });
    
    // Limpiar validación cuando el usuario empiece a escribir
    const inputs = form.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
});
</script>