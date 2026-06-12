<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
require_once __DIR__ . '/../classes/Message.php';
$msg = new Message();
$inbox = $msg->getInbox($_SESSION['user_id']);
$sent = $msg->getSent($_SESSION['user_id']);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Messages</title>
<link rel="stylesheet" href="/tutorlink/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:1000px;margin:90px auto;padding:20px">
    <h2>Inbox</h2>
    <?php if(!empty($_SESSION['flash_success'])): ?><div style="background:#d4edda;padding:10px;border-radius:6px;color:#155724;margin-bottom:12px"><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div><?php endif; ?>
    <p><a class="btn-primary" href="compose.php">Compose</a> <a class="btn-outline" href="inbox.php?view=sent">View Sent</a></p>
    <?php if (empty($inbox)): ?><p>No messages.</p><?php else: ?>
        <table style="width:100%;border-collapse:collapse">
            <thead><tr><th>From</th><th>Subject</th><th>Date</th><th></th></tr></thead>
            <tbody>
            <?php foreach($inbox as $m): ?>
                <tr style="border-bottom:1px solid #eee">
                    <td><?= htmlspecialchars($m['sender_name']) ?></td>
                    <td><?= htmlspecialchars($m['subject'] ?: '(no subject)') ?></td>
                    <td><?= htmlspecialchars($m['created_at']) ?></td>
                    <td><a href="view.php?id=<?= $m['id'] ?>">Open</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
