<?php
require_once __DIR__ . '/Database.php';

class Message {
    private $conn;
    public function __construct($pdo=null){
        if ($pdo) $this->conn = $pdo;
        else { $db = new Database(); $this->conn = $db->connect(); }
    }

    public function send(int $sender_id,int $receiver_id,string $subject,string $body, $booking_id = null){
        $stmt = $this->conn->prepare("INSERT INTO messages (sender_id, receiver_id, booking_id, subject, body) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$sender_id,$receiver_id,$booking_id,$subject,$body]);
    }

    public function getInbox(int $user_id){
        $stmt = $this->conn->prepare("SELECT m.*, u.fullname AS sender_name FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.receiver_id = ? AND m.deleted_by_receiver = 0 ORDER BY m.created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSent(int $user_id){
        $stmt = $this->conn->prepare("SELECT m.*, u.fullname AS receiver_name FROM messages m JOIN users u ON m.receiver_id = u.id WHERE m.sender_id = ? AND m.deleted_by_sender = 0 ORDER BY m.created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id,int $user_id){
        $stmt = $this->conn->prepare("SELECT m.* FROM messages m WHERE m.id = ? AND (m.receiver_id = ? OR m.sender_id = ?) LIMIT 1");
        $stmt->execute([$id,$user_id,$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function markRead(int $id,int $user_id){
        $stmt = $this->conn->prepare("UPDATE messages SET is_read = 1 WHERE id = ? AND receiver_id = ?");
        $stmt->execute([$id,$user_id]);
        return $stmt->rowCount() > 0;
    }

    // expose PDO error info in a safe way for callers
    public function getErrorInfo(){
        if (!$this->conn) return null;
        try {
            return $this->conn->errorInfo();
        } catch (Exception $e) {
            return null;
        }
    }
}
