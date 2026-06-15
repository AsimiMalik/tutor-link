<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { header('Location: ../auth/login.php'); exit(); }
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../includes/csrf.php';
$db = new Database(); $conn = $db->connect();

$stmt = $conn->prepare("SELECT u.id, u.fullname, u.email, tp.is_verified, tp.qualification_file FROM users u LEFT JOIN tutor_profile tp ON u.id = tp.user_id WHERE u.role = 'tutor' ORDER BY u.id DESC");
$stmt->execute();
$tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Manage Tutors</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head><body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:1100px;margin:120px auto;padding:20px">
  <h2>Manage Tutors</h2>
  <div class="glass-card">
    <table style="width:100%;border-collapse:collapse">
      <tr><th>ID</th><th>Name</th><th>Email</th><th>Verified</th><th>Qualification</th><th>Actions</th></tr>
      <?php foreach($tutors as $t): ?>
        <tr style="border-top:1px solid #eee">
          <td><?php echo (int)$t['id']; ?></td>
          <td><?php echo htmlspecialchars($t['fullname']); ?></td>
          <td><?php echo htmlspecialchars($t['email']); ?></td>
          <td><?php echo (int)($t['is_verified'] ?? 0) ? 'Yes' : 'No'; ?></td>
          <td><?php echo !empty($t['qualification_file']) ? '<a href="/brilliance/uploads/qualifications/'.htmlspecialchars($t['qualification_file']).'" target="_blank">View</a>' : '—'; ?></td>
          <td>
            <form method="post" action="actions.php" style="display:inline">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="user_id" value="<?php echo (int)$t['id']; ?>">
              <button class="btn-accent" name="verify_tutor" value="1">Verify</button>
              <button class="btn-outline" name="suspend" value="1">Suspend</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
</body></html>
