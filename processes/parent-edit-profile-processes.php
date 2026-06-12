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
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../classes/Database.php";

$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION['user_id'];

/*
----------------------------------------------------
FETCH USER + PARENT PROFILE
----------------------------------------------------
*/
$stmt = $conn->prepare("SELECT u.id, u.fullname, u.email, p.bio, p.location, p.profile_pic FROM users u LEFT JOIN parent_profile p ON u.id = p.user_id WHERE u.id = ?");
$stmt->execute([$user_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    $data = [
        'id' => $user_id,
        'fullname' => '',
        'email' => '',
        'bio' => '',
        'location' => '',
        'profile_pic' => ''
    ];
}

?>
