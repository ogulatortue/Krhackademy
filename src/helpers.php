<?php

function getDifficultyClass($value): string
{
    if (is_numeric($value)) {
        if ($value >= 250) { return 'difficulty-expert'; }
        if ($value >= 100) { return 'difficulty-hard'; }
        if ($value >= 50) { return 'difficulty-medium'; }
        return 'difficulty-easy';
    } else {
        switch (strtolower($value)) {
            case 'initié':  return 'difficulty-medium';
            case 'avancé':  return 'difficulty-hard';
            case 'expert':  return 'difficulty-expert';
            case 'débutant':
            default:        return 'difficulty-easy';
        }
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(): void
{
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Erreur : Jeton CSRF invalide.');
    }
}