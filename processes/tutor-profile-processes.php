<?php
session_start();

/*
----------------------------------------------------
CHECK LOGIN + ROLE
Only tutors can access this
----------------------------------------------------
*/
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'tutor') {
    header("Location: ../auth/login.php");
    exit();
}

/*
----------------------------------------------------
CONNECT DATABASE
----------------------------------------------------
*/
require_once "../classes/Database.php";

$db = new Database();
$conn = $db->connect();

/*
----------------------------------------------------
GET LOGGED IN USER ID
----------------------------------------------------
*/
$user_id = $_SESSION['user_id'];

/*
----------------------------------------------------
FETCH TUTOR PROFILE DATA
JOIN users + tutor_profile table
----------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT 
        u.fullname,
        t.bio,
        t.qualification,
        t.experience,
        t.location,
        t.hourly_rate,
        t.profile_pic,
        t.is_verified,
        t.rating_avg,
        t.total_reviews
    FROM users u
    LEFT JOIN tutor_profile t ON u.id = t.user_id
    WHERE u.id = ?
");

$stmt->execute([$user_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

/*
----------------------------------------------------
SAFE DEFAULT VALUES
Prevents errors if profile does not exist yet
----------------------------------------------------
*/
if (!$data) {
    $data = [
        'fullname' => '',
        'bio' => '',
        'qualification' => '',
        'experience' => '',
        'location' => '',
        'hourly_rate' => 0,
        'profile_pic' => '',
        'is_verified' => 0,
        'rating_avg' => 0,
        'total_reviews' => 0
    ];
}

// fetch tutor subjects (if table exists)
try {
    $sstmt = $conn->prepare("SELECT s.name FROM tutor_subjects ts JOIN subjects s ON ts.subject_id = s.id WHERE ts.tutor_id = ? ORDER BY s.name");
    $sstmt->execute([$user_id]);
    $subrows = $sstmt->fetchAll(PDO::FETCH_COLUMN);
    $data['subjects'] = $subrows;
} catch (PDOException $e) {
    $data['subjects'] = [];
}
?>