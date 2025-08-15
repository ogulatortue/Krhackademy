<?php
// src/api/mark_challenge_logic.php

require_once __DIR__ . '/../bootstrap.php';

// Sécurité : l'utilisateur doit être connecté pour cette action.
require_login();

// On accepte uniquement les requêtes POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit('Méthode non autorisée.');
}

// On récupère les données envoyées en JSON par le JavaScript.
$input = json_decode(file_get_contents('php://input'), true);
$challenge_id = $input['challenge_id'] ?? null;
$user_id = $_SESSION['user_id'];

// On prépare une réponse JSON.
header('Content-Type: application/json');

if (!$challenge_id) {
    echo json_encode(['status' => 'error', 'message' => 'ID de challenge manquant.']);
    exit();
}

// Requête pour insérer la progression.
// La clause "ON DUPLICATE KEY UPDATE" évite les erreurs si l'utilisateur
// complète le challenge plusieurs fois.
$sql = "INSERT INTO user_challenges_progress (user_id, challenge_id, completed_at) VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE completed_at = NOW()";

$stmt = $pdo->prepare($sql);

if ($stmt->execute([$user_id, $challenge_id])) {
    echo json_encode(['status' => 'success', 'message' => 'Challenge marqué comme réussi !']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour de la progression.']);
}