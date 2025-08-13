<?php

header('Content-Type: application/json');
require '../includes/session_check.php';
require '../includes/db_connect.php';

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT l.lesson_id_str 
    FROM user_lesson_progress ulp
    JOIN lessons l ON ulp.lesson_id = l.id
    WHERE ulp.user_id = ?
");
$stmt->execute([$userId]);
$completedLessons = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->prepare("
    SELECT c.challenge_id_str
    FROM user_challenge_progress ucp
    JOIN challenges c ON ucp.challenge_id = c.id
    WHERE ucp.user_id = ?
");
$stmt->execute([$userId]);
$completedChallenges = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode([
    'lessons' => $completedLessons,
    'challenges' => $completedChallenges
]);
?>