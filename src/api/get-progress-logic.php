<?php

require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

try {
    require_login();

    $user_id = $_SESSION['user_id'];

    $stmt_lessons = $pdo->prepare("SELECT lesson_id FROM user_lessons_progress WHERE user_id = ?");
    $stmt_lessons->execute([$user_id]);
    $completed_lessons = $stmt_lessons->fetchAll(PDO::FETCH_COLUMN);

    $stmt_challenges = $pdo->prepare("SELECT challenge_id FROM user_challenges_progress WHERE user_id = ?");
    $stmt_challenges->execute([$user_id]);
    $completed_challenges = $stmt_challenges->fetchAll(PDO::FETCH_COLUMN);

    $progress = [
        'lessons' => $completed_lessons,
        'challenges' => $completed_challenges,
    ];

    echo json_encode(['status' => 'success', 'data' => $progress]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Une erreur est survenue en récupérant la progression.', 
        'details' => $e->getMessage()
    ]);
}