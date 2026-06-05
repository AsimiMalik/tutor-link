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
<title>Edit Profile</title>

<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php include '../includes/tutor-navbar.php'; ?>

<section class="dashboard-section">
<div class="container">

<div class="section-title">
<h2>Edit Profile</h2>
</div>

<form class="tutor-card" method="POST" action="../processes/update-profile.php" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?php echo $data['id']; ?>">

<label>Profile Picture</label>
<input type="file" name="profile_pic">

<label>Subjects</label>
<input type="text" name="subjects" value="<?php echo $data['subjects']; ?>">

<label>Qualifications</label>
<textarea name="qualifications"><?php echo $data['qualifications']; ?></textarea>

<button class="btn-primary" name="update">Save Changes</button>

</form>

</div>
</section>

</body>
</html>