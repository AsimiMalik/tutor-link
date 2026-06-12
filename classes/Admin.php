<?php

class Admin {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /* =========================
        TUTOR MANAGEMENT
    ========================= */

    // Get all tutors
    public function getAllTutors() {
        $sql = "SELECT * FROM users WHERE role='tutor'";
        return $this->conn->query($sql);
    }

    // Get tutors by status
    public function getTutorsByStatus($status) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE role='tutor' AND status=?");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Approve tutor
    public function approveTutor($id) {
        $stmt = $this->conn->prepare("UPDATE users SET status='approved' WHERE id=? AND role='tutor'");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Suspend tutor
    public function suspendTutor($id) {
        $stmt = $this->conn->prepare("UPDATE users SET status='suspended' WHERE id=? AND role='tutor'");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Delete tutor
    public function deleteTutor($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id=? AND role='tutor'");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* =========================
        BOOKING MANAGEMENT
    ========================= */

    public function getAllBookings() {
        $sql = "SELECT * FROM bookings ORDER BY id DESC";
        return $this->conn->query($sql);
    }

    public function cancelBooking($id) {
        $stmt = $this->conn->prepare("UPDATE bookings SET status='cancelled' WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function completeBooking($id) {
        $stmt = $this->conn->prepare("UPDATE bookings SET status='completed' WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* =========================
        REVIEW MANAGEMENT
    ========================= */

    public function getAllReviews() {
        $sql = "SELECT * FROM reviews ORDER BY id DESC";
        return $this->conn->query($sql);
    }

    public function deleteReview($id) {
        $stmt = $this->conn->prepare("DELETE FROM reviews WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* =========================
        COMPLAINT MANAGEMENT
    ========================= */

    public function getAllComplaints() {
        $sql = "SELECT * FROM complaints ORDER BY id DESC";
        return $this->conn->query($sql);
    }

    public function resolveComplaint($id) {
        $stmt = $this->conn->prepare("UPDATE complaints SET status='resolved' WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function dismissComplaint($id) {
        $stmt = $this->conn->prepare("UPDATE complaints SET status='dismissed' WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* =========================
        DASHBOARD STATS
    ========================= */

    public function count($table, $where = "") {
        $sql = "SELECT COUNT(*) as total FROM $table $where";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['total'];
    }
}