<?php
session_start();

require_once __DIR__ . '/../includes/csrf.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../classes/Database.php';
$db = new Database();
$conn = $db->connect();

$booking_id = $_GET['id'] ?? null;
if (!$booking_id) {
    die('Booking not specified');
}

$stmt = $conn->prepare("SELECT b.*, u.fullname AS tutor_name, tp.profile_pic AS tutor_pic, p.fullname AS parent_name, s.name AS subject_name FROM bookings b JOIN users u ON b.tutor_id = u.id LEFT JOIN tutor_profile tp ON u.id = tp.user_id JOIN users p ON b.parent_id = p.id JOIN subjects s ON b.subject_id = s.id WHERE b.id = ? LIMIT 1");
$stmt->execute([$booking_id]);
$b = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$b) {
    die('Booking not found');
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking #<?= htmlspecialchars($b['id']) ?> | Brilliance</title>
    <link rel="stylesheet" href="/brilliance/assets/css/style.css">
    <style>
        .page{max-width:900px;margin:90px auto;padding:20px}
        .card{background:#fff;padding:18px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.06)}
        .actions{margin-top:16px}
        .muted{color:#64748b}
    </style>
</head>
<body>
<?php
// include role navbar
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'parent') include __DIR__ . '/../includes/parent-navbar.php';
    elseif ($_SESSION['role'] === 'tutor') include __DIR__ . '/../includes/tutor-navbar.php';
    else include __DIR__ . '/../includes/navbar.php';
} else {
    include __DIR__ . '/../includes/navbar.php';
}
?>

<div class="page">
    <?php if(!empty($_SESSION['flash_success'])): ?>
        <div style="background:#d4edda;padding:10px;border-radius:6px;margin-bottom:12px;color:#155724"><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>

    <?php if(!empty($_SESSION['flash_error'])): ?>
        <div style="background:#f8d7da;padding:10px;border-radius:6px;margin-bottom:12px;color:#721c24"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>
    <div class="card">
        <h2>Booking Details</h2>
        <p class="muted">Booking ID: <?= htmlspecialchars($b['id']) ?></p>

        <h3>Subject: <?= htmlspecialchars($b['subject_name']) ?></h3>
        <p><b>Tutor:</b> <?= htmlspecialchars($b['tutor_name']) ?></p>
        <p><b>Parent:</b> <?= htmlspecialchars($b['parent_name']) ?></p>
        <p><b>When:</b> <?= htmlspecialchars($b['session_date']) ?></p>
        <!-- Notes column removed from schema; no display -->

        <p><b>Status:</b> <span class="status"><?= strtoupper(htmlspecialchars($b['status'] ?: 'pending')) ?></span></p>

        <div class="actions">
            <?php if ($_SESSION['role'] === 'parent' && $_SESSION['user_id'] == $b['parent_id']): ?>
                <a class="btn-outline" href="update-booking.php?id=<?= $b['id'] ?>">Edit</a>
                <a class="btn-secondary" href="cancel-booking.php?id=<?= $b['id'] ?>" onclick="return confirm('Cancel this booking?')">Cancel Booking</a>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'tutor' && $_SESSION['user_id'] == $b['tutor_id']): ?>
                <?php if (trim(strtolower($b['status'] ?: '')) === 'pending' || $b['status'] === NULL): ?>
                    <form style="display:inline-block" method="post" action="../processes/update-booking-status.php">
                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="accept">
                        <button class="btn-primary" type="submit">Accept</button>
                    </form>
                    <form style="display:inline-block" method="post" action="../processes/update-booking-status.php">
                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="decline">
                        <button class="btn-outline" type="submit">Decline</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>
