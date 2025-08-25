<?php

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/src/bootstrap.php';

$request_uri = strtok($_SERVER['REQUEST_URI'], '?');
$currentPage = '';

$routes = [
    '/' => ['view' => 'index.phtml', 'currentPage' => 'index'],
    '/lessons' => ['logic' => 'lessons-logic.php', 'view' => 'lessons.phtml', 'currentPage' => 'lessons'],
    '/challenges' => ['logic' => 'challenges-logic.php', 'view' => 'challenges.phtml', 'currentPage' => 'challenges'],
    '/login' => ['logic' => 'login-logic.php', 'view' => 'login.phtml', 'currentPage' => 'login'],
    '/register' => ['logic' => 'register-logic.php', 'view' => 'register.phtml', 'currentPage' => 'register'],
    '/forgot-password' => ['logic' => 'forgot-password-logic.php', 'view' => 'forgot-password.phtml', 'currentPage' => 'forgot-password'],
    '/reset-password' => ['logic' => 'reset-password-logic.php', 'view' => 'reset-password.phtml', 'currentPage' => 'reset-password'],
    '/logout' => ['logic' => 'logout-logic.php'],
    '/scenarios' => ['currentPage' => 'scenarios', 'content' => 'Page des scénarios en construction.'],
    '/api/verify-flag' => ['logic' => 'api/verifier-logic.php'],
    '/api/mark-lesson-complete' => ['logic' => 'api/mark-lesson-logic.php'],
    '/api/mark-challenge-complete' => ['logic' => 'api/mark-challenge-logic.php'],
    '/api/get-progress' => ['logic' => 'api/get-progress-logic.php']
];

if (preg_match('/^\/challenge\/(\d+)$/', $request_uri, $matches)) {
    require_page_login();
    $_GET['id'] = $matches[1];
    $currentPage = 'challenges';
    require ROOT_PATH . '/src/challenge-page-logic.php';
    require ROOT_PATH . '/templates/challenge-page.phtml';

} elseif (preg_match('/^\/lesson\/(\d+)$/', $request_uri, $matches)) {
    require_page_login();
    $_GET['id'] = $matches[1];
    $currentPage = 'lessons';
    require ROOT_PATH . '/src/lesson-page-logic.php';
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