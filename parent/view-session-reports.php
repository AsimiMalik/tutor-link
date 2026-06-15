<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<?php
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/SessionReport.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'parent') {
    header('Location: ../auth/login.php');
    exit();
}

$db = new Database();
$conn = $db->connect();
$sr = new SessionReport($conn);
$reports = $sr->getByParent($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Session Reports — Brilliance</title>
    <link rel="stylesheet" href="/brilliance/assets/css/style.css">
    <style>.card{background:#fff;padding:20px;border-radius:10px;margin-bottom:16px;box-shadow:0 6px 18px rgba(0,0,0,.04)}</style>
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container" style="margin-top:110px">
    <h2>Your Session Reports</h2>

    <?php if (empty($reports)): ?>
        <p>No session reports yet.</p>
    <?php else: ?>
        <?php foreach ($reports as $r): ?>
            <div class="card">
                <strong>From Tutor:</strong> <?php echo htmlspecialchars($r['tutor_name'] ?? 'Tutor'); ?>
                <div><small><?php echo htmlspecialchars($r['created_at']); ?></small></div>
                <p><strong>Topics:</strong> <?php echo nl2br(htmlspecialchars($r['topics'])); ?></p>
                <p><strong>Duration:</strong> <?php echo (int)$r['duration_minutes']; ?> minutes</p>
                <p><strong>Attendance:</strong> <?php echo htmlspecialchars($r['attendance']); ?></p>
                <p><strong>Homework:</strong> <?php echo nl2br(htmlspecialchars($r['homework'])); ?></p>
                <p><strong>Rating:</strong> <?php echo htmlspecialchars($r['rating'] ?? '—'); ?></p>
                <?php if (isset($r['tutor_id']) && !empty($r['tutor_id'])): ?>
                    <p><a href="/brilliance/reviews/submit_review.php?tutor_id=<?php echo (int)$r['tutor_id']; ?>&booking_id=<?php echo (int)$r['booking_id']; ?>" class="btn-outline">Leave Feedback</a></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
