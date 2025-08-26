<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$challenge_id = $_GET['id'] ?? null;

if (!$challenge_id) {
    http_response_code(400);
    echo "ID de challenge manquant.";
    exit();
}

$stmt = $pdo->prepare(" 
    SELECT 
        id, challenge_id_str, title, category, points, icon_class 
    FROM 
        challenges 
    WHERE 
        id = ?
");
$stmt->execute([$challenge_id]);
$challenge = $stmt->fetch();

if (!$challenge) {
    http_response_code(404);
    echo "Challenge non trouvé.";
    exit();
}

$challenge['is_completed'] = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $completion_stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM user_challenges_progress 
        WHERE user_id = ? AND challenge_id = ?
    ");
    $completion_stmt->execute([$user_id, $challenge_id]);
    $count = $completion_stmt->fetchColumn();
    if ($count > 0) {
        $challenge['is_completed'] = true;
    }
}