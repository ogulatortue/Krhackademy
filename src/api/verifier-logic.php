<?php
require_once __DIR__ . '/../bootstrap.php';
header('Content-Type: application/json');
require_login();
verify_csrf_token();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']));
}

$input = json_decode(file_get_contents('php://input'), true);
$challengeId = $input['challenge_id'] ?? null;
$submittedFlag = $input['flag'] ?? '';
$userId = $_SESSION['user_id'];

if (!$challengeId) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'ID de challenge manquant.']));
}

$challengeService = new ChallengeService($pdo);
$challenge = $challengeService->findById($challengeId, $userId);

if (!$challenge) {
    http_response_code(404);
    exit(json_encode(['status' => 'error', 'message' => 'Challenge non trouvé.']));
}

if ($challenge['is_completed']) {
    exit(json_encode(['status' => 'success', 'message' => 'Ce challenge est déjà validé !']));
}

$correctFlag = $challengeService->getFlag($challengeId);

if (strtolower($correctFlag) === strtolower($submittedFlag)) {
    $challengeService->markAsComplete($userId, $challengeId);
    echo json_encode(['status' => 'success', 'message' => 'Flag correct ! Bien joué !']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Flag incorrect. Essayez encore.']);
}