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
$userId = $_SESSION['user_id'];
$status = $input['status'] ?? null;
$type = $input['type'] ?? null;
$id = $input['id'] ?? null;

if ($type === 'challenge' && $status === 'complete') {
    http_response_code(403);
    exit(json_encode(['status' => 'error', 'message' => 'Action non autorisée.']));
}

if (!$type || !$id || !$status) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Paramètres manquants.']));
}

$service = null;
if ($type === 'lesson') {
    $service = new LessonService($pdo);
} elseif ($type === 'challenge') {
    $service = new ChallengeService($pdo);
} else {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Type invalide.']));
}

$success = false;
if ($status === 'complete') {
    $success = $service->markAsComplete($userId, $id);
} elseif ($status === 'incomplete') {
    $success = $service->markAsIncomplete($userId, $id);
}

if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Progression mise à jour.']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour.']);
}