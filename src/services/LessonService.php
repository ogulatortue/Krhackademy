<?php
class LessonService {
    private $pdo;
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    public function findAllSorted(?int $userId = null): array {
        $sql = "SELECT l.id, l.lesson_id_str, l.title, l.category, l.description, l.difficulty, l.icon_class FROM lessons l ORDER BY l.category ASC, CASE l.difficulty WHEN 'Débutant' THEN 1 WHEN 'Initié' THEN 2 WHEN 'Avancé' THEN 3 WHEN 'Expert' THEN 4 ELSE 5 END ASC, l.id ASC";
        $lessons = $this->pdo->query($sql)->fetchAll();
        if ($userId && $lessons) {
            $stmt = $this->pdo->prepare("SELECT lesson_id FROM user_lessons_progress WHERE user_id = ?");
            $stmt->execute([$userId]);
            $completedIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($lessons as &$lesson) {
                $lesson['is_completed'] = in_array($lesson['id'], $completedIds);
            }
        }
        return $lessons;
    }
    public function findById(int $id, ?int $userId = null) {
        $stmt = $this->pdo->prepare("SELECT * FROM lessons WHERE id = ?");
        $stmt->execute([$id]);
        $lesson = $stmt->fetch();
        if ($lesson && $userId) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM user_lessons_progress WHERE user_id = ? AND lesson_id = ?");
            $stmt->execute([$userId, $id]);
            $lesson['is_completed'] = (bool) $stmt->fetchColumn();
        } else if ($lesson) {
            $lesson['is_completed'] = false;
        }
        return $lesson;
    }
}