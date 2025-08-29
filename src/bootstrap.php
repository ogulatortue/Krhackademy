<?php

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// 1. Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Chargement des dépendances et helpers
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/services/User.php';
require_once __DIR__ . '/services/MailerService.php';
require_once __DIR__ . '/services/ValidationService.php';


// 3. Configuration de l'environnement (.env)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// 4. Connexion à la base de données
$host = $_ENV['DB_HOST'];
$db_name = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$charset = $_ENV['DB_CHARSET'];

$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // En production, logguer l'erreur au lieu de l'afficher
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// 5. Fonctions utilitaires de contrôle d'accès
function require_login(): void {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Authentification requise.']);
        exit();
    }
}

function require_page_login(): void {
    if (!isset($_SESSION['user_id'])) {
        $redirectURL = urlencode($_SERVER['REQUEST_URI']);
        header('Location: /login?redirect_to=' . $redirectURL);
        exit();
    }
}