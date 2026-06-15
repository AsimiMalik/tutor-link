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
// compute unread messages count
$unread = 0;
require_once __DIR__ . '/../classes/Database.php';
$db = new Database(); $conn = $db->connect();
if ($conn) {
    try {
        $stmt = $conn->prepare('SELECT COUNT(*) as cnt FROM messages WHERE receiver_id = ? AND is_read = 0');
        $stmt->execute([$_SESSION['user_id']]);
        $unread = (int)$stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    } catch (Exception $e) { $unread = 0; }
}
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

<div style="display:flex;justify-content:flex-end;gap:8px;margin-bottom:18px">
    <a href="/brilliance/messages/inbox.php" class="btn-outline">Inbox<?php if ($unread>0) echo ' <span class="badge">'.htmlspecialchars($unread).'</span>'; ?></a>
    <a href="/brilliance/quizzes/index.php" class="btn-primary">Quizzes</a>
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
<a href="/brilliance/messages/inbox.php" class="btn-primary">Open Chat</a>
</div>

<div class="tutor-card">
<h3>Profile</h3>
<p>View or edit your parent profile.</p>
<a href="/brilliance/parent/parent-profile.php" class="btn-primary">My Profile</a>
</div>

<div class="tutor-card">
<h3>Quizzes</h3>
<p>Take quizzes assigned by tutors to help your child learn.</p>
<a href="/brilliance/quizzes/index.php" class="btn-primary">View Quizzes</a>
</div>

</div>

</div>

</section>

</body>
</html>