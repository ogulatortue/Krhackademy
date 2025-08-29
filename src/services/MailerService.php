<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerService {
    public $mail; // Changé de private à public pour un accès temporaire

    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configure();
    }

    private function configure() {
        $this->mail->isSMTP();
        $this->mail->Host = $_ENV['MAIL_HOST'];
        $this->mail->Port = $_ENV['MAIL_PORT'];

        if ($_ENV['MAIL_HOST'] === '127.0.0.1') {
            $this->mail->SMTPAuth = false;
            $this->mail->SMTPSecure = false;
        } else {
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $_ENV['MAIL_USERNAME'];
            $this->mail->Password = $_ENV['MAIL_PASSWORD'];
            $this->mail->SMTPSecure = 'ssl';
        }

        $this->mail->CharSet = 'UTF-8';
        $this->mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
    }

    public function sendPasswordResetEmail(string $recipientEmail, string $username, string $token): bool {
        try {
            $this->mail->addAddress($recipientEmail);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Réinitialisation de votre mot de passe - Kr[HACK]ademy';
            $resetLink = 'https://academy.krhacken.org/reset-password?token=' . $token;
            $safeUsername = htmlspecialchars($username);
            $this->mail->Body = "<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'></head><body>Bonjour {$safeUsername},<br>Cliquez ici pour réinitialiser votre mot de passe : <a href='{$resetLink}'>Réinitialiser</a></body></html>";
            $this->mail->AltBody = "Bonjour {$username},\n\nLien de réinitialisation : {$resetLink}";
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    public function prepareWelcomeEmail(string $recipientEmail, string $username): void {
        $this->mail->addAddress($recipientEmail);
        $this->mail->isHTML(true);
        $this->mail->Subject = 'Bienvenue sur Kr[HACK]ademy !';
        $loginLink = 'https://academy.krhacken.org/login';
        $safeUsername = htmlspecialchars($username);
        $this->mail->Body = "<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'></head><body><h1>Bienvenue {$safeUsername} !</h1><p>Votre compte a été créé. <a href='{$loginLink}'>Connectez-vous</a>.</p></body></html>";
        $this->mail->AltBody = "Bienvenue {$username} ! Votre compte a été créé. Lien de connexion : {$loginLink}";
    }

    public function sendWelcomeEmail(string $recipientEmail, string $username): bool {
        try {
            $this->prepareWelcomeEmail($recipientEmail, $username);
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error on Welcome Email: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    public function sendEmail(string $recipient, string $subject, string $body, ?string $altBody = null): bool {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            $this->mail->addAddress($recipient);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->AltBody = $altBody ?? 'Veuillez utiliser un client e-mail compatible HTML pour voir ce message.';
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("MailerService Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    public function getSubject(): string {
        return $this->mail->Subject;
    }

    public function getBody(): string {
        return $this->mail->Body;
    }
}