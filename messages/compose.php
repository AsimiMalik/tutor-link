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
<link rel="stylesheet" href="/brilliance/assets/css/messages.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="message-page">
    <h2>Compose Message</h2>
    <?php if(!empty($_SESSION['flash_error'])): ?><div class="message-form" style="background:#fff8f9;border-left:4px solid #f5c6cb;margin-bottom:12px;color:#721c24"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div><?php endif; ?>

    <div class="message-form">
        <form method="post" action="../processes/send-message.php" class="message-form-inner">
            <?php echo csrf_field(); ?>
                    <?php if ($prefill_receiver): ?>
                        <label for="receiver_display">To</label>
                        <input id="receiver_display" type="text" value="<?= htmlspecialchars($receiver_name ?: $prefill_receiver) ?>" readonly>
                        <input type="hidden" name="receiver_id" value="<?= $prefill_receiver ?>">
                    <?php else: ?>
                        <?php
                        // attempt to load users for a nicer selector; fallback to id input if DB unavailable or empty
                        $users = [];
                        try {
                            require_once __DIR__ . '/../classes/Database.php';
                            $db = new Database(); $conn = $db->connect();
                            $stmt = $conn->prepare('SELECT id, fullname, role FROM users ORDER BY fullname LIMIT 500');
                            $stmt->execute();
                            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (Exception $e) { $users = []; }
                        ?>

                        <?php if (!empty($users)): ?>
                            <label for="receiver_id">To</label>
                            <select id="receiver_id" name="receiver_id" required>
                                <option value="">Select recipient</option>
                                <?php foreach($users as $u): ?>
                                    <option value="<?= (int)$u['id'] ?>"><?= htmlspecialchars(($u['fullname'] ?: 'User #'.$u['id']) . (isset($u['role']) ? ' (' . $u['role'] . ')' : '')) ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <label for="receiver_id">To (user id)</label>
                            <input id="receiver_id" type="number" name="receiver_id" value="" placeholder="Enter recipient user ID" required>
                        <?php endif; ?>
                    <?php endif; ?>

            <label for="subject">Subject</label>
            <input id="subject" type="text" name="subject" placeholder="Short subject (optional)">

            <label for="body">Message</label>
            <textarea id="body" name="body" required></textarea>

            <input type="hidden" name="booking_id" value="<?= $prefill_booking ?>">

            <div class="message-actions">
                <button class="btn-primary" type="submit">Send Message</button>
                <a href="inbox.php" class="btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
