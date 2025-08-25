<?php

$error_message = '';

if (isset($_SESSION['user_id'])) {
    header("Location: /");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $identifier = trim($_POST['username']); 
    $password = $_POST['password'];

    if (empty($identifier) || empty($password)) {
        $error_message = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?");
        
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            $redirectTo = $_POST['redirect_to'] ?? '/';

            if (empty($redirectTo) || parse_url($redirectTo, PHP_URL_HOST) !== null) {
                $redirectTo = '/';
            }
            
            header('Location: ' . $redirectTo);
            exit();
            
        } else {
            $error_message = "Identifiant ou mot de passe incorrect.";
        }
    }
}
?>