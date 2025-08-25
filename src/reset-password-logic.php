<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$token = $_GET['token'] ?? $_POST['token'] ?? null;

if (!$token) {
    header('Location: /login');
    exit();
}

$stmt = $pdo->prepare("SELECT id, reset_token_expiry FROM users WHERE reset_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user || new DateTime() > new DateTime($user['reset_token_expiry'])) {
    $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Lien invalide', 'message' => 'Ce lien de réinitialisation est invalide ou a expiré.'];
    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
    }
    header('Location: /forgot-password');
    exit();
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
        $password_hash = password_hash($password, PASSWORD_ARGON2ID);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
        $stmt->execute([$password_hash, $user['id']]);
        
        $_SESSION['flash_message'] = ['type' => 'success', 'title' => 'Succès !', 'message' => 'Votre mot de passe a été réinitialisé.'];
        header('Location: /login');
        exit();
    }
    header('Location: /reset-password?token=' . $token);
    exit();
}