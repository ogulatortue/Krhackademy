<?php

require_once __DIR__ . '/../bootstrap.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    
    $userModel = new User($pdo);
    $data = [
        'username' => trim($_POST['username'] ?? ''),
        'email'    => trim($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'password_confirm' => $_POST['password_confirm'] ?? '',
    ];

    $errors = ValidationService::validateRegistration($data);

    if (!empty($errors)) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur de validation', 'message' => implode('<br>', $errors)];
    } elseif ($userModel->findByUsernameOrEmail($data['username']) || $userModel->findByUsernameOrEmail($data['email'])) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Ce nom d\'utilisateur ou cet e-mail est déjà utilisé.'];
    } else {
        if ($userModel->createUser($data['username'], $data['email'], $data['password'])) {
            
            $tempMailer = new MailerService();
            $tempMailer->prepareWelcomeEmail($data['email'], $data['username']);

            $subject = $tempMailer->getSubject();
            $body = $tempMailer->getBody();
            
            $stmt = $pdo->prepare("INSERT INTO email_queue (recipient, subject, body) VALUES (?, ?, ?)");
            $stmt->execute([$data['email'], $subject, $body]);

            $_SESSION['flash_message'] = ['type' => 'success', 'title' => 'Inscription réussie !', 'message' => 'Votre compte a été créé. Vous pouvez maintenant vous connecter.'];
            header('Location: /login');
            exit();
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur serveur', 'message' => 'Une erreur est survenue lors de la création du compte.'];
        }
    }
    
    header('Location: /register');
    exit();
}