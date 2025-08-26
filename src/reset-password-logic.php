<?php

require_once __DIR__ . '/services/User.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userModel = new User($pdo);
$token = $_GET['token'] ?? $_POST['token'] ?? null;

if (!$token) {
    header('Location: /login');
    exit();
}

$user = $userModel->findUserByResetToken($token);

if (!$user) {
    $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Lien invalide', 'message' => 'Ce lien de réinitialisation est invalide ou a expiré.'];
    return; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($password) || empty($password_confirm)) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Veuillez remplir tous les champs.'];
    } elseif (strlen($password) < 8) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Le mot de passe doit faire au moins 8 caractères.'];
    } elseif ($password !== $password_confirm) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Les mots de passe ne correspondent pas.'];
    } else {
        if ($userModel->updatePassword($user['id'], $password)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'title' => 'Succès !', 'message' => 'Votre mot de passe a été réinitialisé.'];
            header('Location: /login');
            exit();
        } else {
             $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Une erreur de base de données est survenue.'];
        }
    }
    
    header('Location: /reset-password?token=' . urlencode($token));
    exit();
}