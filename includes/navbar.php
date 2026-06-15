
<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// If a specific role navbar exists, delegate to it so role-specific links show.
if (!empty($_SESSION['role'])) {
  $role = $_SESSION['role'];
  if ($role === 'tutor' && file_exists(__DIR__ . '/tutor-navbar.php')) {
    include __DIR__ . '/tutor-navbar.php';
    return;
  }
  if ($role === 'parent' && file_exists(__DIR__ . '/parent-navbar.php')) {
    include __DIR__ . '/parent-navbar.php';
    return;
  }
  // future: handle admin etc.
}

$loggedIn = !empty($_SESSION['user_id']);
$unread = 0;
if ($loggedIn) {
  try {
    require_once __DIR__ . '/../classes/Database.php';
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
      <li><a href="/brilliance/quizzes/index.php">Quizzes</a></li>
      <li><a href="/brilliance/auth/register.php">Become a Tutor</a></li>
      <li><a href="/brilliance/about.php">About</a></li>
      <li><a href="/brilliance/contact.php">Contact</a></li>
      <?php if ($loggedIn && (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin')): ?>
        <li><a href="/brilliance/admin/dashboard.php">Admin</a></li>
      <?php endif; ?>
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

    <button class="nav-toggle" aria-label="Toggle menu">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>

  </div>

</nav>

<script>
// navbar toggle
document.addEventListener('DOMContentLoaded', function(){
  const toggle = document.querySelector('.nav-toggle');
  const links = document.querySelector('.nav-links');
  if (!toggle || !links) return;
  toggle.addEventListener('click', function(){
    if (links.style.display === 'flex' || links.style.display === 'block') {
      links.style.display = 'none';
    } else {
      links.style.display = 'flex';
    }
  });
  // close when clicking outside on small screens
  document.addEventListener('click', function(e){
    if (!links.contains(e.target) && !toggle.contains(e.target) && window.innerWidth <= 1000) {
      links.style.display = 'none';
    }
  });
});
</script>
