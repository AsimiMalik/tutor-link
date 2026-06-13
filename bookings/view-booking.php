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

$stmt = $conn->prepare("SELECT b.*, u.fullname AS tutor_name, tp.profile_pic AS tutor_pic, p.fullname AS parent_name, pp.profile_pic AS parent_pic, pp.bio AS parent_bio, s.name AS subject_name FROM bookings b JOIN users u ON b.tutor_id = u.id LEFT JOIN tutor_profile tp ON u.id = tp.user_id JOIN users p ON b.parent_id = p.id LEFT JOIN parent_profile pp ON p.id = pp.user_id JOIN subjects s ON b.subject_id = s.id WHERE b.id = ? LIMIT 1");
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
        <div style="display:flex;gap:18px;flex-wrap:wrap;margin-top:12px">
            <div style="flex:1;min-width:220px;max-width:420px;background:#f9fbff;padding:12px;border-radius:10px;display:flex;gap:12px;align-items:center">
                <div style="width:84px;height:84px;border-radius:50%;overflow:hidden;flex-shrink:0;background:#fff">
                    <?php if(!empty($b['tutor_pic'])): ?>
                        <img src="/brilliance/uploads/<?php echo htmlspecialchars($b['tutor_pic']); ?>" alt="Tutor" style="width:100%;height:100%;object-fit:cover">
                    <?php else: ?>
                        <img src="/brilliance/assets/images/default-avatar.png" alt="Tutor" style="width:100%;height:100%;object-fit:cover">
                    <?php endif; ?>
                </div>
                <div>
                    <div style="font-weight:700">Tutor: <a href="/brilliance/tutor/view-tutor.php?id=<?= $b['tutor_id'] ?>"><?= htmlspecialchars($b['tutor_name']) ?></a></div>
                    <?php if(!empty($b['hourly_rate'])): ?><div class="muted">Rate: <?= htmlspecialchars($b['hourly_rate']) ?>/hr</div><?php endif; ?>
                </div>
            </div>

            <div style="flex:1;min-width:220px;max-width:420px;background:#f9fbff;padding:12px;border-radius:10px;display:flex;gap:12px;align-items:center">
                <div style="width:84px;height:84px;border-radius:50%;overflow:hidden;flex-shrink:0;background:#fff">
                    <?php if(!empty($b['parent_pic'])): ?>
                        <img src="/brilliance/uploads/<?php echo htmlspecialchars($b['parent_pic']); ?>" alt="Parent" style="width:100%;height:100%;object-fit:cover">
                    <?php else: ?>
                        <img src="/brilliance/assets/images/default-avatar.png" alt="Parent" style="width:100%;height:100%;object-fit:cover">
                    <?php endif; ?>
                </div>
                <div>
                    <div style="font-weight:700">Parent: <a href="/brilliance/parent/view-parent.php?id=<?= $b['parent_id'] ?>"><?= htmlspecialchars($b['parent_name']) ?></a></div>
                    <?php if(!empty($b['parent_bio'])): ?><div class="muted" style="max-width:320px"><?= htmlspecialchars(substr($b['parent_bio'],0,140)) ?><?php if(strlen($b['parent_bio'])>140) echo '...'; ?></div><?php endif; ?>
                </div>
            </div>
        </div>
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
