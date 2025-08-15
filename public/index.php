<?php
// public/index.php

// 1. Initialisation de l'application
require_once __DIR__ . '/../src/bootstrap.php';

// 2. Récupération de l'URL
$request_uri = strtok($_SERVER['REQUEST_URI'], '?');
$currentPage = '';

// 3. Routeur
// Routes dynamiques (avec ID)
if (preg_match('/^\/challenge\/(\d+)$/', $request_uri, $matches)) {
    require_page_login(); // Protégé
    $_GET['id'] = $matches[1];
    $currentPage = 'challenges';
    require __DIR__ . '/../src/challenge_page_logic.php';
    require __DIR__ . '/../templates/challenge-page.phtml';

} elseif (preg_match('/^\/lesson\/(\d+)$/', $request_uri, $matches)) {
    require_page_login(); // Protégé
    $_GET['id'] = $matches[1];
    $currentPage = 'lessons';
    require __DIR__ . '/../src/lesson_page_logic.php';
    require __DIR__ . '/../templates/lesson-page.phtml';

} else {
    // Routes statiques et API
    switch ($request_uri) {
        // --- Routes des pages ---
        case '/':
            $currentPage = 'index';
            require __DIR__ . '/../templates/index.phtml';
            break;
        case '/lessons':
            require_page_login(); // Protégé
            $currentPage = 'lessons';
            require __DIR__ . '/../src/lessons_logic.php';
            require __DIR__ . '/../templates/lessons.phtml';
            break;
        case '/challenges':
            require_page_login(); // Protégé
            $currentPage = 'challenges';
            require __DIR__ . '/../src/challenges_logic.php';
            require __DIR__ . '/../templates/challenges.phtml';
            break;
        case '/scenarios':
            require_page_login(); // Protégé
            $currentPage = 'scenarios';
            echo "Page des scénarios en construction.";
            break;
        case '/login':
            $currentPage = 'login';
            require __DIR__ . '/../src/login_logic.php';
            require __DIR__ . '/../templates/login.phtml';
            break;
        case '/register':
            $currentPage = 'register';
            require __DIR__ . '/../src/register_logic.php';
            require __DIR__ . '/../templates/register.phtml';
            break;
        case '/logout':
            require __DIR__ . '/../src/logout.php';
            break;

        // --- Routes de l'API (protégées dans chaque fichier logique avec require_login()) ---
        case '/api/verify-flag':
            require __DIR__ . '/../src/api/verifier_logic.php';
            break;
        case '/api/mark-lesson-complete':
            require __DIR__ . '/../src/api/mark_lesson_logic.php';
            break;
        case '/api/mark-challenge-complete':
            require __DIR__ . '/../src/api/mark_challenge_logic.php';
            break;
        case '/api/get-progress':
            require __DIR__ . '/../src/api/get_progress_logic.php';
            break;

        // --- Page non trouvée ---
        default:
            http_response_code(404);
            echo "<h1>404 - Page non trouvée</h1>";
            break;
    }
}