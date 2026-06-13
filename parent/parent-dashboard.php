<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

/* ✅ ROLE CHECK */
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'parent'){
    header("Location: ../auth/login.php");
    exit();
}

$fullname = $_SESSION['fullname'] ?? 'Parent';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Parent Dashboard | Brilliance</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head>

<body>

<?php include '../includes/parent-navbar.php'; ?>

<section class="top-tutors">

<div class="container">

<div class="section-title">
<h2>Welcome, <?php echo htmlspecialchars($fullname); ?> 👋</h2>
<p>Find and manage tutors for your children</p>
</div>

<div class="tutors-grid">

<div class="tutor-card">
<h3>Find Tutors</h3>
<p>Search for qualified tutors near you.</p>
<a href="/brilliance/view-tutors.php" class="btn-primary">Search</a>
</div>

<div class="tutor-card">
<h3>My Bookings</h3>
<p>View your booked lessons and schedules.</p>
<a href="/brilliance/parent/parent-bookings.php" class="btn-primary">View Bookings</a>
</div>

<div class="tutor-card">
<h3>Messages</h3>
<p>Chat with tutors directly.</p>
<a href="#" class="btn-primary">Open Chat</a>
</div>

<div class="tutor-card">
<h3>Profile</h3>
<p>View or edit your parent profile.</p>
<a href="/brilliance/parent/parent-profile.php" class="btn-primary">My Profile</a>
</div>

</div>

</div>

</section>

</body>
</html>