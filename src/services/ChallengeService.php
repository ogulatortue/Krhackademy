<?php

class ChallengeService {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findAllSorted(?int $userId = null): array {
        $sql = "SELECT c.id, c.challenge_id_str, c.title, c.category, c.points, c.icon_class FROM challenges c ORDER BY CASE WHEN c.category = 'Introduction' THEN 0 ELSE 1 END, c.category ASC, c.points ASC, c.id ASC";
        $challenges = $this->pdo->query($sql)->fetchAll();
        
        if ($userId && $challenges) {
            $stmt = $this->pdo->prepare("SELECT challenge_id FROM user_challenges_progress WHERE user_id = ?");
            $stmt->execute([$userId]);
            $completedIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($challenges as &$challenge) {
                $challenge['is_completed'] = in_array($challenge['id'], $completedIds);
            }
        }
        return $challenges;
    }

    public function findById(int $id, ?int $userId = null) {
        $stmt = $this->pdo->prepare("SELECT * FROM challenges WHERE id = ?");
        $stmt->execute([$id]);
        $challenge = $stmt->fetch();
        
        if ($challenge && $userId) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM user_challenges_progress WHERE user_id = ? AND challenge_id = ?");
            $stmt->execute([$userId, $id]);
            $challenge['is_completed'] = (bool) $stmt->fetchColumn();
        } else if ($challenge) {
            $challenge['is_completed'] = false;
        }
        
        return $challenge;
    }
    
    public function findBySlug(string $slug, ?int $userId = null) {
        $stmt = $this->pdo->prepare("SELECT * FROM challenges WHERE challenge_id_str = ?");
        $stmt->execute([$slug]);
        $challenge = $stmt->fetch();
        
        if ($challenge && $userId) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM user_challenges_progress WHERE user_id = ? AND challenge_id = ?");
            $stmt->execute([$userId, $challenge['id']]);
            $challenge['is_completed'] = (bool) $stmt->fetchColumn();
        } else if ($challenge) {
            $challenge['is_completed'] = false;
        }
        
        return $challenge;
    }

    public function getFlag(int $challengeId): ?string {
        $stmt = $this->pdo->prepare("SELECT flag FROM challenges WHERE id = ?");
        $stmt->execute([$challengeId]);
        return $stmt->fetchColumn();
    }

    public function hasCompletedChallenge(int $userId, int $challengeId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM user_challenges_progress WHERE user_id = :user_id AND challenge_id = :challenge_id LIMIT 1'
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':challenge_id' => $challengeId
        ]);
        return (bool) $stmt->fetchColumn();
    }


    public function markAsComplete(int $userId, int $challengeId): bool {
        $sql = "INSERT INTO user_challenges_progress (user_id, challenge_id, completed_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE completed_at = NOW()";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$userId, $challengeId]);
    }

    public function markAsIncomplete(int $userId, int $challengeId): bool {
        $sql = "DELETE FROM user_challenges_progress WHERE user_id = ? AND challenge_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$userId, $challengeId]);
    }

    public function findCompletedIdsForUser(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT challenge_id FROM user_challenges_progress WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}