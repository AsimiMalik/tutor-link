<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/SessionReport.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../tutor/tutor-bookings.php'); exit();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'tutor') {
    $_SESSION['flash_error'] = 'Access denied.';
    header('Location: ../auth/login.php'); exit();
}

if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) {
    $_SESSION['flash_error'] = 'Invalid CSRF token.';
    header('Location: ../tutor/tutor-bookings.php'); exit();
}

$booking_id = !empty($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
$attendance = in_array($_POST['attendance'] ?? '', ['present','late','absent']) ? $_POST['attendance'] : null;

if (!$booking_id || !$attendance) {
    $_SESSION['flash_error'] = 'Invalid attendance data.';
    header('Location: ../tutor/tutor-bookings.php'); exit();
}

$db = new Database(); $conn = $db->connect();
$sr = new SessionReport($conn);

// check if a session_report already exists for this booking
$existing = $sr->getByBooking($booking_id);

if ($existing) {
    $ok = $sr->updateByBooking($booking_id, ['attendance' => $attendance, 'tutor_id' => $_SESSION['user_id']]);
} else {
    // try to resolve parent_id from booking
    $bst = $conn->prepare('SELECT parent_id FROM bookings WHERE id = ? LIMIT 1');
    $bst->execute([$booking_id]);
    $bro = $bst->fetch(PDO::FETCH_ASSOC);
    $parent_id = $bro['parent_id'] ?? 0;
    $ok = $sr->create([
        'booking_id' => $booking_id,
        'tutor_id' => $_SESSION['user_id'],
        'parent_id' => $parent_id,
        'attendance' => $attendance,
    ]);
}

if ($ok) {
    $_SESSION['flash_success'] = 'Attendance recorded.';
} else {
    $_SESSION['flash_error'] = 'Unable to save attendance.';
}

header('Location: ../tutor/tutor-bookings.php');
exit();
