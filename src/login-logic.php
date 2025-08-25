<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: /");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($identifier) || empty($password)) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Veuillez remplir tous les champs.'];
        header('Location: /login');
        exit();
    }

    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        $_SESSION['flash_message'] = ['type' => 'success', 'title' => 'Connexion réussie', 'message' => 'Bienvenue, ' . htmlspecialchars($user['username']) . ' !'];

        $redirectTo = $_POST['redirect_to'] ?? '/';
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