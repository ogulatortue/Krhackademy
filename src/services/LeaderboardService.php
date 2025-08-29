<?php
class LeaderboardService {
    private $pdo;
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    public function getWidgetData(?int $currentUserId): array {
        $sql = "
            SELECT u.id, u.username, u.avatar_url, COALESCE(SUM(c.points), 0) AS score
            FROM users u
            LEFT JOIN user_challenges_progress ucp ON u.id = ucp.user_id
            LEFT JOIN challenges c ON ucp.challenge_id = c.id
            GROUP BY u.id, u.username, u.avatar_url
            ORDER BY score DESC, MAX(ucp.completed_at) ASC
        ";
        $fullLeaderboard = $this->pdo->query($sql)->fetchAll();
        $topUsers = array_slice($fullLeaderboard, 0, 3);
        $currentUserData = null;
        if ($currentUserId !== null) {
            foreach ($fullLeaderboard as $index => $user) {
                if ($user['id'] == $currentUserId) {
                    $currentUserData = $user;
                    $currentUserData['rank'] = $index + 1;
                    break;
                }
            }
        }
        $isCurrentUserInTop3 = false;
        if ($currentUserData) {
            foreach ($topUsers as $topUser) {
                if ($topUser['id'] == $currentUserId) {
                    $isCurrentUserInTop3 = true;
                    break;
                }
            }
        }
        return [
            'topUsers' => $topUsers,
            'currentUser' => $currentUserData,
            'isCurrentUserInTop3' => $isCurrentUserInTop3,
        ];
    }
}