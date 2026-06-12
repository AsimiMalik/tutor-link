<?php
session_start();
require_once "../classes/Database.php";

$db = new Database();
$conn = $db->connect();

/* Protect admin route */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

/* =========================
   DASHBOARD STATS QUERIES
========================= */

// Total tutors
$tutors = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='tutor'");
$total_tutors = $tutors->fetch_assoc()['total'];

// Pending tutors
$pending = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='tutor' AND status='pending'");
$pending_tutors = $pending->fetch_assoc()['total'];

// Approved tutors
$approved = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='tutor' AND status='approved'");
$approved_tutors = $approved->fetch_assoc()['total'];

// Suspended tutors
$suspended = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='tutor' AND status='suspended'");
$suspended_tutors = $suspended->fetch_assoc()['total'];

// Bookings
$bookings = $conn->query("SELECT COUNT(*) AS total FROM bookings");
$total_bookings = $bookings->fetch_assoc()['total'];

// Reviews
$reviews = $conn->query("SELECT COUNT(*) AS total FROM reviews");
$total_reviews = $reviews->fetch_assoc()['total'];

// Complaints
$complaints = $conn->query("SELECT COUNT(*) AS total FROM complaints");
$total_complaints = $complaints->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Tutor My Ward</title>

    <link rel="stylesheet" href="includes/admin.css">
</head>
<body>

<?php include "includes/sidebar.php"; ?>

<div class="main">

    <?php include "includes/header.php"; ?>

    <h2>Dashboard Overview</h2>

    <div class="grid">

        <div class="card">
            <h3>Total Tutors</h3>
            <p><?= $total_tutors ?></p>
        </div>

        <div class="card">
            <h3>Pending Tutors</h3>
            <p><?= $pending_tutors ?></p>
        </div>

        <div class="card">
            <h3>Approved Tutors</h3>
            <p><?= $approved_tutors ?></p>
        </div>

        <div class="card">
            <h3>Suspended Tutors</h3>
            <p><?= $suspended_tutors ?></p>
        </div>

        <div class="card">
            <h3>Total Bookings</h3>
            <p><?= $total_bookings ?></p>
        </div>

        <div class="card">
            <h3>Total Reviews</h3>
            <p><?= $total_reviews ?></p>
        </div>

        <div class="card">
            <h3>Complaints</h3>
            <p><?= $total_complaints ?></p>
        </div>

    </div>

</div>

</body>
</html>