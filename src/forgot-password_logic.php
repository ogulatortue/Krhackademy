<?php
// src/forgot-password_logic.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Veuillez fournir une adresse e-mail valide.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
            $stmt->execute([$token, $expiry, $user['id']]);

            $mail = new PHPMailer(true);
            try {
                // --- CONFIGURATION MODIFIÉE POUR UTILISER LES VARIABLES D'ENVIRONNEMENT ---
                $mail->isSMTP();
                $mail->Host       = $_ENV['MAIL_HOST'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['MAIL_USERNAME'];
                $mail->Password   = $_ENV['MAIL_PASSWORD'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = $_ENV['MAIL_PORT'] ?? 465;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
                $mail->addAddress($email);

                $resetLink = "http://{$_SERVER['HTTP_HOST']}/reset-password?token=" . $token;
                $mail->isHTML(true);
                $mail->Subject = 'Réinitialisation de votre mot de passe';
                $mail->Body    = "Bonjour,<br><br>Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien suivant : <a href='{$resetLink}'>{$resetLink}</a><br><br>Ce lien expirera dans une heure.<br>Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet e-mail.";
                $mail->AltBody = "Bonjour,\n\nPour réinitialiser votre mot de passe, veuillez copier-coller ce lien dans votre navigateur : {$resetLink}\n\nCe lien expirera dans une heure.";

                $mail->send();
                $success_message = 'Un e-mail de réinitialisation a été envoyé à votre adresse.';

            } catch (Exception $e) {
                // Pour le débogage, vous pouvez logger l'erreur. Pour l'utilisateur, un message générique est mieux.
                // error_log("PHPMailer Error: " . $mail->ErrorInfo);
                $error_message = "L'e-mail n'a pas pu être envoyé. Veuillez réessayer plus tard.";
            }
        } else {
            $success_message = 'Si un compte correspondant à cette adresse existe, un e-mail de réinitialisation a été envoyé.';
        }
    }
}