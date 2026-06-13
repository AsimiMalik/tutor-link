<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
require_once __DIR__ . '/../classes/Message.php';
$msg = new Message();
$m = $msg->getById($id,$_SESSION['user_id']);
if (!$m) { die('Message not found'); }
// mark read if receiver
if ($m['receiver_id'] == $_SESSION['user_id']) $msg->markRead($id,$_SESSION['user_id']);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>View Message</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:800px;margin:90px auto;padding:20px">
    <h2><?= htmlspecialchars($m['subject'] ?: '(no subject)') ?></h2>
    <p><b>From:</b> <?= htmlspecialchars($m['sender_id']) ?> <b>To:</b> <?= htmlspecialchars($m['receiver_id']) ?> <b>Date:</b> <?= htmlspecialchars($m['created_at']) ?></p>
    <hr>
    <div style="white-space:pre-wrap"><?= htmlspecialchars($m['body']) ?></div>
    <p style="margin-top:18px"><a href="inbox.php">Back to Inbox</a></p>
</div>
</body>
</html>
