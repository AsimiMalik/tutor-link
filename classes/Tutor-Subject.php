<?php

require_once __DIR__ . '/Database.php';

class TutorSubject {
    private $conn;

    public function __construct($pdo = null) {
        if ($pdo) {
            $this->conn = $pdo;
        } else {
            $db = new Database();
            $this->conn = $db->connect();
        }
    }

    public function getSubjectsForTutor(int $tutor_id): array {
        $stmt = $this->conn->prepare("SELECT s.* FROM tutor_subjects ts JOIN subjects s ON ts.subject_id = s.id WHERE ts.tutor_id = ? ORDER BY s.name");
        $stmt->execute([$tutor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubjectIdsForTutor(int $tutor_id): array {
        $stmt = $this->conn->prepare("SELECT subject_id FROM tutor_subjects WHERE tutor_id = ?");
        $stmt->execute([$tutor_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($r){return (int)$r['subject_id'];}, $rows);
    }

    public function assignSubjects(int $tutor_id, array $subject_ids): bool {
        try {
            $this->conn->beginTransaction();
            $del = $this->conn->prepare("DELETE FROM tutor_subjects WHERE tutor_id = ?");
            $del->execute([$tutor_id]);

            if (!empty($subject_ids)) {
                $ins = $this->conn->prepare("INSERT INTO tutor_subjects (tutor_id, subject_id) VALUES (?, ?)");
                foreach ($subject_ids as $sid) {
                    $sid = (int)$sid;
                    if ($sid <= 0) continue;
                    $ins->execute([$tutor_id, $sid]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function clearSubjects(int $tutor_id): bool {
        $stmt = $this->conn->prepare("DELETE FROM tutor_subjects WHERE tutor_id = ?");
        return $stmt->execute([$tutor_id]);
    }
}
