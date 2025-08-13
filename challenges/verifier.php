<?php
// challenges/verifier.php

// 1. Démarrer la session et la connexion à la BDD
// Les '..' remontent d'un dossier pour trouver le dossier 'back-end'
require '../back-end/session_check.php'; 
require '../back-end/db_connect.php';

// 2. Récupérer les données du formulaire
$challengeIdStr = $_POST['challenge_id'] ?? ''; // Assurez-vous d'avoir un <input type="hidden" name="challenge_id" value="forensics-metadata-hunt"> dans votre formulaire HTML
$submittedFlag = $_POST['flag'] ?? '';

// 3. Vérifier si le flag est correct (VOTRE LOGIQUE ICI)
$isFlagCorrect = false;
$correctFlag = "KRHACK{exemple_de_flag_correct}"; // Vous récupérerez ceci de la BDD ou d'un fichier de config

if ($submittedFlag === $correctFlag) {
    $isFlagCorrect = true;
}

// 4. Si le flag est correct, on enregistre la progression
if ($isFlagCorrect) {
    // On récupère l'ID numérique du challenge à partir de son nom
    $stmt = $pdo->prepare("SELECT id FROM challenges WHERE challenge_id_str = ?");
    $stmt->execute([$challengeIdStr]);
    $itemId = $stmt->fetchColumn();

    if ($itemId) {
        // On insère la progression dans la BDD. INSERT IGNORE évite les erreurs si déjà complété.
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_challenge_progress (user_id, challenge_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $itemId]);
    }
    
    // Rediriger vers la page de succès ou la liste des challenges
    header('Location: ../challenges.html?status=success');
    exit();

} else {
    // Si le flag est incorrect
    header('Location: challtemp.html?status=error'); // Renvoyer sur la page du challenge avec un message d'erreur
    exit();
}