<?php
// src/lesson_page_logic.php

// 1. Récupérer l'ID de la leçon depuis l'URL
$lesson_id = $_GET['id'] ?? null;

if (!$lesson_id) {
    http_response_code(400);
    echo "ID de leçon manquant.";
    exit();
}

// 2. Récupérer TOUTES les informations de la leçon
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

// 3. Vérifier si la leçon existe
if (!$lesson) {
    http_response_code(404);
    echo "Leçon non trouvée.";
    exit();
}