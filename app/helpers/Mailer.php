<?php
namespace App\Helpers;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    protected $mail;
    public function __construct() {
        $this->mail = new PHPMailer(true);
        // Configure SMTP here or use .env
    }
    public function send($to, $subject, $body) {
        try {
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.example.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = 'user@example.com';
            $this->mail->Password = 'secret';
            $this->mail->SMTPSecure = 'tls';
            $this->mail->Port = 587;
            $this->mail->setFrom('noreply@example.com', 'Stock Nexus');
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
