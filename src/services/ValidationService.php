<?php

class ValidationService {
    public static function validateRegistration(array $data): array {
        $errors = [];
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            $errors[] = 'Veuillez remplir tous les champs.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Le format de l\'adresse e-mail est invalide.';
        }
        if (strlen($data['password']) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }
        return $errors;
    }

    public static function validatePasswordReset(array $data): array {
        $errors = [];
        if (empty($data['password']) || empty($data['password_confirm'])) {
            $errors[] = 'Veuillez remplir tous les champs.';
        }
        if (strlen($data['password']) < 8) {
            $errors[] = 'Le mot de passe doit faire au moins 8 caractères.';
        }
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }
        return $errors;
    }
}