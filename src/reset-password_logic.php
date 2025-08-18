<?php
$error_message = '';
$success_message = '';
$token = $_GET['token'] ?? $_POST['token'] ?? null;

if (!$token) {
    header('Location: /login'); // Pas de token, on redirige
    exit();
}

// Vérifier si le token est valide et non expiré
$stmt = $pdo->prepare("SELECT id, reset_token_expiry FROM users WHERE reset_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user || new DateTime() > new DateTime($user['reset_token_expiry'])) {
    $error_message = 'Ce lien de réinitialisation est invalide ou a expiré.';
    // On invalide le token dans le doute
    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error_message)) {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($password) || empty($password_confirm)) {
        $error_message = 'Veuillez remplir tous les champs.';
    } elseif (strlen($password) < 8) {
        $error_message = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif ($password !== $password_confirm) {
        $error_message = 'Les mots de passe ne correspondent pas.';
    } else {
        // Tout est bon, on met à jour le mot de passe et on invalide le token
        $password_hash = password_hash($password, PASSWORD_ARGON2ID);
        
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
        $stmt->execute([$password_hash, $user['id']]);

        $success_message = 'Votre mot de passe a été réinitialisé avec succès !';
    }
}