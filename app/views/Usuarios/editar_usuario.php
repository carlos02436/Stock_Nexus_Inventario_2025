<?php
// app/views/usuarios/editar_usuario.php

// Verificar permisos de administrador
if ($_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php?page=dashboard");
    exit;
}

// Incluir el controlador
require_once __DIR__ . '/../../controllers/UsuarioController.php';

// Crear instancia del controlador
$usuarioController = new UsuarioController($db);

// Verificar si se recibió un ID de usuario
$usuario_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($usuario_id === 0) {
    echo "<div class='alert alert-danger'>Error: ID de usuario no válido</div>";
    return;
}

// Procesar el formulario si se envió
$mensaje_exito = '';
$mensaje_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $id = intval($_POST['id']);
    $nombre_completo = trim($_POST['nombre_completo']);
    $usuario = trim($_POST['usuario']);
    $correo = trim($_POST['correo']);
    $rol = $_POST['rol'];
    $estado = $_POST['estado'];
    $contrasena = $_POST['contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];

    // Validaciones básicas
    if (empty($nombre_completo) || empty($usuario) || empty($correo) || empty($rol) || empty($estado)) {
        $mensaje_error = "Todos los campos obligatorios deben ser completados";
    }
    // Validar contraseñas si se proporcionaron
    else if (!empty($contrasena) || !empty($confirmar_contrasena)) {
        if ($contrasena !== $confirmar_contrasena) {
            $mensaje_error = "Las contraseñas no coinciden";
        } else if (strlen($contrasena) < 6) {
            $mensaje_error = "La contraseña debe tener al menos 6 caracteres";
        }
    }

    // Si no hay errores, proceder con la actualización
    if (empty($mensaje_error)) {
        // Preparar datos para actualización
        $datos = [
            'nombre_completo' => $nombre_completo,
            'usuario' => $usuario,
            'correo' => $correo,
            'rol' => $rol,
            'estado' => $estado
        ];

        // Si se proporcionó contraseña, incluirla en la actualización
        if (!empty($contrasena)) {
            // Necesitamos agregar un método al controlador para actualizar con contraseña
            $query = "UPDATE usuarios 
                     SET nombre_completo = :nombre, correo = :correo, usuario = :usuario, 
                         rol = :rol, estado = :estado, contrasena = :contrasena 
                     WHERE id_usuario = :id";
            
            try {
                $stmt = $db->prepare($query);
                $resultado = $stmt->execute([
                    ':nombre' => $nombre_completo,
                    ':correo' => $correo,
                    ':usuario' => $usuario,
                    ':rol' => $rol,
                    ':estado' => $estado,
                    ':contrasena' => $contrasena,
                    ':id' => $id
                ]);
                
                if ($resultado) {
                    $mensaje_exito = "Usuario actualizado correctamente, incluyendo nueva contraseña";
                } else {
                    $mensaje_error = "Error al actualizar el usuario";
                }
            } catch (PDOException $e) {
                $mensaje_error = "Error en la base de datos: " . $e->getMessage();
            }
        } else {
            // Actualizar sin cambiar contraseña
            if ($usuarioController->actualizar($id, $datos)) {
                $mensaje_exito = "Usuario actualizado correctamente";
            } else {
                $mensaje_error = "Error al actualizar el usuario";
            }
        }
    }
}

