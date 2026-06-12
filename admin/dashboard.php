<?php
session_start();

require_once "../classes/Database.php";
require_once "../classes/Admin.php";

$db = new Database();
$conn = $db->connect();

/* =========================
   ADMIN AUTH CHECK
========================= */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

/* =========================
   LOAD ADMIN CLASS
========================= */
$admin = new Admin($conn);

/* =========================
   DASHBOARD STATS (USING CLASS)
========================= */
$total_tutors = $admin->count("users", "WHERE role='tutor'");
$pending_tutors = $admin->count("users", "WHERE role='tutor' AND status='pending'");
$approved_tutors = $admin->count("users", "WHERE role='tutor' AND status='approved'");
$suspended_tutors = $admin->count("users", "WHERE role='tutor' AND status='suspended'");

$total_bookings = $admin->count("bookings");
$total_reviews = $admin->count("reviews");
$total_complaints = $admin->count("complaints");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Tutor My Ward</title>

    <link rel="stylesheet" href="includes/admin.css">
</head>
<body>

<!-- SIDEBAR -->
<?php include "includes/sidebar.php"; ?>

<div class="main">

    <!-- HEADER -->
    <?php include "includes/header.php"; ?>

    <h2>Dashboard Overview</h2>
    <p>Welcome Admin 👋 Manage tutors, bookings, reviews and complaints here.</p>

    <!-- STATS GRID -->
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
            <h3>Total Complaints</h3>
            <p><?= $total_complaints ?></p>
        </div>

    </div>

    <!-- QUICK ACTIONS -->
    <div style="margin-top: 30px;">
        <h3>Quick Actions</h3>

        <a href="tutors.php">Manage Tutors</a> |
        <a href="bookings.php">Manage Bookings</a> |
        <a href="reviews.php">Manage Reviews</a> |
        <a href="complaints.php">View Complaints</a>
    </div>

</div>

</body>
</html>