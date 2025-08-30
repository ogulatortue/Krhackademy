<?php

require_once __DIR__ . '/../bootstrap.php';

require_page_login();

$userService = new User($pdo);
$currentUserId = $_SESSION['user_id'];
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    
    if (isset($_POST['action']) && $_POST['action'] === 'update_account') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($username) || empty($email)) {
            $errors[] = "Le pseudo et l'e-mail ne peuvent pas être vides.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse e-mail n'est pas valide.";
        } else {
            if ($userService->updateAccountInfo($currentUserId, $username, $email)) {
                $success = "Vos informations ont été mises à jour avec succès.";
            } else {
                $errors[] = "Une erreur est survenue lors de la mise à jour.";
            }
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $user = $userService->findById($currentUserId);

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $errors[] = "Tous les champs de mot de passe sont requis.";
        } elseif (!password_verify($currentPassword, $user['password_hash'])) {
            $errors[] = "Le mot de passe actuel est incorrect.";
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = "Les nouveaux mots de passe ne correspondent pas.";
        } elseif (strlen($newPassword) < 8) {
            $errors[] = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
        } else {
            if ($userService->updatePassword($currentUserId, $newPassword)) {
                $success = "Votre mot de passe a été changé avec succès.";
            } else {
                $errors[] = "Une erreur est survenue lors du changement de mot de passe.";
            }
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'delete_account') {
        if ($userService->deleteUser($currentUserId)) {
            header('Location: /logout');
            exit();
        } else {
            $errors[] = "Impossible de supprimer le compte. Veuillez réessayer.";
        }
    }
}

$currentUser = $userService->findById($currentUserId);

if (!$currentUser) {
    header('Location: /logout');
    exit();
}