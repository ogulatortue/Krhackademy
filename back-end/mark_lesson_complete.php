<?php
// back-end/mark_lesson_complete.php
header('Content-Type: application/json');
require 'session_check.php';
require 'db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'];
$lessonIdStr = $input['id'] ?? '';

if (empty($lessonIdStr)) {
    echo json_encode(['success' => false, 'message' => 'ID de leçon manquant.']);
    exit();
}

$stmt = $pdo->prepare("SELECT id FROM lessons WHERE lesson_id_str = ?");
$stmt->execute([$lessonIdStr]);
$itemId = $stmt->fetchColumn();

if ($itemId) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO user_lesson_progress (user_id, lesson_id) VALUES (?, ?)");
    $stmt->execute([$userId, $itemId]);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Leçon inconnue.']);
}