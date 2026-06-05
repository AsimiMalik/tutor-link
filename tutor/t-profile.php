<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'tutor'){
    header("Location: ../auth/login.php");
    exit();
}

require_once "../classes/database.php";
require_once "../classes/User.php";

$db = new Database();
$conn = $db->connect();
$user = new User($conn);

$data = $user->getUserById($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
<head>
<title>My Profile</title>

<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php include '../includes/tutor-navbar.php'; ?>

<section class="dashboard-section">
<div class="container">

<div class="section-title">
<h2>My Profile</h2>
</div>

<div class="tutor-card">

<?php if($data['profile_pic']): ?>
    <img src="../uploads/<?php echo $data['profile_pic']; ?>" style="width:150px;border-radius:50%;">
<?php else: ?>
    <p>No profile picture</p>
<?php endif; ?>

<h3><?php echo $data['fullname']; ?></h3>

<p><b>Subjects:</b> <?php echo $data['subjects']; ?></p>
<p><b>Qualifications:</b> <?php echo $data['qualifications']; ?></p>

<a href="edit-profile.php" class="btn-primary">Edit Profile</a>

</div>

</div>
</section>

</body>
</html>