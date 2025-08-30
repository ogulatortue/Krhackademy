<?php

session_start();

$_SESSION['flash_message'] = [
    'type' => 'success',
    'title' => 'Déconnexion',
    'message' => 'Vous avez été déconnecté avec succès.'
];

unset($_SESSION['user_id']);
unset($_SESSION['username']);

session_regenerate_id(true);

header('Location: /');
exit();