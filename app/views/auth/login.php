<?php
// app/views/auth/login.php

// Headers para prevenir cache en la página de login
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

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
        // CORREGIR: Usar la ruta correcta para database.php
        require_once __DIR__ . '/../../../config/database.php';

        // Verificar credenciales directamente en la base de datos
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE usuario = :usuario AND estado = 'Activo' LIMIT 1");
        $stmt->bindParam(':usuario', $username);
        $stmt->execute();
        $user = $stmt->fetch();

        // En producción, usar password_hash y password_verify
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
        $error = 'Error al conectar con la base de datos: ' . $e->getMessage();
    } catch (Exception $e) {
        $error = 'Error del sistema: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Stock Nexus</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="icon" href="public/img/StockNexus.png">
    <link rel="stylesheet" href="public/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <!-- ... resto del código HTML igual ... -->
    <div class="card p-4 shadow-lg" style="width: 25rem; border-radius: 1rem;
         background: rgba(255, 255, 255, 0.1); box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.18);">
        <div class="d-flex align-items-center justify-content-center mb-4">
            <img src="public/img/StockNexus.png" alt="Stock Nexus Logo" style="width: 60px; height: 60px; margin-right: 15px;">
            <div class="text-start">
                <h3 class="text-white mb-1">Stock Nexus</h3>
                <p class="text-white mb-0">Sistema de Inventario</p>
            </div>
        </div>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>        

        <form method="POST" action="">
            <!-- Usuario -->
            <div class="mb-3">
                <label class="form-label text-white">Usuario</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="usuario" class="form-control" 
                           value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>" 
                           placeholder="Ingresa tu usuario" required>
                </div>
            </div>

            <!-- Contraseña con ojo -->
            <div class="mb-3">
                <label class="form-label text-white">Contraseña</label>
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
                <div class="form-check text-white">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Recuérdame</label>
                </div>
                <a href="index.php?page=forgot_password" class="text-decoration-none small text-white">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

            <!-- Botón -->
            <button type="submit" class="btn-neon w-100 py-2 rounded-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar al Sistema
            </button>
        </form>

        <div class="text-center mt-4">
            <small class="text-white">
                &copy; 2025 Stock Nexus | Todos los derechos reservados
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

<script>
// Prevenir que el usuario regrese con el botón "atrás" del navegador
if (performance.navigation.type === 2) {
    // Navigation type 2 significa que llegó aquí desde cache (botón atrás)
    window.location.reload(true); // Forzar recarga desde el servidor
}

// Limpiar el cache al cargar la página
window.onpageshow = function(event) {
    if (event.persisted) {
        window.location.reload();
    }
};
</script>