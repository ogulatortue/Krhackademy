<?php
session_start();
require 'back-end/db_connect.php';

$error_message = '';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_message = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            $redirect_to = $_POST['redirect_url'] ?? '';

            if (!empty($redirect_to) && parse_url($redirect_to, PHP_URL_HOST) === null) {
                header('Location: ' . $redirect_to);
            } else {
                header('Location: lessons.php');
            }
            exit();
            
        } else {
            $error_message = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Kr[HACK]ademy</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/auth.css">
</head>
<body class="auth-page">

    <div class="auth-container">
        <h1 class="site-title">Kr[HACK]ademy</h1>
        <h2 class="page-title">Connexion</h2>
        
        <form action="login.php" method="post" class="auth-form">
            <input type="hidden" name="redirect_url" value="<?= htmlspecialchars($_GET['redirect_url'] ?? '') ?>">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <p class="auth-link">
            Pas encore de compte ? <a href="register.php">Inscrivez-vous</a>
        </p>
    </div>

</body>
</html>