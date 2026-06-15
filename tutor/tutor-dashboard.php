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

// compute unread messages count
$unread_count = 0;
require_once __DIR__ . '/../classes/Database.php';
$db = new Database(); $conn = $db->connect();
if ($conn) {
    try {
        $stmt = $conn->prepare('SELECT COUNT(*) as cnt FROM messages WHERE receiver_id = ? AND is_read = 0');
        $stmt->execute([$_SESSION['user_id']]);
        $unread_count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    } catch (Exception $e) { $unread_count = 0; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tutor Dashboard | Brilliance</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head>

<body>

<?php include '../includes/tutor-navbar.php'; ?>

<section class="top-tutors">

<div class="container">

<div class="section-title">
<h2>Welcome, <?php echo htmlspecialchars($fullname); ?> 👋</h2>
<p>Manage your profile and students here</p>
</div>
 
<div style="display:flex;justify-content:flex-end;gap:8px;margin-bottom:18px">
    <a href="/brilliance/messages/inbox.php" class="btn-outline">Inbox<?php if ($unread_count>0) echo ' <span class="badge">'.htmlspecialchars($unread_count).'</span>'; ?></a>
    <a href="/brilliance/tutor/create-quiz.php" class="btn-primary">Create Quiz</a>
</div>
<div class="tutors-grid">

<div class="tutor-card">
<h3>My Profile</h3>
<p>Update your subjects, experience, and availability.</p>
<a href="/brilliance/tutor/tutor-edit-profile.php" class="btn-primary">Edit Profile</a>
</div>

<div class="tutor-card">
<h3>My Students</h3>
<p>View students who booked your lessons.</p>
<a href="/brilliance/tutor/tutor-bookings.php" class="btn-primary">View Students</a>
</div>

<div class="tutor-card">
<h3>Messages</h3>
<p>Chat with parents and students.</p>
<div style="display:flex;align-items:center;gap:8px">
    <a href="/brilliance/messages/inbox.php" class="btn-primary">Open Inbox</a>
    <?php if ($unread_count > 0): ?>
        <span style="background:#e74c3c;color:#fff;padding:6px 10px;border-radius:18px;font-weight:600;"><?= $unread_count ?> new</span>
    <?php endif; ?>
    </div>
</div>

<div class="tutor-card">
<h3>Create Quiz</h3>
<p>Create and assign quizzes to your students. Use AI-assist to generate questions.</p>
<a href="/brilliance/tutor/create-quiz.php" class="btn-primary">Create Quiz</a>
</div>

</div>

</div>

</section>

</body>
</html>