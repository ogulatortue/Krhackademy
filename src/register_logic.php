<?php
// src/register_logic.php

$error_message = '';

// Si l'utilisateur est déjà connecté, on le redirige vers l'accueil
if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit();
}

// On exécute ce bloc uniquement si le formulaire a été envoyé (méthode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Récupérer et nettoyer les données du formulaire
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // 2. Valider les données
    if (empty($username) || empty($password) || empty($password_confirm)) {
        $error_message = 'Veuillez remplir tous les champs.';
    } elseif (strlen($password) < 8) {
        $error_message = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif ($password !== $password_confirm) {
        $error_message = 'Les mots de passe ne correspondent pas.';
    } else {
        // 3. Vérifier si le nom d'utilisateur est déjà pris
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error_message = 'Ce nom d\'utilisateur est déjà pris.';
        } else {
            // 4. Tout est bon, on peut créer l'utilisateur
            
            // Hachage sécurisé du mot de passe
            $password_hash = password_hash($password, PASSWORD_ARGON2ID);

            // Insertion dans la base de données
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            
            if ($stmt->execute([$username, $password_hash])) {
                // Création réussie, on redirige vers la page de connexion
                // On pourrait aussi ajouter un message de succès
                header('Location: /login');
                exit();
            } else {
                $error_message = 'Une erreur est survenue lors de la création du compte.';
            }
        }
    }
}