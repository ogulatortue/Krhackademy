<?php
// src/challenge_page_logic.php

// 1. Récupérer l'ID du challenge depuis l'URL
$challenge_id = $_GET['id'] ?? null;

if (!$challenge_id) {
    http_response_code(400); // Bad Request
    echo "ID de challenge manquant.";
    exit();
}

// 2. Récupérer TOUTES les informations du challenge
$stmt = $pdo->prepare("
    SELECT 
        id, challenge_id_str, title, category, points, icon_class 
    FROM 
        challenges 
    WHERE 
        id = ?
");
$stmt->execute([$challenge_id]);
$challenge = $stmt->fetch();

// 3. Vérifier si le challenge existe
if (!$challenge) {
    http_response_code(404);
    echo "Challenge non trouvé.";
    exit();
}

// La variable $challenge, contenant maintenant toutes les colonnes, est disponible pour la vue.