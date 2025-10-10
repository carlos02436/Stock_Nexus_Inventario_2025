<?php
// app/views/auth/forgot_password.php

// Headers para prevenir cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si ya está logueado para redirigir
if (isset($_SESSION['usuario_logged_in']) && $_SESSION['usuario_logged_in'] === true) {
    header('Location: index.php?page=dashboard');
    exit;
}

// Cargar PHPMailer via Composer
require_once __DIR__ . '/../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    try {
        // Verificar si el email existe en la base de datos
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE correo = :correo AND estado = 'Activo' LIMIT 1");
        $stmt->bindParam(":correo", $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generar token seguro
            $token = bin2hex(random_bytes(32));
            $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Guardar token en la base de datos
            $stmt = $db->prepare("UPDATE usuarios SET token_recuperacion = :token, token_expiracion = :expiracion WHERE id_usuario = :id");
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expiracion', $expiracion);
            $stmt->bindParam(':id', $user['id_usuario']);
            $stmt->execute();

            // Crear enlace de recuperación
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $enlace = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/index.php?page=cambiar_password&token=" . $token;

            $asunto = "Recuperación de Contraseña - Stock Nexus";
            
            // Mensaje HTML
            $mensajeHTML = "
                <!DOCTYPE html>
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: #007bff; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                        .content { padding: 20px; background: #f9f9f9; border: 1px solid #ddd; }
                        .button { background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; }
                        .footer { text-align: center; padding: 20px; color: #666; font-size: 14px; }
                        .info-box { background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; margin: 15px 0; }
                        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 5px; margin: 15px 0; color: #856404; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>Stock Nexus</h1>
                            <h2>Recuperación de Contraseña</h2>
                        </div>
                        <div class='content'>
                            <p>Hola <strong>{$user['nombre_completo']}</strong>,</p>
                            <p>Has solicitado cambiar tu contraseña en Stock Nexus.</p>
                            
                            <div class='info-box'>
                                <p><strong>📧 Información de cuenta:</strong></p>
                                <p><strong>Usuario:</strong> $email</p>
                                <p><strong>Nombre:</strong> {$user['nombre_completo']}</p>
                            </div>
                            
                            <p>Para cambiar tu contraseña, haz clic en el siguiente botón:</p>
                            <p style='text-align: center; margin: 25px 0;'>
                                <a href='$enlace' class='button'>Cambiar Mi Contraseña</a>
                            </p>
                            
                            <p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
                            <p style='word-break: break-all; background: #eee; padding: 10px; border-radius: 5px; font-size: 12px;'>$enlace</p>
                            
                            <div class='warning'>
                                <p><strong>⚠️ Importante:</strong> Este enlace expirará en 1 hora por seguridad.</p>
                            </div>
                            
                            <p><strong>🔒 Seguridad:</strong> Si no solicitaste este cambio, ignora este mensaje y tu contraseña permanecerá sin cambios.</p>
                        </div>
                        <div class='footer'>
                            <p>Saludos,<br><strong>Equipo Stock Nexus</strong></p>
                            <p><small>Este es un mensaje automático, por favor no respondas a este email.</small></p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            // Mensaje texto plano
            $mensajeTexto = "Hola {$user['nombre_completo']},\n\n";
            $mensajeTexto .= "Has solicitado cambiar tu contraseña en Stock Nexus.\n\n";
            $mensajeTexto .= "Para cambiar tu contraseña, visita el siguiente enlace:\n";
            $mensajeTexto .= "$enlace\n\n";
            $mensajeTexto .= "Este enlace expirará en 1 hora por seguridad.\n\n";
            $mensajeTexto .= "Si no solicitaste este cambio, ignora este mensaje.\n\n";
            $mensajeTexto .= "Saludos,\nEquipo Stock Nexus";

            try {
                $mail = new PHPMailer(true);
                
                // Configuración SMTP
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'tu_email@gmail.com';  // CAMBIAR POR TU EMAIL
                $mail->Password   = 'tu_app_password';     // CAMBIAR POR TU APP PASSWORD
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';

                $mail->setFrom('tu_email@gmail.com', 'Stock Nexus'); // CAMBIAR POR TU EMAIL
                $mail->addAddress($email, $user['nombre_completo']);

                // Contenido del email
                $mail->isHTML(true);
                $mail->Subject = $asunto;
                $mail->Body    = $mensajeHTML;
                $mail->AltBody = $mensajeTexto;

                if ($mail->send()) {
                    $success = 'Se ha enviado un email con instrucciones para cambiar tu contraseña.';
                } else {
                    $error = 'Error al enviar el email. Por favor, intenta nuevamente.';
                }
            } catch (Exception $e) {
                error_log("Error PHPMailer: " . $e->getMessage());
                $error = 'Error al enviar el email. Por favor, intenta nuevamente.';
            }
        } else {
            // Por seguridad, mostrar mismo mensaje aunque el email no exista
            $success = 'Si el email existe en nuestro sistema, recibirás un enlace para cambiar tu contraseña.';
        }
    } catch (PDOException $e) {
        error_log("Error PDO: " . $e->getMessage());
        $error = 'Error del sistema. Por favor, intenta nuevamente.';
    } catch (Exception $e) {
        error_log("Error general: " . $e->getMessage());
        $error = 'Error del sistema. Por favor, intenta nuevamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña | Stock Nexus</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="icon" href="public/img/StockNexus.png">
    <link rel="stylesheet" href="public/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-lg" style="width: 25rem; border-radius: 1rem;
         background: rgba(255, 255, 255, 0.1); box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.18);">
        <div class="d-flex align-items-center justify-content-center mb-4">
            <img src="public/img/StockNexus.png" alt="Stock Nexus Logo" style="width: 60px; height: 60px; margin-right: 15px;">
            <div class="text-start">
                <h3 class="text-white mb-1">Stock Nexus</h3>
                <p class="text-white mb-0">Recuperar Contraseña</p>
            </div>
        </div>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- Email -->
            <div class="mb-4">
                <label class="form-label text-white">Email</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" name="email" class="form-control" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                           placeholder="Ingresa tu email" required>
                </div>
                <div class="form-text text-white">
                    Te enviaremos un enlace para cambiar tu contraseña.
                </div>
            </div>

            <!-- Botón -->
            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                <i class="bi bi-send me-2"></i>Enviar Instrucciones
            </button>

            <!-- Volver al Login -->
            <div class="text-center">
                <a href="index.php?page=login" class="text-decoration-none text-white">
                    <i class="bi bi-arrow-left me-1"></i>Volver al Login
                </a>
            </div>
        </form>

        <div class="text-center mt-4">
            <small class="text-white">
                &copy; 2025 Stock Nexus | Todos los derechos reservados
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<script>
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