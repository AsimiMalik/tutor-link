<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
$reviewee = isset($_GET['tutor_id']) ? (int)$_GET['tutor_id'] : 0;
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Leave Review</title>
<link rel="stylesheet" href="/tutorlink/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:700px;margin:90px auto;padding:20px">
    <h2>Leave a Review</h2>
    <form method="post" action="../processes/submit-review.php">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="reviewee_id" value="<?= $reviewee ?>">
        <label>Rating (1-5)</label>
        <input type="number" name="rating" min="1" max="5" required>
        <label>Title (optional)</label>
        <input type="text" name="title">
        <label>Comment</label>
        <textarea name="body" required style="min-height:120px"></textarea>
        <button class="btn-primary" type="submit">Submit Review</button>
    </form>
</div>
</body>
</html>
