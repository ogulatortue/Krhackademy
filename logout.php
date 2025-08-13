<?php
// logout.php
session_start();

// Détruit toutes les variables de session
$_SESSION = array();

// Détruit la session
session_destroy();

// Redirige vers la page d'accueil
header("location: index.html");
exit;
?>