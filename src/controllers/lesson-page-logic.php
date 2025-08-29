<?php
$lesson_id = (int)($_GET['id'] ?? 0);
if ($lesson_id <= 0) {
    http_response_code(400);
    exit("ID de leçon invalide.");
}
$lessonService = new LessonService($pdo);
$userId = $_SESSION['user_id'] ?? null;
$lesson = $lessonService->findById($lesson_id, $userId);
if (!$lesson) {
    http_response_code(404);
    exit("Leçon non trouvée.");
}