<?php
session_start();
require 'back-end/db_connect.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error_message = "Veuillez remplir tous les champs.";
    } elseif (strlen($password) < 8) {
        $error_message = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error_message = "Ce nom d'utilisateur ou cet email est déjà utilisé.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $password_hash])) {
                header("Location: login.php?status=registered");
                exit();
            } else {
                $error_message = "Une erreur est survenue. Veuillez réessayer.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Kr[HACK]ademy</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/auth.css">
</head>
<body class="auth-page">

    <div class="auth-container">
        <h1 class="site-title">Kr[HACK]ademy</h1>
        <h2 class="page-title">Inscription</h2>

        <form action="register.php" method="post" class="auth-form">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe (8 caractères min)" required>
            <button type="submit">S'inscrire</button>
        </form>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <p class="auth-link">
            Déjà un compte ? <a href="login.php">Connectez-vous</a>
        </p>
    </div>

</body>
</html>