<?php
session_start();
session_unset();
session_destroy();
session_start();

$_SESSION['flash_message'] = [
    'type' => 'success',
    'title' => 'Déconnexion',
    'message' => 'Vous avez été déconnecté avec succès.'
];

header('Location: /');
exit();