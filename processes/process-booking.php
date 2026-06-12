<?php
session_start();

/*
----------------------------------------------------
CONNECT TO DATABASE
----------------------------------------------------
*/
require_once "../classes/Database.php";

require_once __DIR__ . '/../includes/csrf.php';

$db = new Database();
$conn = $db->connect();

/*
----------------------------------------------------
CHECK IF FORM WAS SUBMITTED
----------------------------------------------------
If user didn't click the booking button, redirect away
----------------------------------------------------
*/
if (!isset($_POST['book'])) {
    header("Location: ../bookings/book-tutor.php");
    exit();
}

if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) {
    die('Invalid CSRF token.');
}

/*
----------------------------------------------------
CHECK IF USER IS LOGGED IN
Only logged-in users can book tutors
----------------------------------------------------
*/
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

/*
----------------------------------------------------
CHECK USER ROLE
Only PARENTS are allowed to book tutors
----------------------------------------------------
*/
if ($_SESSION['role'] !== 'parent') {
    die("Only parents can make bookings.");
}

/*
----------------------------------------------------
COLLECT FORM DATA
----------------------------------------------------
*/
$parent_id = $_SESSION['user_id'];
$tutor_id = $_POST['tutor_id'] ?? null;
$subject_id = $_POST['subject_id'] ?? null;
$session_date = $_POST['session_date'] ?? null;

/*
----------------------------------------------------
VALIDATE INPUT
Make sure nothing is empty
----------------------------------------------------
*/
if (!$tutor_id || !$subject_id || !$session_date) {
    die("All fields are required.");
}

/*
----------------------------------------------------
CHECK IF TUTOR EXISTS IN DATABASE
Prevents fake tutor IDs
----------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT id 
    FROM users 
    WHERE id = ? AND role = 'tutor'
");
$stmt->execute([$tutor_id]);

if (!$stmt->fetch()) {
    die("Invalid tutor selected.");
}

/*
----------------------------------------------------
CHECK IF SUBJECT EXISTS
Prevents invalid subject IDs
----------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT id 
    FROM subjects 
    WHERE id = ?
");
$stmt->execute([$subject_id]);

if (!$stmt->fetch()) {
    die("Invalid subject selected.");
}

/*
----------------------------------------------------
INSERT BOOKING INTO DATABASE
----------------------------------------------------
*/
$stmt = $conn->prepare("
    INSERT INTO bookings 
    (parent_id, tutor_id, subject_id, session_date, status)
    VALUES (?, ?, ?, ?, 'pending')
");

$result = $stmt->execute([
    $parent_id,
    $tutor_id,
    $subject_id,
    $session_date
]);

/*
----------------------------------------------------
REDIRECT AFTER SUCCESS
----------------------------------------------------
*/
if ($result) {
    header("Location: ../parent/parent-bookings.php");
    exit();
} else {
    die("Booking failed. Try again.");
}