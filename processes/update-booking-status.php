<?php
session_start();

require_once __DIR__ . '/../classes/Database.php';

$db = new Database();
$conn = $db->connect();

require_once __DIR__ . '/../includes/csrf.php';

if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('Invalid CSRF token');
}

// ensure tutor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied');
}

$tutor_id = $_SESSION['user_id'];

// validate input
$booking_id = $_POST['booking_id'] ?? null;
// expected actions: 'accept', 'decline', 'toggle', 'cancel'
$action = $_POST['action'] ?? null;

if (!$booking_id || !$action) {
    header('Location: ../tutor/tutor-bookings.php');
    exit();
}

// map action to status string
if (in_array($action, ['accept','decline','toggle','cancel','complete'], true) === false) {
    header('Location: ../tutor/tutor-bookings.php');
    exit();
}

// ensure booking belongs to this tutor
// fetch booking to validate tutor ownership and current status
$stmt = $conn->prepare("SELECT tutor_id, status FROM bookings WHERE id = ?");
$stmt->execute([$booking_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row || (int)$row['tutor_id'] !== (int)$tutor_id) {
    header('HTTP/1.1 403 Forbidden');
    exit('Invalid booking');
}

// determine new status based on action
$currentStatus = isset($row['status']) ? trim(strtolower($row['status'])) : 'pending';
$newStatus = null;
if ($action === 'accept') {
    $newStatus = 'accepted';
} elseif ($action === 'decline') {
    $newStatus = 'declined';
} elseif ($action === 'toggle') {
    if ($currentStatus === 'accepted') $newStatus = 'declined';
    elseif ($currentStatus === 'declined') $newStatus = 'accepted';
    else $newStatus = 'accepted';
} elseif ($action === 'cancel') {
    $newStatus = 'cancelled';
} elseif ($action === 'complete' || $action === 'completed') {
    $newStatus = 'completed';
}

if ($newStatus !== null) {
    try {
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $booking_id]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['flash_success'] = 'Booking status updated to ' . strtoupper($newStatus) . '.';
        } else {
            $_SESSION['flash_error'] = 'No change made — booking may already have that status.';
        }
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Error updating booking: ' . $e->getMessage();
    }
}

// redirect back to tutor bookings
header('Location: ../tutor/tutor-bookings.php');
exit();
