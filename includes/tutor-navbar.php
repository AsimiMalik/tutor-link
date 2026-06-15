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
    } catch (Exception $e) { $unread = 0; }
}
?>

<nav class="navbar">

<div class="container nav-container">

    <a href="/brilliance/tutor/tutor-dashboard.php" class="logo">
        <img src="/brilliance/assets/images/logo.png" alt="Brilliance Logo">
        <span>Brilliance</span>
    </a>

    <ul class="nav-links">
        <li><a href="/brilliance/tutor/tutor-dashboard.php">Dashboard</a></li>
        <li><a href="/brilliance/tutor/tutor-bookings.php">My Students</a></li>
        <li><a href="/brilliance/tutor/tutor-bookings.php">Bookings</a></li>
        <li><a href="/brilliance/messages/inbox.php">Messages<?php if (!empty($unread)) echo ' <span class="badge">'.htmlspecialchars($unread).'</span>'; ?></a></li>
    </ul>

    <div class="nav-buttons">
        <a href="/brilliance/tutor/tutor-profile.php" class="btn-outline">
            <i class="fa-solid fa-user"></i> Profile
        </a>

        <a href="/brilliance/auth/logout.php" class="btn-primary">
            Logout
        </a>
    </div>

        <button class="nav-toggle" aria-label="Toggle menu">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>

        <script>
        document.addEventListener('DOMContentLoaded', function(){
            const toggle = document.querySelector('.nav-toggle');
            const links = document.querySelector('.nav-links');
            if (!toggle || !links) return;
            toggle.addEventListener('click', function(){ links.style.display = (links.style.display === 'flex' ? 'none' : 'flex'); });
            document.addEventListener('click', function(e){ if (!links.contains(e.target) && !toggle.contains(e.target) && window.innerWidth <= 1000) links.style.display = 'none'; });
        });
        </script>

</div>

</nav>