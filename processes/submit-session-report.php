<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/SessionReport.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../tutor/tutor-dashboard.php');
    exit();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'tutor') {
    $_SESSION['error'] = 'Access denied.';
    header('Location: ../auth/login.php');
    exit();
}

if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) {
    $_SESSION['error'] = 'Invalid CSRF token.';
    header('Location: ../tutor/tutor-dashboard.php');
    exit();
}

$db = new Database();
$conn = $db->connect();
$report = new SessionReport($conn);
// prepare data
$data = [
    'booking_id' => !empty($_POST['booking_id']) ? (int)$_POST['booking_id'] : null,
    'tutor_id' => $_SESSION['user_id'],
    'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : 0,
    'topics' => trim($_POST['topics'] ?? ''),
    'duration_minutes' => (int)($_POST['duration_minutes'] ?? 0),
    'attendance' => in_array($_POST['attendance'] ?? 'present', ['present','late','absent']) ? $_POST['attendance'] : 'present',
    'homework' => trim($_POST['homework'] ?? ''),
    'rating' => !empty($_POST['rating']) ? (int)$_POST['rating'] : null,
];

// If booking_id provided but parent_id missing, resolve parent from bookings table
if (!empty($data['booking_id']) && empty($data['parent_id'])) {
    try {
        $bst = $conn->prepare('SELECT parent_id FROM bookings WHERE id = ? LIMIT 1');
        $bst->execute([$data['booking_id']]);
        $bro = $bst->fetch(PDO::FETCH_ASSOC);
        if ($bro && !empty($bro['parent_id'])) {
            $data['parent_id'] = (int)$bro['parent_id'];
        }
    } catch (Exception $e) {
        // ignore — will validate below
    }
}

// parent_id is required in table; ensure we have it
if (empty($data['parent_id'])) {
    $_SESSION['error'] = 'Parent could not be determined for this report. Please provide Parent ID or valid Booking ID.';
    header('Location: ../tutor/tutor-session-report.php');
    exit();
}

$ok = $report->create($data);
if ($ok) {
    // get inserted id
    $reportId = (int)$conn->lastInsertId();

    // create a notification for the parent
    try {
        require_once __DIR__ . '/../classes/Notification.php';
        $notif = new Notification($conn);
        $tutorName = $_SESSION['fullname'] ?? 'Your tutor';
        $message = "New session report submitted by {$tutorName}.";
        $notif->create($data['parent_id'], $_SESSION['user_id'], 'session_report', $reportId, 'session_reports', $message);
    } catch (Exception $e) {
        // ignore notification failures
    }

    $_SESSION['success'] = 'Session report submitted.';
} else {
    $_SESSION['error'] = 'Unable to save report.';
}

header('Location: ../tutor/tutor-dashboard.php');
exit();
