<?php
require_once __DIR__ . '/../bootstrap.php';
header('Content-Type: application/json');
require_login();

$lessonService = new LessonService($pdo);
$challengeService = new ChallengeService($pdo);
$userId = $_SESSION['user_id'];

$progress = [
    'lessons' => $lessonService->findCompletedIdsForUser($userId),
    'challenges' => $challengeService->findCompletedIdsForUser($userId),
];

echo json_encode(['status' => 'success', 'data' => $progress]);