<?php
// src/lessons_logic.php

$stmt = $pdo->query("
    SELECT 
        id, 
        lesson_id_str, 
        title, 
        category, 
        description, 
        difficulty, 
        icon_class 
    FROM 
        lessons 
    ORDER BY 
        category ASC, -- 1. Tri principal par catégorie
        CASE difficulty -- 2. Tri secondaire par difficulté (ordre logique)
            WHEN 'Débutant' THEN 1
            WHEN 'Initié' THEN 2
            WHEN 'Avancé' THEN 3
            WHEN 'Expert' THEN 4
            ELSE 5
        END ASC,
        id ASC -- 3. Tri final en cas d'égalité parfaite
");

$lessons = $stmt->fetchAll();