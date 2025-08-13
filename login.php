<?php
// login.php
session_start();
require 'back-end/db_connect.php';

$error_message = '';

// Si l'utilisateur est déjà connecté, on le redirige vers l'accueil
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
            // Le mot de passe est correct, on démarre la session !
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Rediriger vers la page des leçons ou l'accueil
            header("Location: lessons.php");
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
    <link rel="stylesheet" href="./css/header-footer.css">
</head>
<body>
    <?php 
        // On peut même inclure le header sur cette page
        $currentPage = 'login'; 
        require 'header.php'; 
    ?>
    <main class="main" style="padding-top: 5rem;">
        <div class="auth-container" style="margin: auto; max-width: 400px; text-align: center;">
            <h2>Connexion</h2>
            <form action="login.php" method="post" style="display: flex; flex-direction: column; gap: 1rem;">
                <input type="text" name="username" placeholder="Nom d'utilisateur" required style="padding: 0.8rem; border-radius: 5px; border: 1px solid #ccc;">
                <input type="password" name="password" placeholder="Mot de passe" required style="padding: 0.8rem; border-radius: 5px; border: 1px solid #ccc;">
                <button type="submit" style="padding: 0.8rem; border: none; background-color: #764BA2; color: white; border-radius: 5px; cursor: pointer;">Se connecter</button>
            </form>
            <?php if (!empty($error_message)): ?>
                <p style="color: red; margin-top: 1rem;"><?= htmlspecialchars($error_message) ?></p>
            <?php endif; ?>
            <p style="margin-top: 1rem;">Pas encore de compte ? <a href="register.php">Inscrivez-vous</a></p>
        </div>
    </main>
    
    <?php require 'footer.php'; ?>
</body>
</html>