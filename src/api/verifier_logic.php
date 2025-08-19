<?php

require_once __DIR__ . '/../bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée.');
}

$input = json_decode(file_get_contents('php://input'), true);
$challenge_id_str = $input['challenge_id'] ?? null;
$submitted_flag_raw = $input['flag'] ?? '';

header('Content-Type: application/json');
$response = [];

$extracted_flag = null;
if (preg_match('/^KRHACK\{(.+)\}$/', $submitted_flag_raw, $matches)) {
    $extracted_flag = $matches[1];
}

$stmt = $pdo->prepare("SELECT id, flag FROM challenges WHERE challenge_id_str = ?");
$stmt->execute([$challenge_id_str]);
$challenge = $stmt->fetch();

if ($challenge && $extracted_flag !== null && hash_equals($challenge['flag'], $extracted_flag)) {
    
    $response = ['status' => 'success', 'message' => 'Flag correct ! Bien joué !'];

    // --- MISE À JOUR SELON VOTRE STRUCTURE ---

    $user_id = $_SESSION['user_id'];
    $challenge_id = $challenge['id'];

    // On utilise le bon nom de table (user_challenges_progress)
    // et on ajoute la colonne `completed_at` avec la date actuelle via NOW()
    $stmt_solve = $pdo->prepare(
        "INSERT IGNORE INTO user_challenges_progress (user_id, challenge_id, completed_at) VALUES (?, ?, NOW())"
    );
    $stmt_solve->execute([$user_id, $challenge_id]);
    
    // --- FIN DE LA MISE À JOUR ---

} else {
    $response = ['status' => 'error', 'message' => 'Flag incorrect. Essayez encore.'];
}

echo json_encode($response);