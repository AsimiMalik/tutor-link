<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { header('Location: ../auth/login.php'); exit(); }
require_once __DIR__ . '/../includes/csrf.php';

$out = '';
$lockFile = __DIR__ . '/../db/migrations.lock';
if (file_exists($lockFile)) {
  $out = "Migrations are disabled on this site (db/migrations.lock present). Remove the lock file to enable migrations.";
} else {
  // Only run migrations on POST with a valid CSRF token
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) {
      $out = "Invalid CSRF token. Migrations not run.";
    } else {
      ob_start();
      try {
        require_once __DIR__ . '/../scripts/apply_migrations.php';
      } catch (Exception $e) {
        echo "Migration runner error: " . $e->getMessage();
      }
      $out = ob_get_clean();
    }
  } else {
    $out = "Migrations are ready. Press the button below to run them.";
  }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Run Migrations</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head><body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:900px;margin:120px auto;padding:20px">
  <h2>Apply Migrations</h2>
  <div class="glass-card"><pre><?php echo htmlspecialchars($out); ?></pre></div>
  <div style="margin-top:12px">
    <?php if (!file_exists($lockFile)): ?>
      <form method="post" style="display:inline">
        <?php echo csrf_field(); ?>
        <button class="btn-danger" type="submit" onclick="return confirm('Run database migrations now? This may alter the database schema.')">Run Migrations</button>
      </form>
      <a class="btn-outline" href="../scripts/run_reconcile.php" style="margin-left:8px">Run Reconcile Uploads</a>
    <?php endif; ?>
    <a class="btn-primary" href="dashboard.php" style="margin-left:8px">Back</a>
  </div>
</div>
</body></html>