// Obtener los datos del usuario directamente de la base de datos
try {
    $query = "SELECT id_usuario, nombre_completo, usuario, correo, rol, estado 
              FROM usuarios WHERE id_usuario = :id";
    $stmt = $db->prepare($query);
    $stmt->execute([':id' => $usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        echo "<div class='alert alert-danger'>Error: Usuario no encontrado</div>";
        return;
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error al obtener datos del usuario: " . $e->getMessage() . "</div>";
    return;
}
?>
<div class="container-fluid px-4 pb-5" style="margin-top:180px;">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-edit me-2"></i>Editar Usuario</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php?page=usuarios" class="boton3 text-decoration-none">
                <div class="boton-top3"><i class="fas fa-arrow-left me-2"></i>Volver a Usuarios</div>
                <div class="boton-bottom3"></div>
                <div class="boton-base3"></div>
            </a>
        </div>
    </div>

    <!-- Mostrar mensajes -->
    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo $mensaje_exito; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo $mensaje_error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card py-3">
                <div class="card-body">
                    <form id="formEditarUsuario" method="POST">
                        <input type="hidden" name="id" value="<?php echo $usuario['id_usuario']; ?>">
                        <input type="hidden" name="actualizar" value="1">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="nombre_completo" class="form-label">Nombre Completo *</label>
                                    <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" 
                                           value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="usuario" class="form-label">Nombre de Usuario *</label>
                                    <input type="text" class="form-control" id="usuario" name="usuario" 
                                           value="<?php echo htmlspecialchars($usuario['usuario']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 text-white">
                            <label for="correo" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="correo" name="correo" 
                                   value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="rol" class="form-label">Rol *</label>
                                    <select class="form-select" id="rol" name="rol" required>
                                        <option value="">Seleccionar Rol</option>
                                        <option value="Administrador" <?php echo $usuario['rol'] === 'Administrador' ? 'selected' : ''; ?>>Administrador</option>
                                        <option value="Vendedor" <?php echo $usuario['rol'] === 'Vendedor' ? 'selected' : ''; ?>>Vendedor</option>
                                        <option value="Contador" <?php echo $usuario['rol'] === 'Contador' ? 'selected' : ''; ?>>Contador</option>
                                        <option value="Bodeguero" <?php echo $usuario['rol'] === 'Bodeguero' ? 'selected' : ''; ?>>Bodeguero</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="estado" class="form-label">Estado *</label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="Activo" <?php echo $usuario['estado'] === 'Activo' ? 'selected' : ''; ?>>Activo</option>
                                        <option value="Inactivo" <?php echo $usuario['estado'] === 'Inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Campos de contraseña -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="contrasena" class="form-label">Nueva Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="contrasena" name="contrasena" 
                                               placeholder="Dejar en blanco para no cambiar" value="">
                                        <button type="button" class="btn btn-neon toggle-password" data-target="contrasena">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text text-white">Mínimo 6 caracteres</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 text-white">
                                    <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" 
                                               placeholder="Repetir nueva contraseña" value="">
                                        <button type="button" class="btn btn-neon toggle-password" data-target="confirmar_contrasena">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success my-4">
                            <small>
                                <i class="fas fa-info-circle me-2"></i>
                                Los campos marcados con * son obligatorios. La contraseña solo es necesaria si desea cambiarla.
                            </small>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                            <button type="submit" class="boton1 me-2 text-decoration-none">
                                <div class="boton-top1"><i class="fas fa-save me-2"></i> Actualizar Usuario</div>
                                <div class="boton-bottom1"></div>
                                <div class="boton-base1"></div>
                            </button>
                            <a href="index.php?page=usuarios" class="boton2 text-decoration-none">
                                <div class="boton-top2"><i class="fas fa-times me-2"></i> Cancelar</div>
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
    
    // Validación del formulario
    const form = document.getElementById('formEditarUsuario');
    const contrasena = document.getElementById('contrasena');
    const confirmarContrasena = document.getElementById('confirmar_contrasena');
    
    // Función para validar que las contraseñas coincidan
    function validarContrasenas() {
        if (contrasena.value !== '' && confirmarContrasena.value !== '') {
            if (contrasena.value !== confirmarContrasena.value) {
                confirmarContrasena.setCustomValidity('Las contraseñas no coinciden');
                confirmarContrasena.classList.add('is-invalid');
                return false;
            } else {
                confirmarContrasena.setCustomValidity('');
                confirmarContrasena.classList.remove('is-invalid');
                return true;
            }
        }
        return true;
    }
    
    // Función para validar longitud de contraseña
    function validarLongitudContrasena() {
        if (contrasena.value !== '' && contrasena.value.length < 6) {
            contrasena.setCustomValidity('La contraseña debe tener al menos 6 caracteres');
            contrasena.classList.add('is-invalid');
            return false;
        } else {
            contrasena.setCustomValidity('');
            contrasena.classList.remove('is-invalid');
            return true;
        }
    }
    
    // Event listeners para validación en tiempo real
    contrasena.addEventListener('input', function() {
        validarLongitudContrasena();
        validarContrasenas();
    });
    
    confirmarContrasena.addEventListener('input', function() {
        validarContrasenas();
    });
    
    // Validación al enviar el formulario
    form.addEventListener('submit', function(e) {
        const longitudValida = validarLongitudContrasena();
        const contrasenasCoinciden = validarContrasenas();
        
        if (!longitudValida || !contrasenasCoinciden) {
            e.preventDefault();
            if (!longitudValida) {
                alert('La contraseña debe tener al menos 6 caracteres');
            } else {
                alert('Las contraseñas no coinciden');
            }
            return false;
        }
        
        // Si ambos campos de contraseña están vacíos, no hay problema
        // Si uno está lleno y el otro vacío, mostrar error
        if ((contrasena.value === '' && confirmarContrasena.value !== '') || 
            (contrasena.value !== '' && confirmarContrasena.value === '')) {
            e.preventDefault();
            alert('Debe completar ambos campos de contraseña o dejarlos vacíos');
            return false;
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