<?php

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/src/bootstrap.php';

$request_uri = strtok($_SERVER['REQUEST_URI'], '?');
$currentPage = '';

$routes = [
    '/' => ['view' => 'index.phtml'],
    '/lessons' => ['logic' => 'lessons_logic.php', 'view' => 'lessons.phtml', 'currentPage' => 'lessons'],
    '/challenges' => ['logic' => 'challenges_logic.php', 'view' => 'challenges.phtml', 'currentPage' => 'challenges'],
    '/login' => ['logic' => 'login_logic.php', 'view' => 'login.phtml', 'currentPage' => 'login'],
    '/register' => ['logic' => 'register_logic.php', 'view' => 'register.phtml', 'currentPage' => 'register'],
    '/forgot-password' => ['logic' => 'forgot-password_logic.php', 'view' => 'forgot-password.phtml', 'currentPage' => 'forgot-password'],
    '/reset-password' => ['logic' => 'reset-password_logic.php', 'view' => 'reset-password.phtml', 'currentPage' => 'reset-password'],
    '/logout' => ['logic' => 'logout.php'],
    '/scenarios' => ['currentPage' => 'scenarios', 'content' => 'Page des scénarios en construction.'],
    '/api/verify-flag' => ['logic' => 'api/verifier_logic.php'],
    '/api/mark-lesson-complete' => ['logic' => 'api/mark_lesson_logic.php'],
    '/api/mark-challenge-complete' => ['logic' => 'api/mark_challenge_logic.php'],
    '/api/get-progress' => ['logic' => 'api/get_progress_logic.php']
];

if (preg_match('/^\/challenge\/(\d+)$/', $request_uri, $matches)) {
    require_page_login();
    $_GET['id'] = $matches[1];
    $currentPage = 'challenges';
    require ROOT_PATH . '/src/challenge_page_logic.php';
    require ROOT_PATH . '/templates/challenge-page.phtml';

} elseif (preg_match('/^\/lesson\/(\d+)$/', $request_uri, $matches)) {
    require_page_login();
    $_GET['id'] = $matches[1];
    $currentPage = 'lessons';
    require ROOT_PATH . '/src/lesson_page_logic.php';
    require ROOT_PATH . '/templates/lesson-page.phtml';

} elseif (isset($routes[$request_uri])) {
    $route = $routes[$request_uri];
    $currentPage = $route['currentPage'] ?? '';

    if (isset($route['logic'])) {
        require ROOT_PATH . '/src/' . $route['logic'];
    }
    if (isset($route['view'])) {
        require ROOT_PATH . '/templates/' . $route['view'];
    }
    if (isset($route['content'])) {
        echo $route['content'];
    }

} else {
    http_response_code(404);
    echo "<h1>404 - Page non trouvée</h1>";
}