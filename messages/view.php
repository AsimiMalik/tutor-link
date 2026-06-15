<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: ../messages/inbox.php'); exit(); }
require_once __DIR__ . '/../classes/Message.php';
$msg = new Message();
$m = $msg->getById($id,$_SESSION['user_id']);
if (!$m) { $_SESSION['flash_error']='Message not found or access denied'; header('Location: ../messages/inbox.php'); exit(); }
// mark read if receiver
if ((int)$m['receiver_id'] === (int)$_SESSION['user_id']) {
	$msg->markRead($id,$_SESSION['user_id']);
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>View Message</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
<link rel="stylesheet" href="/brilliance/assets/css/messages.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page">
	<div class="container" style="max-width:900px">
		<h2>Message</h2>

		<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
			<div style="color:var(--text)">From: <strong><?= htmlspecialchars($m['sender_name'] ?? 'User') ?></strong></div>
			<div style="color:var(--text);font-size:13px">Sent: <?= htmlspecialchars($m['created_at']) ?></div>
		</div>

		<div class="glass-card">
			<div style="display:flex;gap:12px;align-items:flex-start;margin-bottom:12px">
				<div style="flex:0 0 72px">
					<?php if (!empty($m['sender_pic'])): ?>
						<img src="/brilliance/uploads/<?= htmlspecialchars($m['sender_pic']) ?>" class="message-avatar" alt="avatar">
					<?php else: ?>
						<img src="/brilliance/assets/images/logo.png" class="message-avatar" alt="avatar">
					<?php endif; ?>
				</div>
				<div style="flex:1">
					<div style="font-weight:700;font-size:16px;">Subject: <?= htmlspecialchars($m['subject'] ?: '(no subject)') ?></div>
					<div style="margin-top:12px;white-space:pre-wrap;line-height:1.6;color:var(--text)"><?= nl2br(htmlspecialchars($m['body'])) ?></div>
				</div>
			</div>

			<div style="display:flex;gap:8px;justify-content:flex-end">
				<a class="btn-outline" href="inbox.php">Back to Inbox</a>
				<a class="btn-primary" href="compose.php?receiver_id=<?= ((int)$m['sender_id'] === $_SESSION['user_id'] ? $m['receiver_id'] : $m['sender_id']) ?>">Reply</a>
				<a class="btn-outline" href="chat.php?user=<?= ((int)$m['sender_id'] === $_SESSION['user_id'] ? $m['receiver_id'] : $m['sender_id']) ?>">Open Chat</a>
			</div>
		</div>
	</div>
</div>
</body>
</html>

