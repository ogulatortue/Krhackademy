<?php

// 1. Charge l'autoloader de Composer pour utiliser les librairies
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Charge les variables du fichier .env dans l'environnement
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// 3. Utilise les variables sécurisées de l'environnement ($_ENV)
$host = $_ENV['DB_HOST'];
$db_name = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS']; // <- Le mot de passe est maintenant lu ici
$charset = $_ENV['DB_CHARSET'];

// Le reste de votre code de connexion reste le même
$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>