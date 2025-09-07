<?php

class User {
    private $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    public function findById(int $id) {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function findByUsernameOrEmail(string $identifier) {
        $sql = "SELECT * FROM users WHERE email = :email OR username = :username LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'email' => $identifier, 
            'username' => $identifier
        ]);
        return $stmt->fetch();
    }

    public function findUserByResetToken(string $token) {
        $tokenHash = hash('sha256', $token);
        $sql = "SELECT * FROM users WHERE reset_token = :token_hash AND reset_token_expiry > NOW() LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token_hash' => $tokenHash]);
        return $stmt->fetch();
    }

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

    public function updateAccountInfo(int $userId, string $username, string $email): bool {
        $sql = "UPDATE users SET username = :username, email = :email WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'username' => $username,
            'email' => $email,
            'id' => $userId
        ]);
    }

    public function updatePassword(int $userId, string $newPassword): bool {
        $passwordHash = password_hash($newPassword, PASSWORD_ARGON2ID);
        $sql = "UPDATE users SET password_hash = :password_hash WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['password_hash' => $passwordHash, 'id' => $userId]);
    }

    public function deleteUser(int $userId): bool {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $userId]);
    }

    public function setResetToken(int $userId, string $token): bool {
        $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $tokenHash = hash('sha256', $token);
        $sql = "UPDATE users SET reset_token = :token, reset_token_expiry = :expires WHERE id = :id";
        $stmt = $this->db->prepare($sql);
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

    public function getFullProfileData(int $userId) {
        $sql = "
            SELECT
                u.username,
                u.avatar_url,
                u.avatar_bg_color,
                u.banner_url,
                u.banner_bg_color,
                u.bio,
                u.created_at AS registration_date,
                COALESCE(SUM(c.points), 0) AS total_score,
                (SELECT COUNT(*) FROM user_challenges_progress WHERE user_id = u.id) AS challenges_completed,
                (SELECT COUNT(*) FROM user_lessons_progress WHERE user_id = u.id) AS lessons_completed
            FROM
                users u
            LEFT JOIN
                user_challenges_progress ucp ON u.id = ucp.user_id
            LEFT JOIN
                challenges c ON ucp.challenge_id = c.id
            WHERE
                u.id = :user_id
            GROUP BY
                u.id;
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch();
    }
    public function updateProfileCustomization(int $userId, ?string $bio, ?string $bannerUrl, ?string $avatarUrl): bool {
        if ($bio === null && $bannerUrl === null && $avatarUrl === null) {
            return true;
        }

        $fields = [];
        $params = [];

        if ($bio !== null) {
            $fields[] = "bio = :bio";
            $params[':bio'] = $bio;
        }

        if ($bannerUrl !== null) {
            $fields[] = "banner_url = :banner_url";
            $params[':banner_url'] = $bannerUrl;
        }
        
        if ($avatarUrl !== null) {
            $fields[] = "avatar_url = :avatar_url";
            $params[':avatar_url'] = $avatarUrl;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $params[':id'] = $userId;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}