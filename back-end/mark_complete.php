<?php
// api/mark_complete.php
header('Content-Type: application/json');
require '../includes/session_check.php';
require '../includes/db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'];
$type = $input['type'] ?? '';
$idStr = $input['id'] ?? '';

if (empty($type) || empty($idStr)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data.']);
    exit();
}

if ($type === 'lesson') {
    $stmt = $pdo->prepare("SELECT id FROM lessons WHERE lesson_id_str = ?");
    $stmt->execute([$idStr]);
    $itemId = $stmt->fetchColumn();

    if ($itemId) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_lesson_progress (user_id, lesson_id) VALUES (?, ?)");
        $stmt->execute([$userId, $itemId]);
    }
} elseif ($type === 'challenge') {
    $stmt = $pdo->prepare("SELECT id FROM challenges WHERE challenge_id_str = ?");
    $stmt->execute([$idStr]);
    $itemId = $stmt->fetchColumn();

    if ($itemId) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_challenge_progress (user_id, challenge_id) VALUES (?, ?)");
        $stmt->execute([$userId, $itemId]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid type.']);
    exit();
}

echo json_encode(['success' => true]);
?>