
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$loggedIn = !empty($_SESSION['user_id']);
$unread = 0;
if ($loggedIn) {
    try {
        require_once __DIR__ . '/../classes/database.php';
        $db = new Database(); $conn = $db->connect();
        $stmt = $conn->prepare('SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0 AND deleted_by_receiver = 0');
        $stmt->execute([$_SESSION['user_id']]);
        $unread = (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        $unread = 0;
    }
}
?>

<nav class="navbar">

  <div class="container nav-container">

    <a href="/brilliance/index.php" class="logo">
      <img src="/brilliance/assets/images/logo.png" alt="Brilliance Logo">
      <span>Brilliance</span>
    </a>

    <ul class="nav-links">
      <li><a href="/brilliance/index.php">Home</a></li>
      <li><a href="/brilliance/view-tutors.php">Find Tutors</a></li>
      <li><a href="/brilliance/auth/register.php">Become a Tutor</a></li>
      <li><a href="/brilliance/about.php">About</a></li>
      <li><a href="/brilliance/contact.php">Contact</a></li>
      <?php if ($loggedIn): ?>
        <li><a href="/brilliance/messages/inbox.php">Messages<?php if ($unread>0) echo ' <span class="badge">'.htmlspecialchars($unread).'</span>'; ?></a></li>
      <?php endif; ?>
    </ul>

    <div class="nav-buttons">
      <?php if (!$loggedIn): ?>
        <a href="/brilliance/auth/login.php" class="btn-outline">Login</a>
        <a href="/brilliance/auth/register.php" class="btn-primary">Get Started</a>
      <?php else: ?>
        <a href="/brilliance/messages/inbox.php" class="btn-outline">Inbox<?php if ($unread>0) echo ' <span class="badge">'.htmlspecialchars($unread).'</span>'; ?></a>
        <a href="/brilliance/auth/logout.php" class="btn-primary">Logout</a>
      <?php endif; ?>
    </div>

  </div>

</nav>
