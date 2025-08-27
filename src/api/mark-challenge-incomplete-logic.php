<?php

require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

try {
    require_login();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
        exit();
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $challenge_id = $input['challenge_id'] ?? null;
    $user_id = $_SESSION['user_id'];

    if (!$challenge_id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID de challenge manquant.']);
        exit();
    }

    $sql = "DELETE FROM user_challenges_progress WHERE user_id = ? AND challenge_id = ?";
    
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$user_id, $challenge_id])) {
        echo json_encode(['status' => 'success', 'message' => 'Challenge marqué comme non terminé.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour de la base de données.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Une erreur serveur est survenue.', 'details' => $e->getMessage()]);
}