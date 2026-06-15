<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { header('Location: ../auth/login.php'); exit(); }
require_once __DIR__ . '/../classes/Database.php';
$db = new Database(); $conn = $db->connect();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: tutors.php'); exit(); }

$stmt = $conn->prepare('SELECT u.id, u.fullname, u.email, u.created_at, tp.* FROM users u LEFT JOIN tutor_profile tp ON u.id = tp.user_id WHERE u.id = ? AND u.role = "tutor" LIMIT 1');
$stmt->execute([$id]);
$tutor = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$tutor) { header('Location: tutors.php'); exit(); }
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>View Tutor</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head><body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:900px;margin:120px auto;padding:20px">
  <h2>View Tutor: <?php echo htmlspecialchars($tutor['fullname']); ?></h2>
  <div class="glass-card">
    <p><strong>ID:</strong> <?php echo (int)$tutor['id']; ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($tutor['email']); ?></p>
    <p><strong>Joined:</strong> <?php echo htmlspecialchars($tutor['created_at'] ?? ''); ?></p>
    <p><strong>Verified:</strong> <?php echo (int)($tutor['is_verified'] ?? 0) ? 'Yes' : 'No'; ?></p>
    <p><strong>Qualification:</strong>
      <?php if (!empty($tutor['qualification_file'])): ?>
        <a href="/brilliance/uploads/qualifications/<?php echo htmlspecialchars($tutor['qualification_file']); ?>" target="_blank">View file</a>
      <?php else: ?>
        —
      <?php endif; ?>
    </p>
    <hr>
    <div>
      <form method="post" action="actions.php" style="display:inline">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="user_id" value="<?php echo (int)$tutor['id']; ?>">
        <button class="btn-accent" name="verify_tutor" value="1">Verify Tutor</button>
      </form>

      <form method="post" action="actions.php" style="display:inline;margin-left:8px">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="user_id" value="<?php echo (int)$tutor['id']; ?>">
        <button class="btn-outline" name="suspend" value="1" onclick="return confirm('Toggle active for this tutor?')">Toggle Active</button>
      </form>

      <a class="btn-primary" href="tutors.php" style="margin-left:10px">Back to Tutors</a>
    </div>
  </div>
</div>
</body></html>
