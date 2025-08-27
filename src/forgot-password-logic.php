<?php

require_once __DIR__ . '/services/User.php';
require_once __DIR__ . '/services/MailerService.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userModel = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Veuillez fournir une adresse e-mail valide.'];
    } else {
        $user = $userModel->findByUsernameOrEmail($email);
        if ($user) {
            $token = bin2hex(random_bytes(32));
            if ($userModel->setResetToken($user['id'], $token)) {
                $mailer = new MailerService();
                $mailer->sendPasswordResetEmail($email, $user['username'], $token);
            }
        }
        $_SESSION['flash_message'] = [
            'type' => 'success', 
            'title' => 'Demande envoyée', 
            'message' => 'Si un compte est associé à cette adresse e-mail, vous recevrez un lien pour réinitialiser votre mot de passe.'
        ];
    }

    header('Location: /forgot-password');
    exit();
}