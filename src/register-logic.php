<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/services/MailerService.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Veuillez remplir tous les champs.'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Le format de l\'adresse e-mail est invalide.'];
    } elseif (strlen($password) < 8) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Le mot de passe doit contenir au moins 8 caractères.'];
    } elseif ($password !== $password_confirm) {
        $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Les mots de passe ne correspondent pas.'];
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Ce nom d\'utilisateur ou e-mail est déjà utilisé.'];
        } else {
            $password_hash = password_hash($password, PASSWORD_ARGON2ID);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");

            if ($stmt->execute([$username, $email, $password_hash])) {
                $mailer = new MailerService();
                $mailer->sendWelcomeEmail($email, $username);
                
                $_SESSION['flash_message'] = ['type' => 'success', 'title' => 'Inscription réussie !', 'message' => 'Vous pouvez maintenant vous connecter.'];
                header('Location: /login');
                exit();
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Une erreur est survenue lors de la création du compte.'];
            }
        }
    }
    header('Location: /register');
    exit();
}