<?php

require_page_login();

$currentUserId = $_SESSION['user_id'] ?? null;

$sql = "
    SELECT
        u.id,
        u.username,
        COALESCE(SUM(c.points), 0) AS score,
        MAX(ucp.completed_at) as last_completion_date
    FROM
        users u
    LEFT JOIN
        user_challenges_progress ucp ON u.id = ucp.user_id
    LEFT JOIN
        challenges c ON ucp.challenge_id = c.id
    GROUP BY
        u.id, u.username
    ORDER BY
        score DESC, last_completion_date ASC;
";

$stmt = $pdo->query($sql);
$leaderboardData = $stmt->fetchAll();
