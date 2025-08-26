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

    public function sendPasswordResetEmail(string $recipientEmail, string $username, string $token): bool {
        try {
            $this->mail->addAddress($recipientEmail);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Réinitialisation de votre mot de passe - Kr[HACK]ademy';
            $resetLink = 'https://academy.krhacken.org/reset-password?token=' . $token;
            $safeUsername = htmlspecialchars($username);
            $this->mail->Body = "<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Réinitialisation de mot de passe</title><style>body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; }</style></head><body style='margin: 0; padding: 0; background-color: #1a1a1e; color: #cccccc;'><table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color: #1a1a1e;'><tr><td align='center' style='padding: 40px 0;'><table width='100%' border='0' cellspacing='0' cellpadding='0' style='max-width: 600px; background-color: #2a2a2e; border: 1px solid #444; border-radius: 12px; overflow: hidden;'><tr><td align='center' style='padding: 30px 20px; background: url(\"https://academy.krhacken.org/images/header_background_blured3.webp\") center/cover; border-bottom: 1px solid #9216a8;'><h1 style='margin: 0; font-size: 28px; font-weight: bold; color: #ffffff; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);'>Kr[HACK]ademy</h1></td></tr><tr><td style='padding: 40px 30px;'><h2 style='margin: 0 0 20px 0; font-size: 24px; font-weight: bold; color: #ffffff;'>Réinitialisation de votre mot de passe</h2><p style='margin: 0 0 15px 0; font-size: 16px; line-height: 1.6;'>Bonjour {$safeUsername},</p><p style='margin: 0 0 30px 0; font-size: 16px; line-height: 1.6;'>Nous avons reçu une demande de réinitialisation du mot de passe pour votre compte. Cliquez sur le bouton ci-dessous pour continuer.</p><table border='0' cellspacing='0' cellpadding='0' width='100%'><tr><td align='center'><a href='{$resetLink}' target='_blank' style='display: inline-block; padding: 14px 28px; font-size: 16px; font-weight: bold; color: #ffffff; text-decoration: none; background-color: rgba(35, 38, 49, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);'>Réinitialiser mon mot de passe</a></td></tr></table><p style='margin: 30px 0 15px 0; font-size: 14px; line-height: 1.6;'>Ce lien de réinitialisation expirera dans 1 heure.</p><p style='margin: 0; font-size: 14px; line-height: 1.6;'>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet e-mail en toute sécurité.</p></td></tr><tr><td align='center' style='padding: 20px 30px; border-top: 1px solid #444;'><p style='margin: 0; font-size: 12px; color: #888888;'>&copy; " . date('Y') . " Kr[HACK]ademy. Tous droits réservés.</p></td></tr></table></td></tr></table></body></html>";
            $this->mail->AltBody = "Bonjour {$username},\n\nPour réinitialiser votre mot de passe pour Kr[HACK]ademy, veuillez copier et coller le lien suivant dans votre navigateur :\n{$resetLink}\n\nCe lien expirera dans 1 heure. Si vous n'êtes pas à l'origine de cette demande, ignorez cet e-mail.";
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    public function sendWelcomeEmail(string $recipientEmail, string $username): bool {
        try {
            $this->mail->addAddress($recipientEmail);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Bienvenue sur Kr[HACK]ademy !';
            $loginLink = 'https://academy.krhacken.org/login';
            $safeUsername = htmlspecialchars($username);
            $this->mail->Body = "<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Bienvenue sur Kr[HACK]ademy</title><style>body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; }</style></head><body style='margin: 0; padding: 0; background-color: #1a1a1e; color: #cccccc;'><table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color: #1a1a1e;'><tr><td align='center' style='padding: 40px 0;'><table width='100%' border='0' cellspacing='0' cellpadding='0' style='max-width: 600px; background-color: #2a2a2e; border: 1px solid #444; border-radius: 12px; overflow: hidden;'><tr><td align='center' style='padding: 30px 20px; background: url(\"https://academy.krhacken.org/images/header_background_blured3.webp\") center/cover; border-bottom: 1px solid #9216a8;'><h1 style='margin: 0; font-size: 28px; font-weight: bold; color: #ffffff; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);'>Kr[HACK]ademy</h1></td></tr><tr><td style='padding: 40px 30px;'><h2 style='margin: 0 0 20px 0; font-size: 24px; font-weight: bold; color: #ffffff;'>Bienvenue !</h2><p style='margin: 0 0 15px 0; font-size: 16px; line-height: 1.6;'>Bonjour {$safeUsername},</p><p style='margin: 0 0 30px 0; font-size: 16px; line-height: 1.6;'>Toute l'équipe de Kr[HACK]ademy est heureuse de vous accueillir. Votre compte a été créé avec succès. Vous pouvez désormais vous connecter et commencer votre parcours d'apprentissage.</p><table border='0' cellspacing='0' cellpadding='0' width='100%'><tr><td align='center'><a href='{$loginLink}' target='_blank' style='display: inline-block; padding: 14px 28px; font-size: 16px; font-weight: bold; color: #ffffff; text-decoration: none; background-color: rgba(35, 38, 49, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);'>Se connecter</a></td></tr></table><p style='margin: 30px 0 0 0; font-size: 14px; line-height: 1.6;'>À bientôt sur la plateforme !</p></td></tr><tr><td align='center' style='padding: 20px 30px; border-top: 1px solid #444;'><p style='margin: 0; font-size: 12px; color: #888888;'>&copy; " . date('Y') . " Kr[HACK]ademy. Tous droits réservés.</p></td></tr></table></td></tr></table></body></html>";
            $this->mail->AltBody = "Bonjour {$username},\n\nBienvenue sur Kr[HACK]ademy ! Votre compte a été créé avec succès. Connectez-vous dès maintenant pour commencer votre parcours d'apprentissage.\n\nLien de connexion : {$loginLink}";
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error on Welcome Email: {$this->mail->ErrorInfo}");
            return false;
        }
    }
}