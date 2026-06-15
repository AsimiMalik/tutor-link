<?php
class Notification {
    private $conn;
    public function __construct($pdo){
        $this->conn = $pdo;
    }

    public function create($user_id, $actor_id, $type, $reference_id = null, $reference_table = null, $message = null){
        $stmt = $this->conn->prepare("INSERT INTO notifications (user_id, actor_id, type, reference_id, reference_table, message) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$user_id, $actor_id, $type, $reference_id, $reference_table, $message]);
    }
}
