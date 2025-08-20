<?php
require_once __DIR__ . '/../src/bootstrap.php';

$request_uri = strtok($_SERVER['REQUEST_URI'], '?');
$currentPage = '';

if (preg_match('/^\/challenge\/(\d+)$/', $request_uri, $matches)) {
    require_page_login();
    $_GET['id'] = $matches[1];
    $currentPage = 'challenges';
    require __DIR__ . '/../src/challenge_page_logic.php';
    require __DIR__ . '/../templates/challenge-page.phtml';
} elseif (preg_match('/^\/lesson\/(\d+)$/', $request_uri, $matches)) {
    require_page_login();
    $_GET['id'] = $matches[1];
    $currentPage = 'lessons';
    require __DIR__ . '/../src/lesson_page_logic.php';
    require __DIR__ . '/../templates/lesson-page.phtml';
} else {
    switch ($request_uri) {
        case '/':
            $currentPage = 'index';
            require __DIR__ . '/../templates/index.phtml';
            break;
        case '/lessons':
            $currentPage = 'lessons';
            require __DIR__ . '/../src/lessons_logic.php';
            require __DIR__ . '/../templates/lessons.phtml';
            break;
        case '/challenges':
            $currentPage = 'challenges';
            require __DIR__ . '/../src/challenges_logic.php';
            require __DIR__ . '/../templates/challenges.phtml';
            break;
        case '/scenarios':
            require_page_login();
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
        case '/forgot-password':
            $currentPage = 'forgot-password';
            require __DIR__ . '/../src/forgot-password_logic.php';
            require __DIR__ . '/../templates/forgot-password.phtml';
            break;
        case '/reset-password':
            $currentPage = 'reset-password';
            require __DIR__ . '/../src/reset-password_logic.php';
            require __DIR__ . '/../templates/reset-password.phtml';
            break;
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
        default:
            http_response_code(404);
            echo "<h1>404 - Page non trouvée</h1>";
            break;
    }
}