<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { header('Location: ../auth/login.php'); exit(); }
require_once __DIR__ . '/../classes/Database.php';
$db = new Database(); $conn = $db->connect();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin Dashboard</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head><body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:1100px;margin:120px auto;padding:20px">
    <h2>Admin Dashboard</h2>
    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:18px">
        <a class="btn-primary" href="users.php">Manage Users</a>
        <a class="btn-primary" href="tutors.php">Manage Tutors</a>
        <a class="btn-primary" href="run_migrations.php">Run Migrations</a>
        <a class="btn-primary" href="../scripts/run_reconcile.php">Reconcile Qualification Uploads</a>
        <a class="btn-primary" href="audit.php">Audit Logs</a>
        <a class="btn-outline" href="actions.php?action=export_logs">Export Logs</a>
    </div>
    <hr style="margin:20px 0">
    <div class="glass-card">
        <?php
            $u = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
            $t = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'tutor'")->fetchColumn();
            $p = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'parent'")->fetchColumn();
            echo "<strong>Totals:</strong> Users: {$u} — Tutors: {$t} — Parents: {$p}";
        ?>
    </div>
</div>
</body></html>