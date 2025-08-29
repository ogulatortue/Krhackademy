<?php
$challenge_id = (int)($_GET['id'] ?? 0);
if ($challenge_id <= 0) {
    http_response_code(400);
    exit("ID de challenge invalide.");
}
$challengeService = new ChallengeService($pdo);
$userId = $_SESSION['user_id'] ?? null;
$challenge = $challengeService->findById($challenge_id, $userId);
if (!$challenge) {
    http_response_code(404);
    exit("Challenge non trouvé.");
}