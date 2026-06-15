<?php
session_start();
require_once __DIR__ . '/classes/Database.php';

$db = new Database();
$conn = $db->connect();

// read search parameters (GET)
$subject = isset($_GET['subject']) ? trim($_GET['subject']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$level = isset($_GET['level']) ? trim($_GET['level']) : '';

// build dynamic query with safe parameters
$params = [];
$joins = '';
$where = "u.role = 'tutor'";

if ($subject !== '') {
  $joins .= " LEFT JOIN tutor_subjects ts ON u.id = ts.tutor_id LEFT JOIN subjects s ON ts.subject_id = s.id";
  $where .= " AND (s.name LIKE ? OR u.fullname LIKE ? OR t.bio LIKE ? )";
  $like = "%" . $subject . "%";
  $params[] = $like;
  $params[] = $like;
  $params[] = $like;
}

if ($location !== '') {
  $where .= " AND t.location LIKE ?";
  $params[] = "%" . $location . "%";
}

$sql = "SELECT DISTINCT u.id, u.fullname, t.profile_pic, t.rating_avg, t.total_reviews, t.hourly_rate, t.location
    FROM users u
    LEFT JOIN tutor_profile t ON u.id = t.user_id
    " . $joins . "
    WHERE " . $where . "
    ORDER BY t.rating_avg DESC, u.fullname ASC
    LIMIT 200";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
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
<?php include 'includes/parent-navbar.php'; ?>

<section class="tutors-wrapper">
  <div class="container tutors-container">
    <div class="section-title">
      <h2>Available Tutors</h2>
      <p>Browse tutors and view detailed profiles</p>
    </div>
    <!-- Search / filter form (preserves previous values) -->
    <form method="get" action="/brilliance/view-tutors.php" class="search-card" style="margin-bottom:20px; display:flex; gap:10px; flex-wrap:wrap;">
      <div class="search-field" style="flex:1; min-width:180px;">
        <i class="fa-solid fa-book"></i>
        <input type="text" name="subject" placeholder="Subject" value="<?php echo htmlspecialchars($subject); ?>">
      </div>

      <div class="search-field" style="flex:1; min-width:160px;">
        <i class="fa-solid fa-location-dot"></i>
        <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($location); ?>">
      </div>

      <div class="search-field" style="flex:1; min-width:160px;">
        <i class="fa-solid fa-school"></i>
        <input type="text" name="level" placeholder="Class Level (optional)" value="<?php echo htmlspecialchars($level); ?>">
      </div>

      <button type="submit" class="search-btn">Search</button>
    </form>

    <div class="tutors-grid">
      <?php if (empty($tutors)): ?>
        <div class="muted">No tutors found matching your search.</div>
      <?php endif; ?>

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
          // fetch subjects for this tutor (small N so per-row query acceptable)
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

<?php include 'includes/footer.php'; ?>
</body>
</html>
