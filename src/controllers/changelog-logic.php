<?php

$changelogPath = ROOT_PATH . '/data/changelog.json';
$changelogEntries = [];
$error = '';

if (file_exists($changelogPath)) {
    $jsonContent = file_get_contents($changelogPath);
    $changelogData = json_decode($jsonContent, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $changelogEntries = $changelogData;
    } else {
        $error = "Erreur lors de la lecture des données du changelog (JSON invalide).";
    }
} else {
    $error = "Le fichier de changelog est introuvable.";
}

$pageTitle = 'Changelog - Kr[HACK]ademy';
$specific_css = ['/css/menus.css', '/css/changelog.css','/css/header-widgets.css'];
$currentPage = 'changelog';