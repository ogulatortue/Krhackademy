<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/src/bootstrap.php';

$request_uri = strtok($_SERVER['REQUEST_URI'], '?');
if (strlen($request_uri) > 1) {
    $request_uri = rtrim($request_uri, '/');
}
$currentPage = '';
$routes = [
    '/' => ['view' => 'index.phtml', 'currentPage' => 'index'],
    '/lessons' => ['logic' => 'lessons-logic.php', 'view' => 'lessons.phtml', 'currentPage' => 'lessons'],
    '/lesson/{id}' => ['logic' => 'lesson-page-logic.php', 'view' => 'lesson-page.phtml', 'currentPage' => 'lessons', 'auth' => true],
    '/challenges' => ['logic' => 'challenges-logic.php', 'view' => 'challenges.phtml', 'currentPage' => 'challenges'],
    '/challenge/{id}' => ['logic' => 'challenge-page-logic.php', 'view' => 'challenge-page.phtml', 'currentPage' => 'challenges', 'auth' => true],
    '/scenarios' => ['logic' => 'scenarios-logic.php', 'view' => 'scenarios.phtml', 'currentPage' => 'scenarios'],
    '/leaderboard' => ['logic' => 'leaderboard-logic.php', 'view' => 'leaderboard.phtml', 'currentPage' => 'leaderboard'],
    '/login' => ['logic' => 'login-logic.php', 'view' => 'login.phtml'],
    '/register' => ['logic' => 'register-logic.php', 'view' => 'register.phtml'],
    '/forgot-password' => ['logic' => 'forgot-password-logic.php', 'view' => 'forgot-password.phtml'],
    '/reset-password' => ['logic' => 'reset-password-logic.php', 'view' => 'reset-password.phtml'],
    '/logout' => ['logic' => 'logout-logic.php'],
    '/api/verify-flag' => ['logic' => 'api/verifier-logic.php'],
    '/api/mark-lesson-complete' => ['logic' => 'api/mark-lesson-logic.php'],
    '/api/mark-lesson-incomplete' => ['logic' => 'api/mark-lesson-incomplete-logic.php'],
    '/api/mark-challenge-incomplete' => ['logic' => 'api/mark-challenge-incomplete-logic.php'],
    '/api/get-progress' => ['logic' => 'api/get-progress-logic.php'],
];
$routeFound = false;
foreach ($routes as $route => $config) {
    $pattern = preg_replace('/\{id\}/', '(\d+)', $route);
    if (preg_match('#^' . $pattern . '$#', $request_uri, $matches)) {
        if (!empty($config['auth'])) {
            require_page_login();
        }
        if (isset($matches[1])) {
            $_GET['id'] = $matches[1];
        }
        $currentPage = $config['currentPage'] ?? '';
        if (isset($config['logic'])) {
            require ROOT_PATH . '/src/' . $config['logic'];
        }
        if (isset($config['view'])) {
            require ROOT_PATH . '/templates/' . $config['view'];
        }
        $routeFound = true;
        break;
    }
}
if (!$routeFound) {
    http_response_code(404);
    echo "<h1>404 - Page non trouvée</h1>";
}