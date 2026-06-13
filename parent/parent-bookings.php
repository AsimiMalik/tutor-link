<?php
session_start();

/*
----------------------------------------------------
CHECK LOGIN + ROLE
Only parents can access this page
----------------------------------------------------
*/
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../auth/login.php");
    exit();
}

/*
----------------------------------------------------
CONNECT DATABASE
----------------------------------------------------
*/
require_once "../classes/Database.php";

$db = new Database();
$conn = $db->connect();

/*
----------------------------------------------------
GET LOGGED IN PARENT ID
----------------------------------------------------
*/
$parent_id = $_SESSION['user_id'];

/*
----------------------------------------------------
FETCH BOOKINGS
JOIN:
- users (to get tutor name)
- subjects (to get subject name)
----------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT 
        b.*,
        u.fullname AS tutor_name,
        s.name AS subject_name
    FROM bookings b
    JOIN users u ON b.tutor_id = u.id
    JOIN subjects s ON b.subject_id = s.id
    WHERE b.parent_id = ?
    ORDER BY b.id DESC
");

$stmt->execute([$parent_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings | Brilliance</title>

    <link rel="stylesheet" href="/brilliance/assets/css/style.css">

    <style>
        /* PAGE STYLE */
        body {
            font-family: Arial;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        .page-container {
            max-width: 800px;
            margin: auto;
            margin-top: 90px; /* space below fixed navbar */
        }

        h2 {
            text-align: center;
        }

        /* BOOKING CARD */
        .card {
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        /* STATUS BADGES (use same class names as tutor view) */
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            display: inline-block;
        }

        .pending { background: #fff3cd; color: #856404; }
        .accepted { background: #d4edda; color: #155724; }
        .declined { background: #f8d7da; color: #721c24; }
        .cancelled { background: #f8d7da; color: #721c24; }
        .completed { background: #d1ecf1; color: #0c5460; }
    </style>
</head>

<body>

<?php include __DIR__ . '/../includes/parent-navbar.php'; ?>

<div class="page-container">

    <h2>My Bookings</h2>

    <?php if(!empty($_SESSION['flash_success'])): ?>
        <div style="background:#d4edda;padding:10px;border-radius:6px;margin-bottom:12px;color:#155724"><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>

    <?php if(!empty($_SESSION['flash_error'])): ?>
        <div style="background:#f8d7da;padding:10px;border-radius:6px;margin-bottom:12px;color:#721c24"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>

    <!-- IF NO BOOKINGS -->
    <?php if (count($bookings) === 0): ?>
        <p style="text-align:center;">No bookings yet.</p>
    <?php endif; ?>

    <!-- LOOP BOOKINGS -->
    <?php foreach ($bookings as $b): ?>

        <div class="card">

            <!-- TUTOR NAME -->
            <h3><?= htmlspecialchars($b['tutor_name']) ?></h3>

            <!-- SUBJECT -->
            <p><b>Subject:</b> <?= htmlspecialchars($b['subject_name']) ?></p>

            <!-- DATE -->
            <p><b>Session Date:</b> <?= htmlspecialchars($b['session_date']) ?></p>

            <!-- STATUS -->
            <p>
                <b>Status:</b>
                <?php
                // normalize status and map to display classes/text
                $rawStatus = isset($b['status']) ? trim(strtolower($b['status'])) : '';
                if ($rawStatus === '') {
                    $rawStatus = 'pending';
                }

                $displayClass = 'pending';
                $displayText = strtoupper($rawStatus);

                // accept equivalent values
                if (in_array($rawStatus, ['accepted', 'accept', 'confirmed', 'confirmed_by_tutor'], true)) {
                    $displayClass = 'accepted';
                    $displayText = 'ACCEPTED';
                }

                // declined values
                if (in_array($rawStatus, ['declined', 'decline', 'rejected', 'rejected_by_tutor'], true)) {
                    $displayClass = 'declined';
                    $displayText = 'DECLINED';
                }

                // cancelled (explicit)
                if ($rawStatus === 'cancelled' || $rawStatus === 'canceled') {
                    $displayClass = 'cancelled';
                    $displayText = 'CANCELLED';
                }

                // completed
                if ($rawStatus === 'completed') {
                    $displayClass = 'completed';
                    $displayText = 'COMPLETED';
                }
                ?>
                <span class="status <?= $displayClass ?>">
                    <?= $displayText ?>
                </span>
                <?php if ($rawStatus === 'cancelled'): ?>
                    <div style="margin-top:8px">
                        <a class="btn-outline" href="../bookings/book-tutor.php?tutor_id=<?= urlencode($b['tutor_id']) ?>&subject_id=<?= urlencode($b['subject_id']) ?>">Rebook</a>
                    </div>
                <?php endif; ?>
            </p>

        </div>

    <?php endforeach; ?>

</div>

</body>
</html>