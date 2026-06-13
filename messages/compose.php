<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
$prefill_receiver = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : 0;
$prefill_booking = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

// optional receiver name lookup
$receiver_name = '';
if ($prefill_receiver) {
    require_once __DIR__ . '/../classes/Database.php';
    $db = new Database(); $conn = $db->connect();
    $stmt = $conn->prepare('SELECT fullname FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$prefill_receiver]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    $receiver_name = $r['fullname'] ?? '';
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Compose Message</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:700px;margin:90px auto;padding:20px">
    <h2>Compose Message</h2>
    <?php if(!empty($_SESSION['flash_error'])): ?><div style="background:#f8d7da;padding:10px;border-radius:6px;color:#721c24;margin-bottom:12px"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div><?php endif; ?>
    <form method="post" action="../processes/send-message.php">
        <?php echo csrf_field(); ?>
        <label>To</label>
        <input type="number" name="receiver_id" value="<?= $prefill_receiver ?>" placeholder="User ID" required>
        <?php if ($receiver_name): ?><div class="muted"><?= htmlspecialchars($receiver_name) ?></div><?php endif; ?>
        <label>Subject</label>
        <input type="text" name="subject">
        <label>Message</label>
        <textarea name="body" required style="min-height:150px"></textarea>
        <input type="hidden" name="booking_id" value="<?= $prefill_booking ?>">
        <button class="btn-primary" type="submit">Send</button>
    </form>
</div>
</body>
</html>
