<?php
// src/api/verifier_logic.php

// On inclut le bootstrap pour avoir $pdo et la fonction require_login
require_once __DIR__ . '/../bootstrap.php';

// 1. Sécurité : on vérifie que l'utilisateur est connecté
require_login();

// On s'assure que la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit('Méthode non autorisée.');
}

// 2. Récupérer les données envoyées (souvent en JSON par fetch)
$input = json_decode(file_get_contents('php://input'), true);
$challenge_id = $input['challenge_id'] ?? null;
$submitted_flag = $input['flag'] ?? '';

// On prépare une réponse JSON
header('Content-Type: application/json');
$response = [];

// 3. Logique de vérification
$stmt = $pdo->prepare("SELECT flag FROM challenges WHERE id = ?");
$stmt->execute([$challenge_id]);
$challenge = $stmt->fetch();

if ($challenge && hash_equals($challenge['flag'], $submitted_flag)) {
    // Le flag est correct !
    // Ici, vous ajouteriez la logique pour marquer le challenge comme réussi pour l'utilisateur
    $response = ['status' => 'success', 'message' => 'Flag correct ! Bien joué !'];
} else {
    // Le flag est incorrect
    $response = ['status' => 'error', 'message' => 'Flag incorrect. Essayez encore.'];
}

// 4. Envoyer la réponse
echo json_encode($response);