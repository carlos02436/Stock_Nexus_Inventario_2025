<?php
// No volvemos a requerir database.php porque ya está en index.php

// Requerir PHPMailer (asegúrate de que la carpeta src/ esté en la raíz del proyecto)
// Requerir PHPMailer
require_once __DIR__ . '/../../../src/PHPMailer.php';
require_once __DIR__ . '/../../../src/SMTP.php';
require_once __DIR__ . '/../../../src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    $stmt = $db->prepare("SELECT * FROM usuarios WHERE correo = :correo LIMIT 1");
    $stmt->bindParam(":correo", $email, PDO::PARAM_STR);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Generar token seguro
        $token = bin2hex(random_bytes(32));
        $asunto = "Recuperación de contraseña - Stock Nexus";
        
        $mensaje = "Hola " . $usuario['nombre_completo'] . ",\n\n";
        $mensaje .= "Has solicitado recuperar tu contraseña.\n\n";
        $mensaje .= "Para restablecer tu contraseña, haz clic en el siguiente enlace:\n";
        $mensaje .= "http://localhost/Stock_Nexus_Inventario_2025/index.php?page=reset_password&token=" . $token . "&correo=" . urlencode($email);
        $mensaje .= "\n\nEste enlace expirará en 1 hora.\n";
        $mensaje .= "Si no solicitaste este cambio, ignora este mensaje.";

        try {
            // Guardar el token en la base de datos usando los nombres correctos de columnas
            $stmt = $db->prepare("UPDATE usuarios SET token_recuperacion = :token, token_expiracion = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE correo = :correo");
            $stmt->bindParam(":token", $token);
            $stmt->bindParam(":correo", $email);
            $stmt->execute();

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'stocknexus2025@gmail.com';
            $mail->Password   = 'qkxu zmgh yawy udca';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $mail->setFrom('stocknexus2025@gmail.com', 'Stock Nexus');
            $mail->addAddress($email);

            $mail->isHTML(false);
            $mail->Subject = $asunto;
            $mail->Body    = $mensaje;

            if ($mail->send()) {
                $msg = "✅ Se ha enviado un enlace de recuperación a tu correo.";
            } else {
                $msg = "❌ Error al enviar el correo.";
            }
        } catch (Exception $e) {
            $msg = "❌ No se pudo enviar el correo. Error: " . $mail->ErrorInfo;
        }
    } else {
        $msg = "⚠️ Correo no registrado.";
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
        
        <!-- ✅ CORREGIDO: Mostrar $msg en lugar de $error y $success -->
        <?php if(!empty($msg)): ?>
            <div class="alert alert-<?= strpos($msg, '✅') !== false ? 'success' : (strpos($msg, '❌') !== false ? 'danger' : 'warning') ?> alert-dismissible fade show" role="alert">
                <i class="bi <?= strpos($msg, '✅') !== false ? 'bi-check-circle' : (strpos($msg, '❌') !== false ? 'bi-exclamation-triangle' : 'bi-exclamation-circle') ?> me-2"></i>
                <?= htmlspecialchars($msg) ?>
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

            <!-- Botón Enviar Instrucciones -->
            <button type="submit" class="btn-neon w-100 py-2 mb-3 rounded-3">
                <i class="bi bi-send me-2"></i>Enviar Instrucciones
            </button>

            <!-- Botón Volver al Login -->
            <button type="button" class="btn btn-secondary w-100 py-2 mb-3 rounded-3" onclick="window.location.href='index.php?page=login'">
                <i class="bi bi-arrow-left me-2"></i>Volver al Login
            </button>
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