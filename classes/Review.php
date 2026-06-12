<?php
require_once __DIR__ . '/Database.php';

class Review {
    private $conn;
    public function __construct($pdo=null){
        if ($pdo) $this->conn = $pdo;
        else { $db = new Database(); $this->conn = $db->connect(); }
    }

    public function submit(int $reviewer_id,int $reviewee_id,int $rating,string $title = null,string $body = null, $booking_id = null){
        $stmt = $this->conn->prepare("INSERT INTO reviews (booking_id, reviewer_id, reviewee_id, rating, title, body, visibility) VALUES (?, ?, ?, ?, ?, ?, 'visible')");
        return $stmt->execute([$booking_id,$reviewer_id,$reviewee_id,$rating,$title,$body]);
    }

    public function getForUser(int $user_id){
        $stmt = $this->conn->prepare("SELECT r.*, u.fullname AS reviewer_name FROM reviews r JOIN users u ON r.reviewer_id = u.id WHERE r.reviewee_id = ? AND r.visibility = 'visible' ORDER BY r.created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAverage(int $user_id){
        $stmt = $this->conn->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total FROM reviews WHERE reviewee_id = ? AND visibility = 'visible'");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
