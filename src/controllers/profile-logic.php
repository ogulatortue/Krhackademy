<?php

require_once __DIR__ . '/../bootstrap.php';

require_page_login();

$userService = new User($pdo);
$currentUserId = $_SESSION['user_id'];

$userProfile = $userService->getFullProfileData($currentUserId);

if (!$userProfile) {
    header('Location: /logout');
    exit();
}