<?php
// app/views/auth/login.php

// NO iniciar sesión aquí - ya se inicia en index.php
// Solo verificar si ya está logueado para redirigir
if (isset($_SESSION['usuario_logged_in']) && $_SESSION['usuario_logged_in'] === true) {
    header('Location: index.php?page=dashboard');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['usuario'] ?? '';
    $password = $_POST['contrasena'] ?? '';

    try {
        // Incluir la configuración de la base de datos con ruta ABSOLUTA
        require_once __DIR__ . '/../../../../config/database.php';

        // Verificar credenciales directamente en la base de datos
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE usuario = :usuario AND estado = 'Activo' LIMIT 1");
        $stmt->bindParam(':usuario', $username);
        $stmt->execute();
        $user = $stmt->fetch();

        // En producción, usar password_hash y password_verify
        // Por ahora, comparación directa ya que en tu BD las contraseñas están en texto plano
        if ($user && $password === $user['contrasena']) {
            $_SESSION['usuario_logged_in'] = true;
            $_SESSION['usuario_id'] = $user['id_usuario'];
            $_SESSION['usuario_nombre'] = $user['nombre_completo'];
            $_SESSION['usuario_rol'] = $user['rol'];
            $_SESSION['usuario_correo'] = $user['correo'];

            header('Location: index.php?page=dashboard');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    } catch (PDOException $e) {
        $error = 'Error al conectar con la base de datos';
    } catch (Exception $e) {
        $error = 'Error del sistema';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Stock Nexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-lg" style="width: 25rem; border-radius: 1rem;">
        <div class="text-center mb-4">
            <i class="bi bi-warehouse text-primary" style="font-size: 3rem;"></i>
            <h3 class="mt-3 text-primary">Stock Nexus</h3>
            <p class="text-muted">Sistema de Inventario</p>
        </div>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>        

        <form method="POST" action="">
            <!-- Usuario -->
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="usuario" class="form-control" 
                           value="<?= $_POST['usuario'] ?? '' ?>" 
                           placeholder="Ingresa tu usuario" required>
                </div>
            </div>

            <!-- Contraseña con ojo -->
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" id="contrasena" name="contrasena" 
                           class="form-control" placeholder="Ingresa tu contraseña" required>
                    <button type="button" class="input-group-text bg-white" id="togglePassword" 
                            style="cursor:pointer; border-left: none;">
                        <i class="bi bi-eye-slash" id="icono-ojo"></i>
                    </button>
                </div>
            </div>

            <!-- Recuérdame y Olvidaste contraseña -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Recuérdame</label>
                </div>
                <a href="index.php?page=forgot_password" class="text-decoration-none small">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

            <!-- Botón -->
            <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar al Sistema
            </button>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted">
                &copy; 2024 Stock Nexus. Todos los derechos reservados.
            </small>
        </div>
    </div>

    <!-- Script para el ojo -->
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('contrasena');
        const iconoOjo = document.getElementById('icono-ojo');

        togglePassword.addEventListener('click', () => {
            const tipo = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = tipo;

            // Cambiar ícono
            iconoOjo.classList.toggle('bi-eye');
            iconoOjo.classList.toggle('bi-eye-slash');
        });

        // Prevenir envío del formulario con Enter en el botón de ojo
        togglePassword.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>