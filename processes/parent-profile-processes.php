<?php
session_start();

/*
----------------------------------------------------
CHECK LOGIN + ROLE
Only parents can access this
----------------------------------------------------
*/
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'parent') {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../classes/Database.php";
$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT u.fullname, u.email, p.profile_pic, p.location, p.bio FROM users u LEFT JOIN parent_profile p ON u.id = p.user_id WHERE u.id = ?");
$stmt->execute([$user_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    $data = [
        'fullname' => '',
        'email' => '',
        'profile_pic' => '',
        'location' => '',
        'bio' => ''
    ];
}

?>
