<?php
// src/challenges_logic.php

$stmt = $pdo->query("
    SELECT 
        id, 
        challenge_id_str, 
        title, 
        category, 
        points,
        icon_class 
    FROM 
        challenges 
    ORDER BY 
        category ASC,
        CASE
            WHEN points < 50 THEN 1
            WHEN points >= 50 AND points < 100 THEN 2
            WHEN points >= 100 AND points < 250 THEN 3
            WHEN points >= 250 THEN 4
            ELSE 5
        END ASC,
        points ASC,
        id ASC
");

$challenges = $stmt->fetchAll();