<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'parent') {
    header('Location: ../auth/login.php'); exit();
}

require_once __DIR__ . '/../classes/Database.php';

$db = new Database(); $conn = $db->connect();
$parent_id = $_SESSION['user_id'];

// Attendance summary for this parent's students
$stmt = $conn->prepare("SELECT attendance, COUNT(*) as cnt FROM session_reports WHERE parent_id = ? GROUP BY attendance");
$stmt->execute([$parent_id]);
$attendanceRows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$present = isset($attendanceRows['present']) ? (int)$attendanceRows['present'] : 0;
$late = isset($attendanceRows['late']) ? (int)$attendanceRows['late'] : 0;
$absent = isset($attendanceRows['absent']) ? (int)$attendanceRows['absent'] : 0;

// Average rating across tutors for this parent's reports
$stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM session_reports WHERE parent_id = ? AND rating IS NOT NULL");
$stmt->execute([$parent_id]);
$avg_row = $stmt->fetch(PDO::FETCH_ASSOC);
$avg_rating = $avg_row && $avg_row['avg_rating'] !== null ? number_format($avg_row['avg_rating'],2) : '—';

// Per-tutor summary
$stmt = $conn->prepare("SELECT t.tutor_id, u.fullname, AVG(t.rating) as avg_rating, COUNT(*) as sessions FROM session_reports t JOIN users u ON t.tutor_id = u.id WHERE t.parent_id = ? GROUP BY t.tutor_id, u.fullname ORDER BY avg_rating DESC");
$stmt->execute([$parent_id]);
$perTutor = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent reports
$stmt = $conn->prepare("SELECT sr.*, u.fullname AS tutor_name FROM session_reports sr JOIN users u ON sr.tutor_id = u.id WHERE sr.parent_id = ? ORDER BY sr.created_at DESC LIMIT 20");
$stmt->execute([$parent_id]);
$recent = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Progress — Brilliance</title>
    <link rel="stylesheet" href="/brilliance/assets/css/style.css">
    <style>.container{max-width:1000px;margin:110px auto}.card{background:#fff;padding:16px;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.06);margin-bottom:12px}</style>
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container">
    <h2>Student Progress & Attendance</h2>

    <div class="card">
        <h3>Attendance Summary</h3>
        <p>Present: <?php echo $present; ?> — Late: <?php echo $late; ?> — Absent: <?php echo $absent; ?></p>
        <div style="margin-top:12px"><canvas id="parentAttendance" width="400" height="180"></canvas></div>
    </div>

    <div class="card">
        <h3>Average Performance</h3>
        <p>Average reported rating: <?php echo $avg_rating; ?></p>
    </div>

    <div class="card">
        <h3>Per-Tutor Summary</h3>
        <?php if (empty($perTutor)): ?>
            <p>No tutor sessions yet.</p>
        <?php else: ?>
            <ul>
                <?php foreach($perTutor as $p): ?>
                    <li><strong><?php echo htmlspecialchars($p['fullname']); ?></strong> — Avg Rating: <?php echo $p['avg_rating'] ? number_format($p['avg_rating'],2) : '—'; ?> — Sessions: <?php echo (int)$p['sessions']; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Recent Session Reports</h3>
        <?php if (empty($recent)): ?>
            <p>No reports yet.</p>
        <?php else: foreach($recent as $r): ?>
            <div style="border-bottom:1px solid #eee;padding:8px 0">
                <strong><?php echo htmlspecialchars($r['tutor_name']); ?></strong>
                <div style="font-size:12px;color:#666"><?php echo htmlspecialchars($r['created_at']); ?></div>
                <div>Topics: <?php echo htmlspecialchars(substr($r['topics'] ?? '',0,160)); ?></div>
                <div>Attendance: <?php echo htmlspecialchars($r['attendance']); ?> — Rating: <?php echo htmlspecialchars($r['rating'] ?? '—'); ?></div>
            </div>
        <?php endforeach; endif; ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('parentAttendance').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Present','Late','Absent'],
            datasets: [{ data: [<?php echo $present; ?>, <?php echo $late; ?>, <?php echo $absent; ?>], backgroundColor: ['#10B981','#F59E0B','#EF4444'] }]
        },
        options: { responsive: true }
    });
</script>
</body>
</html>
