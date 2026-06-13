<?php
require_once __DIR__ . '/../classes/Review.php';
$review = new Review();
$target = isset($_GET['tutor_id']) ? (int)$_GET['tutor_id'] : 0;
$rows = [];
$avg = null;
if ($target>0){
    $rows = $review->getForUser($target);
    $avg = $review->getAverage($target);
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Reviews</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:900px;margin:90px auto;padding:20px">
    <h2>Reviews</h2>
    <?php if ($avg): ?>
        <p>Average: <?= htmlspecialchars(number_format($avg['avg_rating'],2)) ?> (<?= (int)$avg['total'] ?> reviews)</p>
    <?php endif; ?>
    <?php if (empty($rows)): ?>
        <p>No reviews yet.</p>
    <?php else: ?>
        <?php foreach($rows as $r): ?>
            <div style="border:1px solid #eee;padding:12px;border-radius:8px;margin-bottom:12px">
                <strong><?= htmlspecialchars($r['reviewer_name']) ?></strong>
                <span style="float:right">Rating: <?= htmlspecialchars($r['rating']) ?>/5</span>
                <p style="margin-top:6px"><strong><?= htmlspecialchars($r['title'] ?? '') ?></strong></p>
                <div style="white-space:pre-wrap"><?= htmlspecialchars($r['body'] ?? '') ?></div>
                <div class="muted" style="margin-top:8px"><?= htmlspecialchars($r['created_at']) ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
