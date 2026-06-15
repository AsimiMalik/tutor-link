<?php
session_start();
require_once __DIR__ . '/database.php';

$db = new Database();
$conn = $db->connect();

// fetch tutors and their profile info
$stmt = $conn->prepare("SELECT u.id, u.fullname, t.profile_pic, t.rating_avg, t.total_reviews, t.hourly_rate, t.location
                       FROM users u
                       LEFT JOIN tutor_profile t ON u.id = t.user_id
                       WHERE u.role = 'tutor'");
$stmt->execute();
 $tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Find Tutors | Brilliance</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
<link rel="stylesheet" href="/brilliance/assets/css/tutors.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<section class="tutors-wrapper">
  <div class="container tutors-container">
    <div class="section-title">
      <h2>Available Tutors</h2>
      <p>Browse tutors and view detailed profiles</p>
    </div>

    <div class="tutors-grid">
      <?php foreach($tutors as $t): ?>
        <div class="tutor-card">
          <?php if(!empty($t['profile_pic'])): ?>
            <img src="/brilliance/uploads/<?php echo htmlspecialchars($t['profile_pic']); ?>" alt="<?php echo htmlspecialchars($t['fullname']); ?>">
          <?php else: ?>
            <img src="/brilliance/assets/images/logo.png" alt="No image">
          <?php endif; ?>

          <h3><?php echo htmlspecialchars($t['fullname'] ?? 'Tutor'); ?></h3>
          <div class="meta"><?php echo htmlspecialchars($t['location'] ?? 'No location'); ?></div>

          <?php
          try {
              $ss = $conn->prepare("SELECT s.name FROM tutor_subjects ts JOIN subjects s ON ts.subject_id = s.id WHERE ts.tutor_id = ? ORDER BY s.name");
              $ss->execute([ $t['id'] ]);
              $tsubjects = $ss->fetchAll(PDO::FETCH_COLUMN);
          } catch (PDOException $e) {
              $tsubjects = [];
          }
          ?>
          <div style="margin:8px 0">
            <?php if (!empty($tsubjects)): ?>
              <?php foreach($tsubjects as $sub): ?>
                <a class="subject-tag" href="/brilliance/view-tutors.php?subject=<?php echo urlencode($sub); ?>"><?php echo htmlspecialchars($sub); ?></a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <div class="rating">
            <div class="stars">★</div>
            <div><?php echo $t['rating_avg'] ?? '0'; ?> (<?php echo $t['total_reviews'] ?? '0'; ?>)</div>
          </div>

          <div class="card-actions">
            <a class="btn" href="/brilliance/tutor/view-tutor.php?id=<?php echo urlencode($t['id']); ?>">View Profile</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
