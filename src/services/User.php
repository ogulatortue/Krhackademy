<?php // src/services/User.php

class User {
    private $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    // --- Méthodes de recherche ---

    public function findByUsernameOrEmail(string $identifier) {
        $sql = "SELECT * FROM users WHERE email = :identifier OR username = :identifier LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['identifier' => $identifier]);
        return $stmt->fetch();
    }

    public function findUserByResetToken(string $token) {
        $tokenHash = hash('sha256', $token);
        $sql = "SELECT * FROM users WHERE reset_token = :token_hash AND reset_token_expiry > NOW() LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token_hash' => $tokenHash]);
        return $stmt->fetch();
    }

    // --- Méthodes de modification ---

    public function createUser(string $username, string $email, string $password): bool {
        $passwordHash = password_hash($password, PASSWORD_ARGON2ID);
        $sql = "INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password_hash' => $passwordHash
        ]);
    }

    public function updatePassword(int $userId, string $newPassword): bool {
        $passwordHash = password_hash($newPassword, PASSWORD_ARGON2ID);
        $sql = "UPDATE users SET password_hash = :password_hash WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['password_hash' => $passwordHash, 'id' => $userId]);
    }

    public function setResetToken(int $userId, string $token): bool {
        $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes')); // Durée de vie plus courte est souvent mieux
        $tokenHash = hash('sha256', $token);

        $sql = "UPDATE users SET reset_token = :token, reset_token_expiry = :expires WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        // CORRECTION DU BUG ICI
        return $stmt->execute([
            'token' => $tokenHash,
            'expires' => $expiry, 
            'id' => $userId
        ]);
    }

    public function clearResetToken(int $userId): bool {
        $sql = "UPDATE users SET reset_token = NULL, reset_token_expiry = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $userId]);
    }
}