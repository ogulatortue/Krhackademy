<?php
// src/api/get_progress_logic.php

require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

try {
    // On vérifie que l'utilisateur est bien connecté
    require_login();

    $user_id = $_SESSION['user_id'];

    // Récupérer les leçons terminées
    $stmt_lessons = $pdo->prepare("SELECT lesson_id FROM user_lessons_progress WHERE user_id = ?");
    $stmt_lessons->execute([$user_id]);
    $completed_lessons = $stmt_lessons->fetchAll(PDO::FETCH_COLUMN);

    // Récupérer les challenges réussis
    $stmt_challenges = $pdo->prepare("SELECT challenge_id FROM user_challenges_progress WHERE user_id = ?");
    $stmt_challenges->execute([$user_id]);
    $completed_challenges = $stmt_challenges->fetchAll(PDO::FETCH_COLUMN);

    // Préparer la réponse en cas de succès
    $progress = [
        'lessons' => $completed_lessons,
        'challenges' => $completed_challenges,
    ];

    echo json_encode(['status' => 'success', 'data' => $progress]);

} catch (Exception $e) {
    // S'il y a la moindre erreur, on l'attrape et on renvoie un JSON propre
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Une erreur est survenue en récupérant la progression.', 
        'details' => $e->getMessage() // On inclut le message d'erreur technique
    ]);
}