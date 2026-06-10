<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

/* ✅ ROLE CHECK */
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'tutor'){
    header("Location: ../auth/login.php");
    exit();
}

$fullname = $_SESSION['fullname'] ?? 'Tutor';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tutor Dashboard | TutorLink</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
<?php include '../assets/css/style.css'; ?>
</style>
</head>

<body>

<?php include '../includes/t-navbar.php'; ?>

<section class="top-tutors">

<div class="container">

<div class="section-title">
<h2>Welcome, <?php echo htmlspecialchars($fullname); ?> 👋</h2>
<p>Manage your profile and students here</p>
</div>

<div class="tutors-grid">

<div class="tutor-card">
<h3>My Profile</h3>
<p>Update your subjects, experience, and availability.</p>
<a href="#" class="btn-primary">Edit Profile</a>
</div>

<div class="tutor-card">
<h3>My Students</h3>
<p>View students who booked your lessons.</p>
<a href="#" class="btn-primary">View Students</a>
</div>

<div class="tutor-card">
<h3>Messages</h3>
<p>Chat with parents and students.</p>
<a href="#" class="btn-primary">Open Chat</a>
</div>

</div>

</div>

</section>

</body>
</html>