<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
$other = isset($_GET['user']) ? (int)$_GET['user'] : 0;
if ($other <= 0) { header('Location: inbox.php'); exit(); }
require_once __DIR__ . '/../classes/Database.php';
$db = new Database(); $conn = $db->connect();
// lookup name
$stmt = $conn->prepare('SELECT fullname FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$other]);
$other_row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$other_row) { header('Location: inbox.php'); exit(); }
$other_name = $other_row['fullname'];
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Chat with <?php echo htmlspecialchars($other_name); ?></title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
<link rel="stylesheet" href="/brilliance/assets/css/messages.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="message-page" id="chat-root" data-other="<?php echo $other; ?>">
  <h2>Chat with <?php echo htmlspecialchars($other_name); ?></h2>

  <div id="messageList" class="message-list" aria-live="polite"></div>

  <div class="message-form" style="margin-top:12px">
    <form id="chatForm">
      <?php require_once __DIR__ . '/../includes/csrf.php'; ?>
      <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
      <input type="hidden" name="receiver_id" value="<?php echo $other; ?>">
      <label for="messageBody">Message</label>
      <textarea id="messageBody" name="body" required></textarea>
      <div class="message-actions">
        <button class="btn-primary" type="submit">Send</button>
        <a class="btn-outline" href="inbox.php">Back to Inbox</a>
      </div>
    </form>
  </div>
</div>
<script src="/brilliance/assets/js/messages.js"></script>
<script>window.USER_ID = <?php echo (int)($_SESSION['user_id'] ?? 0); ?>;</script>
</body>
</html>
