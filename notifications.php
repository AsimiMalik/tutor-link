<?php
session_start();
require_once __DIR__ . '/classes/Database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php'); exit();
}

$db = new Database(); $conn = $db->connect();

// mark single notification read (via GET param)
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $nid = (int)$_GET['mark_read'];
    $u = $conn->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
    $u->execute([$nid, $_SESSION['user_id']]);
    header('Location: notifications.php'); exit();
}

// mark all read
if (isset($_GET['mark_all'])) {
    $u = $conn->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
    $u->execute([$_SESSION['user_id']]);
    header('Location: notifications.php'); exit();
}

$stmt = $conn->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Notifications — Brilliance</title>
    <link rel="stylesheet" href="/brilliance/assets/css/style.css">
    <style>.container{max-width:900px;margin:120px auto}.note{background:#fff;padding:12px;border-radius:8px;margin-bottom:10px;box-shadow:0 6px 16px rgba(0,0,0,.04)}</style>
</head>
<body>
<?php include __DIR__ . '/includes/navbar.php'; ?>
<div class="container">
    <h2>Your Notifications</h2>
    <p><a href="notifications.php?mark_all=1" class="btn-outline">Mark all read</a></p>
    <?php if (empty($notes)): ?>
        <p>No notifications.</p>
    <?php else: foreach($notes as $n): ?>
        <div class="note">
            <div style="display:flex;justify-content:space-between">
                <div><?php echo htmlspecialchars($n['message'] ?? ''); ?></div>
                <div><small><?php echo htmlspecialchars($n['created_at']); ?></small></div>
            </div>
            <div style="margin-top:8px">
                <?php if (!$n['is_read']): ?>
                    <a href="notifications.php?mark_read=<?php echo (int)$n['id']; ?>" class="btn-primary">Mark read</a>
                <?php else: ?>
                    <span style="color:#666">Read</span>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>
</body>
</html>
