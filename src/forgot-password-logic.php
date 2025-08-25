<?php

use Krack\Models\User;
use Krack\Services\MailerService;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Veuillez fournir une adresse e-mail valide.'];
    } else {
        $userModel = new User();
        $user = $userModel->findByUsernameOrEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            if ($userModel->setResetToken($user['id'], $token)) {
                $mailer = new MailerService();
                if ($mailer->sendPasswordResetEmail($email, $token)) {
                    $_SESSION['flash_message'] = ['type' => 'success', 'title' => 'Email envoyé', 'message' => 'Un e-mail de réinitialisation vous a été envoyé.'];
                } else {
                    $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'L\'e-mail n\'a pas pu être envoyé.'];
                }
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Erreur lors de la génération du lien.'];
            }
        } else {
            $_SESSION['flash_message'] = ['type' => 'success', 'title' => 'Email envoyé', 'message' => 'Si un compte existe, un e-mail a été envoyé.'];
        }
    }
    header('Location: /forgot-password');
    exit();
}