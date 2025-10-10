<?php
// ============================================================
// Mailer.php - Configuración y envío de correos con PHPMailer
// ============================================================

// Incluir PHPMailer manualmente (según tu estructura actual)
require_once __DIR__ . '/../../src/PHPMailer.php';
require_once __DIR__ . '/../../src/Exception.php';
require_once __DIR__ . '/../../src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    protected $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configure();
    }
    
    protected function configure() {
        // Configuración SMTP de Gmail
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'TU_CORREO@gmail.com';           // tu correo Gmail
        $this->mail->Password = 'TU_CONTRASEÑA_DE_APLICACIÓN';   // contraseña de aplicación (16 caracteres)
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;

        // Datos del remitente
        $this->mail->setFrom('TU_CORREO@gmail.com', 'Stock Nexus');
        $this->mail->isHTML(true);
        $this->mail->CharSet = 'UTF-8';

        // Configuración de depuración (solo para pruebas)
        // 0 = silencioso, 1 = errores, 2 = detalle conexión
        $this->mail->SMTPDebug = 0;
        $this->mail->Debugoutput = 'error_log'; // guarda los errores en php_error_log
    }
    
    public function send($to, $subject, $body) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->AltBody = strip_tags($body); // versión texto plano
            
            if (!$this->mail->send()) {
                error_log("❌ Error al enviar correo: " . $this->mail->ErrorInfo);
                return false;
            }

            return true;
        } catch (Exception $e) {
            error_log("❌ Excepción PHPMailer: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar correo de recuperación de contraseña
     */
    public function sendRecoveryEmail($to, $nombre, $id_usuario) {
        // Crear enlace directo para cambiar contraseña
        $enlace = "http://" . $_SERVER['HTTP_HOST'] . "/index.php?page=cambiar_password&id=" . $id_usuario;

        $subject = "Solicitud de Cambio de Contraseña - Stock Nexus";

        $body = "
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
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Stock Nexus</h1>
                        <h2>Cambio de Contraseña</h2>
                    </div>
                    <div class='content'>
                        <p>Hola <strong>$nombre</strong>,</p>
                        <p>Has solicitado cambiar tu contraseña en <strong>Stock Nexus</strong>.</p>

                        <div class='info-box'>
                            <p><strong>📧 Información de tu cuenta:</strong></p>
                            <p><strong>Usuario:</strong> $to</p>
                            <p><strong>Nombre:</strong> $nombre</p>
                        </div>

                        <p>Para cambiar tu contraseña, haz clic en el siguiente botón:</p>
                        <p style='text-align: center; margin: 25px 0;'>
                            <a href='$enlace' class='button' style='color: white; text-decoration: none;'>Cambiar Mi Contraseña</a>
                        </p>

                        <p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
                        <p style='word-break: break-all; background: #eee; padding: 10px; border-radius: 5px; font-size: 12px;'>$enlace</p>

                        <p><strong>💡 Nota:</strong> Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
                    </div>
                    <div class='footer'>
                        <p>Saludos,<br><strong>Equipo Stock Nexus</strong></p>
                        <p><small>Este es un mensaje automático, por favor no respondas a este email.</small></p>
                    </div>
                </div>
            </body>
            </html>
        ";

        return $this->send($to, $subject, $body);
    }
}