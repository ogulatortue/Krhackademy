<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerService {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configure();
    }

    private function configure() {
        $this->mail->isSMTP();
        $this->mail->Host       = $_ENV['MAIL_HOST'];
        $this->mail->Port       = $_ENV['MAIL_PORT'];

        if ($_ENV['MAIL_HOST'] === '127.0.0.1') {
            $this->mail->SMTPAuth   = false;
            $this->mail->SMTPSecure = false; 
        } else {
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = $_ENV['MAIL_USERNAME'];
            $this->mail->Password   = $_ENV['MAIL_PASSWORD'];
            $this->mail->SMTPSecure = 'ssl';
        }

        $this->mail->CharSet    = 'UTF-8';
        $this->mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
    }

    public function sendPasswordResetEmail(string $recipientEmail, string $token): bool {
        try {
            $this->mail->addAddress($recipientEmail);

            $this->mail->isHTML(true);
            $this->mail->Subject = 'Réinitialisation de votre mot de passe - Kr[HACK]ademy';
            
            $resetLink = 'https://academy.krhacken.org/reset-password?token=' . $token;

            $this->mail->Body = "
                <h1>Demande de réinitialisation de mot de passe</h1>
                <p>Bonjour,</p>
                <p>Pour réinitialiser votre mot de passe pour Kr[HACK]ademy, veuillez cliquer sur le lien ci-dessous :</p>
                <p><a href='{$resetLink}'>Réinitialiser mon mot de passe</a></p>
                <p>Ce lien expirera dans 1 heure.</p>
                <p>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet e-mail.</p>
            ";
            
            $this->mail->AltBody = "Bonjour,\n\nPour réinitialiser votre mot de passe, copiez-collez ce lien dans votre navigateur : {$resetLink}\nCe lien expirera dans 1 heure.";

            $this->mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }
}