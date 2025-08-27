<?php

require_once __DIR__ . '/../bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée.');
}

$input = json_decode(file_get_contents('php://input'), true);
$challenge_id = $input['challenge_id'] ?? null;
$submitted_flag = $input['flag'] ?? '';

header('Content-Type: application/json');
$response = [];

$stmt = $pdo->prepare("SELECT id, flag FROM challenges WHERE id = ?");
$stmt->execute([$challenge_id]);
$challenge = $stmt->fetch();

if (!$challenge) {
    echo json_encode(['status' => 'error', 'message' => 'Challenge non trouvé.']);
    exit();
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_challenges_progress WHERE user_id = ? AND challenge_id = ?");
$stmt->execute([$_SESSION['user_id'], $challenge_id]);
$is_completed = $stmt->fetchColumn() > 0;

if ($is_completed) {
    echo json_encode(['status' => 'success', 'message' => 'Ce challenge est déjà validé !']);
    exit();
}

if (strtolower($challenge['flag']) === strtolower($submitted_flag)) {
    
    $response = ['status' => 'success', 'message' => 'Flag correct ! Bien joué !'];

    $stmt_solve = $pdo->prepare(
        "INSERT IGNORE INTO user_challenges_progress (user_id, challenge_id, completed_at) VALUES (?, ?, NOW())"
    );
    $stmt_solve->execute([$_SESSION['user_id'], $challenge_id]);

} else {
    $response = ['status' => 'error', 'message' => 'Flag incorrect. Essayez encore.'];
}

echo json_encode($response);