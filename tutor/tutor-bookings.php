<?php
session_start();

require_once __DIR__ . '/../includes/csrf.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor'){
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../classes/Database.php';
$db = new Database();
$conn = $db->connect();

$tutor_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT b.*, p.name AS subject_name, u.fullname AS parent_name, u.email AS parent_email FROM bookings b JOIN subjects p ON b.subject_id = p.id JOIN users u ON b.parent_id = u.id WHERE b.tutor_id = ? ORDER BY b.id DESC");
$stmt->execute([$tutor_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Students / Bookings | Brilliance</title>
    <link rel="stylesheet" href="/brilliance/assets/css/style.css">
    <style>
        .page-container{max-width:900px;margin:40px auto;margin-top:120px}
        .card{background:#fff;padding:16px;border-radius:10px;margin-bottom:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06)}
        .status{padding:6px 10px;border-radius:6px;font-weight:600}
        .pending{background:#fff3cd;color:#856404}
        .accepted{background:#d4edda;color:#155724}
        .declined{background:#f8d7da;color:#721c24}
        .cancelled{background:#f8d7da;color:#721c24}
        .completed{background:#d1ecf1;color:#0c5460}
        .actions{margin-top:10px}
        .actions form{display:inline-block;margin-right:8px}
        .btn-outline{background:transparent;border:2px solid #2563eb;padding:8px 12px;border-radius:8px;color:#2563eb;cursor:pointer}
        .btn-primary{background:#2563eb;color:#fff;padding:8px 12px;border-radius:8px;border:none;cursor:pointer}
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/tutor-navbar.php'; ?>
<div class="page-container">
    <h2>Bookings & Requests</h2>

    <?php if(!empty($_SESSION['flash_success'])): ?>
        <div style="background:#d4edda;padding:10px;border-radius:6px;margin-bottom:12px;color:#155724"><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>

    <?php if(!empty($_SESSION['flash_error'])): ?>
        <div style="background:#f8d7da;padding:10px;border-radius:6px;margin-bottom:12px;color:#721c24"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>

    <?php if(empty($bookings)): ?>
        <p>No bookings yet.</p>
    <?php endif; ?>

    <?php foreach($bookings as $b): ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($b['parent_name']); ?> <small>&lt;<?php echo htmlspecialchars($b['parent_email']); ?>&gt;</small></h3>
            <p><b>Subject:</b> <?php echo htmlspecialchars($b['subject_name']); ?></p>
            <p><b>When:</b> <?php echo htmlspecialchars($b['session_date']); ?></p>
            <!-- Notes column removed from schema; not shown -->
            <?php
                $rawStatus = isset($b['status']) ? trim(strtolower($b['status'])) : '';
                if ($rawStatus === '') $rawStatus = 'pending';
            ?>
            <p><b>Status:</b> <span class="status <?php echo htmlspecialchars($rawStatus); ?>"><?php echo strtoupper($rawStatus); ?></span></p>

            <?php if($rawStatus === 'pending'): ?>
                <div class="actions">
                    <form method="POST" action="../processes/update-booking-status.php">
                        <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="accept">
                        <button class="btn-primary" type="submit">Accept</button>
                    </form>

                    <form method="POST" action="../processes/update-booking-status.php">
                        <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="decline">
                        <button class="btn-outline" type="submit">Decline</button>
                    </form>
                </div>
            <?php elseif($rawStatus === 'accepted' || $rawStatus === 'declined'): ?>
                <div class="actions">
                    <form method="POST" action="../processes/update-booking-status.php">
                        <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="toggle">
                        <button class="btn-primary" type="submit">Edit</button>
                    </form>

                    <?php if ($rawStatus === 'accepted'): ?>
                    <form method="POST" action="../processes/update-booking-status.php">
                        <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="complete">
                        <button class="btn-primary" type="submit">Mark Completed</button>
                    </form>
                    <?php endif; ?>

                    <form method="POST" action="../processes/update-booking-status.php">
                        <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="cancel">
                        <button class="btn-outline" type="submit">Cancel</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

</div>
</body>
</html>
