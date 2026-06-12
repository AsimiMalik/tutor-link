<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../classes/Database.php';
$db = new Database();
$conn = $db->connect();

$booking_id = $_GET['id'] ?? null;
if (!$booking_id) {
    die('Booking not specified');
}

// verify ownership
$stmt = $conn->prepare('SELECT parent_id FROM bookings WHERE id = ? LIMIT 1');
$stmt->execute([$booking_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) die('Booking not found');
if ($row['parent_id'] != $_SESSION['user_id']) die('Not authorized');

$upd = $conn->prepare('UPDATE bookings SET status = ? WHERE id = ?');
$upd->execute(['cancelled', $booking_id]);

header('Location: ../parent/parent-bookings.php');
exit();
