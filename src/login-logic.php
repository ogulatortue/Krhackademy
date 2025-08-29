<?php

require_once __DIR__ . '/bootstrap.php';

if (isset($_SESSION['user_id'])) {
    header("Location: /");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    verify_csrf_token();
    
    $identifier = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($identifier) || empty($password)) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Veuillez remplir tous les champs.'];
        header('Location: /login');
        exit();
    }
    
    $userModel = new User($pdo);
    $user = $userModel->findByUsernameOrEmail($identifier);

    if ($user && password_verify($password, $user['password_hash'])) {
        // Régénère l'ID de session pour prévenir la fixation de session
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['flash_message'] = ['type' => 'success', 'title' => 'Connexion réussie', 'message' => 'Bienvenue, ' . htmlspecialchars($user['username']) . ' !'];

        $redirectTo = $_POST['redirect_to'] ?? '/';
        // Sécurité : empêche les redirections vers des sites externes
        if (empty($redirectTo) || parse_url($redirectTo, PHP_URL_HOST) !== null) {
            $redirectTo = '/';
        }
        header('Location: ' . $redirectTo);
        exit();
    } else {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Échec de la connexion', 'message' => 'Identifiant ou mot de passe incorrect.'];
        header('Location: /login');
        exit();
    }
}