<?php

require_once __DIR__ . '/../bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée.');
}

$input = json_decode(file_get_contents('php://input'), true);
$challenge_id = $input['challenge_id'] ?? null;
$user_id = $_SESSION['user_id'];

header('Content-Type: application/json');

if (!$challenge_id) {
    echo json_encode(['status' => 'error', 'message' => 'ID de challenge manquant.']);
    exit();
}

$sql = "INSERT INTO user_challenges_progress (user_id, challenge_id, completed_at) VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE completed_at = NOW()";

$stmt = $pdo->prepare($sql);

if ($stmt->execute([$user_id, $challenge_id])) {
    echo json_encode(['status' => 'success', 'message' => 'Challenge marqué comme réussi !']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour de la progression.']);
}