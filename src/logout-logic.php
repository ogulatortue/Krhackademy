<?php
// src/logout.php
session_start();
session_unset();
session_destroy();
header('Location: /'); // Redirige vers la page d'accueil
exit();