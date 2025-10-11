<?php
// Headers para prevenir cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Inicializar variables
$msg = "";
$email = $_GET['correo'] ?? ''; // Obtener email de la URL
$show_form = true; // ← INICIALIZAR LA VARIABLE

// reset_password.php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validar que el email existe
    if (empty($email)) {
        $msg = "❌ Error: No se ha especificado un email válido";
        $show_form = true;
    }
    // Validaciones y actualización de contraseña
    else if ($password === $confirm_password) {
        if (strlen($password) >= 6) {
            // ⚠️ GUARDAR EN TEXTO PLANO (sin password_hash)
            $stmt = $db->prepare("UPDATE usuarios SET contrasena = :contrasena WHERE correo = :correo");
            $stmt->bindParam(":contrasena", $password); // ← Guardar directamente la contraseña
            $stmt->bindParam(":correo", $email);
            
            if ($stmt->execute()) {
                $msg = "✅ Contraseña actualizada correctamente. Ahora puedes iniciar sesión.";
                $show_form = false; // ← OCULTAR FORMULARIO DESPUÉS DEL ÉXITO
                
                // Limpiar tokens de recuperación si existen
                $stmt = $db->prepare("UPDATE usuarios SET token_recuperacion = NULL, token_expiracion = NULL WHERE correo = :correo");
                $stmt->bindParam(":correo", $email);
                $stmt->execute();
            } else {
                $msg = "❌ Error al actualizar la contraseña";
                $show_form = true;
            }
        } else {
            $msg = "❌ La contraseña debe tener al menos 6 caracteres";
            $show_form = true;
        }
    } else {
        $msg = "❌ Las contraseñas no coinciden";
        $show_form = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña | Stock Nexus</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="icon" href="public/img/StockNexus.png">
    <link rel="stylesheet" href="public/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .password-toggle {
            cursor: pointer;
            background: transparent;
            border: 1px solid #ced4da;
            border-left: none;
            color: #6c757d;
            transition: all 0.3s ease;
        }
        .password-toggle:hover {
            background: #e9ecef;
            color: #495057;
        }
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }
        .form-control:focus + .input-group-text + .password-toggle {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-lg" style="width: 25rem; border-radius: 1rem;
        background: rgba(255, 255, 255, 0.1); box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.18);">
        <div class="d-flex align-items-center justify-content-center mb-4">
            <img src="public/img/StockNexus.png" alt="Stock Nexus Logo" style="width: 60px; height: 60px; margin-right: 15px;">
            <div class="text-start">
                <h3 class="text-white mb-1">Stock Nexus</h3>
                <p class="text-white mb-0">Nueva Contraseña</p>
            </div>
        </div>

        <!-- Mostrar mensajes -->
        <?php if(!empty($msg)): ?>
            <div class="alert alert-<?= strpos($msg, '✅') !== false ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                <i class="bi <?= strpos($msg, '✅') !== false ? 'bi-check-circle' : 'bi-exclamation-triangle' ?> me-2"></i>
                <?= htmlspecialchars($msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if($show_form): ?>
        <form method="POST" action="">
            <!-- Campo oculto para el email -->
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            
            <!-- Mostrar email actual (solo informativo) -->
            <?php if(!empty($email)): ?>
            <div class="mb-3">
                <label class="form-label text-white">Restableciendo contraseña para:</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($email) ?>" readonly disabled>
                </div>
            </div>
            <?php endif; ?>

            <!-- Nueva Contraseña con ojo -->
            <div class="mb-3">
                <label for="password" class="form-label text-white">Nueva Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa nueva contraseña" required minlength="6">
                    <button type="button" class="btn password-toggle" onclick="togglePassword('password')">
                        <i class="bi bi-eye" id="eye-icon-password"></i>
                    </button>
                </div>
                <div class="form-text text-white">Mínimo 6 caracteres</div>
            </div>

            <!-- Confirmar Contraseña con ojo -->
            <div class="mb-4">
                <label for="confirm_password" class="form-label text-white">Confirmar Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock-fill"></i>
                    </span>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirma tu contraseña" required minlength="6">
                    <button type="button" class="btn password-toggle" onclick="togglePassword('confirm_password')">
                        <i class="bi bi-eye" id="eye-icon-confirm_password"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-neon w-100 py-2 mb-3 rounded-3">
                <i class="bi bi-check-circle me-2"></i>Guardar Contraseña
            </button>
        </form>
        <?php endif; ?>

        <!-- Botón Volver al Login -->
        <button type="button" class="btn btn-secondary w-100 py-2 mb-3 rounded-3" onclick="window.location.href='index.php?page=login'">
            <i class="bi bi-arrow-left me-2"></i>Volver al Login
        </button>
        
        <div class="text-center mt-4">
            <small class="text-white">
                &copy; 2025 Stock Nexus | Todos los derechos reservados
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para mostrar/ocultar contraseña
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = document.getElementById('eye-icon-' + fieldId);
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            }
        }

        // Validación de contraseñas coincidentes
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('❌ Las contraseñas no coinciden. Por favor, verifica.');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('❌ La contraseña debe tener al menos 6 caracteres.');
                return false;
            }
        });

        // Prevenir que el usuario regrese con el botón "atrás" del navegador
        if (performance.navigation.type === 2) {
            window.location.reload(true);
        }

        // Limpiar el cache al cargar la página
        window.onpageshow = function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        };
    </script>
</body>
</html>