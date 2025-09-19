<?php

require_once __DIR__ . '/../bootstrap.php';

require_page_login();

$userService = new User($pdo);
$currentUserId = $_SESSION['user_id'];

$profileUserId = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $profileUserId = (int)$_GET['id'];
} else {
    $profileUserId = $currentUserId;
}

$userProfile = $userService->getFullProfileData($profileUserId);
if (!$userProfile) {
    $_SESSION['flash_message'] = [
        'type'    => 'error',
        'title'   => 'Utilisateur introuvable',
        'message' => "Le profil que vous essayez de consulter n'existe pas."
    ];
    header('Location: /');
    exit();
}
