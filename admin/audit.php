<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { header('Location: ../auth/login.php'); exit(); }
require_once __DIR__ . '/../classes/Database.php';
$db = new Database(); $conn = $db->connect();

// Check if admin_audit table exists
$cfg = include __DIR__ . '/../config/db-connect.php';
$stmt = $conn->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = 'admin_audit'");
$stmt->execute([$cfg['dbname']]);
$exists = (bool)$stmt->fetchColumn();

$rows = [];
if ($exists) {
    $q = $conn->query('SELECT id, admin_user_id, action, target_user_id, details, created_at FROM admin_audit ORDER BY created_at DESC LIMIT 200');
    $rows = $q->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin Audit</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head><body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:1100px;margin:120px auto;padding:20px">
  <h2>Admin Audit Logs</h2>
  <div style="margin-bottom:12px">
    <a class="btn-outline" href="actions.php?action=export_logs">Export CSV</a>
  </div>
  <div class="glass-card">
    <?php if (!$exists): ?>
      <p>No `admin_audit` table found. Run migrations to enable audit logging.</p>
    <?php else: ?>
      <table style="width:100%;border-collapse:collapse">
        <tr><th>ID</th><th>Admin</th><th>Action</th><th>Target</th><th>Details</th><th>When</th></tr>
        <?php foreach($rows as $r): ?>
          <tr style="border-top:1px solid #eee">
            <td><?php echo (int)$r['id']; ?></td>
            <td><?php echo (int)$r['admin_user_id']; ?></td>
            <td><?php echo htmlspecialchars($r['action']); ?></td>
            <td><?php echo htmlspecialchars($r['target_user_id']); ?></td>
            <td><pre style="white-space:pre-wrap;margin:0"><?php echo htmlspecialchars($r['details']); ?></pre></td>
            <td><?php echo htmlspecialchars($r['created_at']); ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
</body></html>
