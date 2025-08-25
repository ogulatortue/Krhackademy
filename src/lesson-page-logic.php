<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$lesson_id = $_GET['id'] ?? null;

if (!$lesson_id) {
    http_response_code(400);
    echo "ID de leçon manquant.";
    exit();
}

$stmt = $pdo->prepare("
    SELECT 
        id, lesson_id_str, title, category, description, difficulty, icon_class 
    FROM 
        lessons 
    WHERE 
        id = ?
");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch();

if (!$lesson) {
    http_response_code(404);
    echo "Leçon non trouvée.";
    exit();
}

$lesson['is_completed'] = false;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $completion_stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM user_lessons_progress 
    WHERE user_id = ? AND lesson_id = ?
    ");
    $completion_stmt->execute([$user_id, $lesson_id]);
    
    $count = $completion_stmt->fetchColumn();

    if ($count > 0) {
        $lesson['is_completed'] = true;
    }
}