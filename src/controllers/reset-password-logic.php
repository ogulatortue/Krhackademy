<?php

require_once __DIR__ . '/bootstrap.php';

$userModel = new User($pdo);
$token = $_GET['token'] ?? $_POST['token'] ?? null;

if (!$token) {
    header('Location: /login');
    exit();
}

// L'utilisateur est recherché une seule fois au chargement de la page
$user = $userModel->findUserByResetToken($token);

if (!$user && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Lien invalide', 'message' => 'Ce lien de réinitialisation est invalide ou a expiré.'];
    // On peut rediriger ici ou laisser le template afficher le message
    header('Location: /login');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // On revérifie l'utilisateur en cas de soumission de formulaire, pour s'assurer qu'il n'a pas expiré entre temps
    if (!$user) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Lien invalide', 'message' => 'Ce lien de réinitialisation est invalide ou a expiré.'];
        header('Location: /login');
        exit();
    }
    
    verify_csrf_token();
    
    $data = [
        'password' => $_POST['password'] ?? '',
        'password_confirm' => $_POST['password_confirm'] ?? '',
    ];

    $errors = ValidationService::validatePasswordReset($data);

    if (!empty($errors)) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur de validation', 'message' => implode('<br>', $errors)];
    } else {
        if ($userModel->updatePassword($user['id'], $data['password'])) {
            $userModel->clearResetToken($user['id']);
            
            $_SESSION['flash_message'] = ['type' => 'success', 'title' => 'Succès !', 'message' => 'Votre mot de passe a été réinitialisé avec succès.'];
            header('Location: /login');
            exit();
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur serveur', 'message' => 'Une erreur de base de données est survenue.'];
        }
    }
    
    header('Location: /reset-password?token=' . urlencode($token));
    exit();
}