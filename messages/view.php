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
<div class="message-page">
	<h2>Message</h2>
		<p>
			<a class="btn-outline" href="inbox.php">Back to Inbox</a>
			<a class="btn-primary" href="compose.php?receiver_id=<?= ((int)$m['sender_id'] === $_SESSION['user_id'] ? $m['receiver_id'] : $m['sender_id']) ?>">Reply</a>
			<a class="btn-outline" href="chat.php?user=<?= ((int)$m['sender_id'] === $_SESSION['user_id'] ? $m['receiver_id'] : $m['sender_id']) ?>">Open Chat</a>
		</p>

	<div class="message-detail">
		<p><strong>From:</strong> <?= htmlspecialchars($m['sender_name'] ?? 'User') ?></p>
		<p><strong>To:</strong> <?php if (isset($m['receiver_id'])) echo htmlspecialchars($m['receiver_id']); else echo '-'; ?></p>
		<p><strong>Subject:</strong> <?= htmlspecialchars($m['subject'] ?: '(no subject)') ?></p>
		<p style="color:#666;font-size:0.9em">Sent: <?= htmlspecialchars($m['created_at']) ?></p>
		<hr>
		<div style="white-space:pre-wrap;"><?= nl2br(htmlspecialchars($m['body'])) ?></div>
	</div>
</div>
</body>
</html>

