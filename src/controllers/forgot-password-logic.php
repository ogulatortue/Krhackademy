<?php

require_once __DIR__ . '/../bootstrap.php';

$userModel = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Veuillez fournir une adresse e-mail valide.'];
    } else {
        $user = $userModel->findByUsernameOrEmail($email);
        
        if ($user) {
            $token = bin2hex(random_bytes(32));
            if ($userModel->setResetToken($user['id'], $token)) {
                $mailer = new MailerService();
                $mailer->sendPasswordResetEmail($user['email'], $user['username'], $token);
            }
        }
        
        $_SESSION['flash_message'] = [
            'type' => 'success', 
            'title' => 'Demande envoyée', 
            'message' => 'Si un compte est associé à cette adresse, vous recevrez un lien pour réinitialiser votre mot de passe.'
        ];
    }

    header('Location: /forgot-password');
    exit();
}