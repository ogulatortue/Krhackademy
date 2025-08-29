<?php
$lessonService = new LessonService($pdo);
$userId = $_SESSION['user_id'] ?? null;
$lessons = $lessonService->findAllSorted($userId);