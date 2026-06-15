<?php
session_start();
require_once __DIR__ . '/../classes/database.php';
require_once __DIR__ . '/../classes/Review.php';

$db = new Database();
$conn = $db->connect();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(404);
    echo 'Tutor not found';
    exit();
}

$stmt = $conn->prepare("SELECT u.fullname, u.email, t.* FROM users u LEFT JOIN tutor_profile t ON u.id = t.user_id WHERE u.id = ? AND u.role = 'tutor'");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$data) {
    http_response_code(404);
    echo 'Tutor not found';
    exit();
}

// fetch subjects taught by this tutor (if table exists)
try {
    $sstmt = $conn->prepare("SELECT s.name FROM tutor_subjects ts JOIN subjects s ON ts.subject_id = s.id WHERE ts.tutor_id = ? ORDER BY s.name");
    $sstmt->execute([$id]);
    $subjects = $sstmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $subjects = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo htmlspecialchars($data['fullname']); ?> — Tutor | Brilliance</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
<link rel="stylesheet" href="/brilliance/assets/css/tutor-profile.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="profile-wrapper">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-img">
                <?php if(!empty($data['profile_pic'])): ?>
                    <img src="/brilliance/uploads/<?php echo htmlspecialchars($data['profile_pic']); ?>" alt="Profile" class="profile-avatar-large">
                <?php else: ?>
                    <div class="no-img">No Image</div>
                <?php endif; ?>
            </div>

            <div class="profile-info">
                <h2><?php echo htmlspecialchars($data['fullname']); ?> <span class="badge"><?php echo (int)($data['is_verified'] ?? 0) ? 'Verified' : ''; ?></span></h2>
                <p class="location">📍 <?php echo htmlspecialchars($data['location'] ?? 'Not set'); ?></p>
                <?php
                $reviewObj = new Review();
                $avg = $reviewObj->getAverage($id);
                $avg_display = $avg && $avg['avg_rating'] ? number_format($avg['avg_rating'],2) : '0';
                $total_reviews = $avg && isset($avg['total']) ? (int)$avg['total'] : 0;
                ?>
                <p class="rating">⭐ <?php echo $avg_display; ?> (<?php echo $total_reviews; ?> reviews)</p>
                <div class="action-buttons">
                    <a href="/brilliance/bookings/book-tutor.php?tutor_id=<?php echo $id; ?>" class="btn">Book Tutor</a>
                    <a href="/brilliance/messages/compose.php?to=<?php echo $id; ?>" class="btn">Message</a>
                    <?php if (isset($_SESSION['user_id'])):
                        // show review button only to parents
                        $show_review_btn = false;
                        if (isset($_SESSION['user_id'])){
                            $u = $conn->prepare("SELECT role FROM users WHERE id = ?");
                            $u->execute([$_SESSION['user_id']]);
                            $r = $u->fetch(PDO::FETCH_ASSOC);
                            if ($r && ($r['role'] ?? '') === 'parent') $show_review_btn = true;
                        }
                        if ($show_review_btn): ?>
                        <a href="/brilliance/reviews/submit_review.php?tutor_id=<?php echo $id; ?>" class="btn">Leave Review</a>
                    <?php endif; endif; ?>
                </div>
            </div>

            <div class="stats-row">
                <div class="stat">
                    <h4><?php echo $avg_display; ?></h4>
                    <p>Average Rating</p>
                </div>

                <div class="stat">
                    <h4><a href="/brilliance/reviews/view_reviews.php?tutor_id=<?php echo $id; ?>"><?php echo $total_reviews; ?></a></h4>
                    <p><a href="/brilliance/reviews/view_reviews.php?tutor_id=<?php echo $id; ?>">Reviews</a></p>
                </div>

                <div class="stat">
                    <h4><?php echo count($subjects); ?></h4>
                    <p>Subjects</p>
                </div>
            </div>

        </div>

        <div class="profile-body">
            <div class="section">
                <h3>Bio</h3>
                <p><?php echo nl2br(htmlspecialchars($data['bio'] ?? 'No bio provided')); ?></p>
            </div>
            <div class="profile-body">
                <div class="section">
                    <h3>Reviews</h3>
                    <?php
                    $reviews = $reviewObj->getForUser($id);
                    if (empty($reviews)):
                    ?>
                        <p>No reviews yet. Be the first to <a href="/brilliance/reviews/submit_review.php?tutor_id=<?php echo $id; ?>">leave a review</a>.</p>
                    <?php else: ?>
                        <?php $count = 0; foreach($reviews as $r): if ($count++ >= 5) break; ?>
                            <div style="border:1px solid #eee;padding:12px;border-radius:6px;margin-bottom:10px">
                                <strong><?php echo htmlspecialchars($r['reviewer_name']); ?></strong>
                                <span style="float:right">Rating: <?php echo (int)$r['rating']; ?>/5</span>
                                <p style="margin-top:6px"><strong><?php echo htmlspecialchars($r['title'] ?? ''); ?></strong></p>
                                <div style="white-space:pre-wrap"><?php echo htmlspecialchars($r['body'] ?? ''); ?></div>
                                <div class="muted" style="margin-top:8px"><?php echo htmlspecialchars($r['created_at']); ?></div>
                            </div>
                        <?php endforeach; ?>
                        <p><a href="/brilliance/reviews/view_reviews.php?tutor_id=<?php echo $id; ?>">View all reviews</a></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="section">
                <h3>Experience</h3>
                <p><?php echo nl2br(htmlspecialchars($data['experience'] ?? 'No experience provided')); ?></p>
            </div>

            <div class="section">
                <h3>Subjects</h3>
                <p>
                    <?php if (!empty($subjects)): ?>
                        <?php foreach($subjects as $s): ?>
                            <a class="subject-tag" href="/brilliance/view-tutors.php?subject=<?php echo urlencode($s); ?>"><?php echo htmlspecialchars($s); ?></a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        No subjects listed
                    <?php endif; ?>
                </p>
            </div>

            <div class="section">
                <h3>Hourly Rate</h3>
                <p>₦<?php echo htmlspecialchars($data['hourly_rate'] ?? '0'); ?> / hour</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
