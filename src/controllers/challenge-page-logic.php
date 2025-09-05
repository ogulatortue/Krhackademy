<?php
$challenge_slug = trim($_GET['id'] ?? '');

if (empty($challenge_slug)) {
    http_response_code(400);
    exit("Identifiant de challenge invalide.");
}

$challengeService = new ChallengeService($pdo);
$userId = $_SESSION['user_id'] ?? null;

$challenge = $challengeService->findBySlug($challenge_slug, $userId);

if (!$challenge) {
    http_response_code(404);
    exit("Challenge non trouvé.");
}