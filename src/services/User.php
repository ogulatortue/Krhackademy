<?php

class User {
    
    private $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    public function findByUsernameOrEmail(string $email) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function setResetToken(int $userId, string $token): bool {
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $tokenHash = hash('sha256', $token);

        $sql = "UPDATE users SET reset_token = :token, reset_token_expiry = :expires WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':token', $tokenHash);
        $stmt->bindValue(':expires', $expiry);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function findUserByResetToken(string $token) {
        $tokenHash = hash('sha256', $token);

        $sql = "SELECT * FROM users WHERE reset_token = :token_hash AND reset_token_expiry > NOW() LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token_hash' => $tokenHash]);
        return $stmt->fetch();
    }

    public function updatePassword(int $userId, string $newPassword): bool {
        $passwordHash = password_hash($newPassword, PASSWORD_ARGON2ID);

        $sql = "UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':password', $passwordHash);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}