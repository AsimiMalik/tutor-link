<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { header('Location: ../auth/login.php'); exit(); }
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/db-connect.php';
$db = new Database(); $conn = $db->connect();

// detect if users.is_active column exists; fall back to a default value if not
$config = include __DIR__ . '/../config/db-connect.php';
$dbName = $config['dbname'];
$colCheck = $conn->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = ? AND table_name = 'users' AND column_name = 'is_active'");
$colCheck->execute([$dbName]);
$hasIsActive = (bool)$colCheck->fetchColumn();

// handle optional search
$q = trim($_GET['q'] ?? '');
$selectFields = $hasIsActive ? 'id, fullname, email, role, is_active' : 'id, fullname, email, role, 1 AS is_active';
if ($q !== '') {
  $stmt = $conn->prepare("SELECT $selectFields FROM users WHERE fullname LIKE ? OR email LIKE ? ORDER BY id DESC");
  $stmt->execute(["%{$q}%","%{$q}%"]);
} else {
  $stmt = $conn->query("SELECT $selectFields FROM users ORDER BY id DESC");
}
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Manage Users</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head><body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:1100px;margin:120px auto;padding:20px">
  <h2>Manage Users</h2>
  <form method="get" style="margin-bottom:12px"><input type="text" name="q" placeholder="Search name or email" value="<?php echo htmlspecialchars($q); ?>"> <button class="btn-outline" type="submit">Search</button></form>
  <div class="glass-card">
    <table style="width:100%;border-collapse:collapse">
      <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Active</th><th>Actions</th></tr>
      <?php foreach($users as $u): ?>
        <tr style="border-top:1px solid #eee">
          <td><?php echo (int)$u['id']; ?></td>
          <td><?php echo htmlspecialchars($u['fullname']); ?></td>
          <td><?php echo htmlspecialchars($u['email']); ?></td>
          <td><?php echo htmlspecialchars($u['role']); ?></td>
          <td><?php echo ($u['is_active'] ?? 1) ? 'Yes' : 'No'; ?></td>
          <td>
            <form method="post" action="actions.php" style="display:inline">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
              <button class="btn-outline" name="suspend" value="1" onclick="return confirm('Toggle active for this user?')">Toggle Active</button>
            </form>
            <form method="post" action="actions.php" style="display:inline;margin-left:6px">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
              <select name="new_role">
                <option value="parent" <?php echo $u['role']==='parent' ? 'selected' : ''; ?>>Parent</option>
                <option value="tutor" <?php echo $u['role']==='tutor' ? 'selected' : ''; ?>>Tutor</option>
                <option value="admin" <?php echo $u['role']==='admin' ? 'selected' : ''; ?>>Admin</option>
              </select>
              <button class="btn-primary" name="change_role" value="1" onclick="return confirm('Change role for this user?')">Change Role</button>
            </form>
            <form method="post" action="actions.php" style="display:inline;margin-left:6px">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
              <button class="btn-danger" name="delete_user" value="1" onclick="return confirm('Permanently delete this user? This cannot be undone.')">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
</body></html>
