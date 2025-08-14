<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $redirect_url = $_SERVER['REQUEST_URI'];
    header('Location: login.php?redirect_url=' . urlencode($redirect_url));
    exit();
}

$current_user_id = $_SESSION['user_id'];
?>