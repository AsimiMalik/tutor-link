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
<div class="page">
    <div class="container" style="max-width:900px">
        <h2>Reviews</h2>
        <?php if ($avg): ?>
            <div class="glass-card" style="margin-bottom:12px;display:flex;justify-content:space-between;align-items:center">
                <div>
                    <strong>Average Rating</strong>
                    <div style="font-size:20px"><?= htmlspecialchars(number_format($avg['avg_rating'],2)) ?> / 5</div>
                </div>
                <div style="color:var(--text)"><?= (int)$avg['total'] ?> reviews</div>
            </div>
        <?php endif; ?>

        <?php if (empty($rows)): ?>
            <p>No reviews yet.</p>
        <?php else: ?>
            <?php foreach($rows as $r): ?>
                <div class="glass-card" style="margin-bottom:12px;padding:14px">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px">
                        <div>
                            <strong><?= htmlspecialchars($r['reviewer_name']) ?></strong>
                            <div style="color:#8891a6;font-size:13px"><?= htmlspecialchars($r['created_at']) ?></div>
                        </div>
                        <div style="font-weight:700;color:var(--primary)"><?= htmlspecialchars($r['rating']) ?>★</div>
                    </div>
                    <?php if (!empty($r['title'])): ?><p style="margin-top:8px"><strong><?= htmlspecialchars($r['title']) ?></strong></p><?php endif; ?>
                    <div style="white-space:pre-wrap;margin-top:6px;color:var(--text)"><?= htmlspecialchars($r['body'] ?? '') ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
