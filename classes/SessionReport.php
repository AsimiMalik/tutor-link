<?php
class SessionReport {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create(array $data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO session_reports (booking_id, tutor_id, parent_id, topics, duration_minutes, attendance, homework, rating) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['booking_id'] ?? null,
            $data['tutor_id'],
            $data['parent_id'],
            $data['topics'] ?? null,
            $data['duration_minutes'] ?? 0,
            $data['attendance'] ?? 'present',
            $data['homework'] ?? null,
            $data['rating'] ?? null,
        ]);
    }

    public function getByBooking($booking_id) {
        $stmt = $this->conn->prepare("SELECT sr.*, u.fullname AS tutor_name FROM session_reports sr JOIN users u ON sr.tutor_id = u.id WHERE sr.booking_id = ? LIMIT 1");
        $stmt->execute([$booking_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateByBooking($booking_id, array $data) {
        // build dynamic set
        $sets = [];
        $params = [];
        foreach (['tutor_id','parent_id','topics','duration_minutes','attendance','homework','rating'] as $col) {
            if (array_key_exists($col, $data)) {
                $sets[] = "$col = ?";
                $params[] = $data[$col];
            }
        }
        if (empty($sets)) return false;
        $params[] = $booking_id;
        $sql = "UPDATE session_reports SET " . implode(', ', $sets) . " WHERE booking_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function getByParent($parent_id) {
        $stmt = $this->conn->prepare("SELECT sr.*, u.fullname AS tutor_name FROM session_reports sr JOIN users u ON sr.tutor_id = u.id WHERE sr.parent_id = ? ORDER BY sr.created_at DESC");
        $stmt->execute([$parent_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByTutor($tutor_id) {
        $stmt = $this->conn->prepare("SELECT sr.*, u.fullname AS parent_name FROM session_reports sr JOIN users u ON sr.parent_id = u.id WHERE sr.tutor_id = ? ORDER BY sr.created_at DESC");
        $stmt->execute([$tutor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
