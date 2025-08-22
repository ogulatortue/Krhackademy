<?php
// src/bootstrap.php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
// Démarrer la session au tout début
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Charger l'autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Charger les variables d'environnement depuis le fichier .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Connexion à la base de données
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
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

/**
 * Pour les API : Vérifie si l'utilisateur est connecté.
 * Si non, arrête le script et renvoie une erreur JSON.
 */
function require_login(): void {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401); // Unauthorized
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Authentification requise.']);
        exit();
    }
}

/**
 * Pour les Pages : Vérifie si l'utilisateur est connecté.
 * Si non, le redirige vers la page de connexion en mémorisant la page de destination.
 */
function require_page_login(): void {
    if (!isset($_SESSION['user_id'])) {
        // On mémorise l'URL que l'utilisateur essayait d'atteindre
        $redirectURL = urlencode($_SERVER['REQUEST_URI']);
        
        // On redirige vers la page de connexion avec l'URL en paramètre
        header('Location: /login?redirect_to=' . $redirectURL);
        exit();
    }
}