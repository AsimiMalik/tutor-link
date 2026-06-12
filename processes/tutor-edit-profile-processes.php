<?php
session_start();

/*
----------------------------------------------------
CHECK LOGIN
----------------------------------------------------
*/
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

/*
----------------------------------------------------
CHECK ROLE
----------------------------------------------------
*/
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tutor') {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../classes/Database.php";

$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION['user_id'];

/*
----------------------------------------------------
FETCH USER + TUTOR PROFILE
----------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT 
        u.id,
        u.fullname,
        u.email,
        t.bio,
        t.qualification,
        t.experience,
        t.location,
        t.hourly_rate,
        t.profile_pic
    FROM users u
    LEFT JOIN tutor_profile t 
    ON u.id = t.user_id
    WHERE u.id = ?
");

$stmt->execute([$user_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

/*
----------------------------------------------------
FALLBACK IF NO PROFILE EXISTS
----------------------------------------------------
*/
if (!$data) {
    $data = [
        'id' => $user_id,
        'fullname' => '',
        'email' => '',
        'bio' => '',
        'qualification' => '',
        'experience' => '',
        'location' => '',
        'hourly_rate' => 0,
        'profile_pic' => ''
    ];
}

// fetch all available subjects for selection
$stmt = $conn->query("SELECT id, name FROM subjects ORDER BY name");
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// fetch assigned subject ids for this tutor (if table exists)
try {
    $stmt = $conn->prepare("SELECT subject_id FROM tutor_subjects WHERE tutor_id = ?");
    $stmt->execute([$user_id]);
    $assigned_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $assigned_subjects = array_map(function($r){return (int)$r['subject_id'];}, $assigned_rows);
} catch (PDOException $e) {
    $assigned_subjects = [];
}
?>