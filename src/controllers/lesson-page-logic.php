<?php
$lesson_id_str = $_GET['id'] ?? '';

if (empty($lesson_id_str)) {
    http_response_code(400);
    exit("ID de leçon invalide.");
}

$lessonService = new LessonService($pdo);
$userId = $_SESSION['user_id'] ?? null;

$lesson = $lessonService->findByLessonIdStr($lesson_id_str, $userId);

if (!$lesson) {
    http_response_code(404);
    exit("Leçon non trouvée.");
}