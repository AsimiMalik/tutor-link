<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'tutor') {
    header('Location: ../auth/login.php'); exit();
}

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Review.php';

$db = new Database(); $conn = $db->connect();
$tutor_id = $_SESSION['user_id'];

// Total lessons completed (bookings with status = 'completed')
$stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE tutor_id = ? AND status = 'completed'");
$stmt->execute([$tutor_id]);
$total_completed = (int)$stmt->fetchColumn();

// Total bookings (requests)
$stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE tutor_id = ?");
$stmt->execute([$tutor_id]);
$total_requests = (int)$stmt->fetchColumn();

// Accepted bookings
$stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE tutor_id = ? AND status = 'accepted'");
$stmt->execute([$tutor_id]);
$accepted = (int)$stmt->fetchColumn();

// Acceptance rate
$acceptance_rate = $total_requests > 0 ? round(($accepted / $total_requests) * 100,2) : 0;

// Average rating: prefer stored value in tutor_profile
$stmt = $conn->prepare("SELECT rating_avg, total_reviews FROM tutor_profile WHERE user_id = ? LIMIT 1");
$stmt->execute([$tutor_id]);
$prof = $stmt->fetch(PDO::FETCH_ASSOC);
$avg_rating = $prof && $prof['rating_avg'] !== null ? number_format((float)$prof['rating_avg'],2) : '0.00';
$total_reviews = $prof ? (int)$prof['total_reviews'] : 0;

// Attendance summary from session_reports
$stmt = $conn->prepare("SELECT attendance, COUNT(*) as cnt FROM session_reports WHERE tutor_id = ? GROUP BY attendance");
$stmt->execute([$tutor_id]);
$attendanceRows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$present = isset($attendanceRows['present']) ? (int)$attendanceRows['present'] : 0;
$late = isset($attendanceRows['late']) ? (int)$attendanceRows['late'] : 0;
$absent = isset($attendanceRows['absent']) ? (int)$attendanceRows['absent'] : 0;

// Response rate: compute parents contacted and parents the tutor replied to
$stmt = $conn->prepare("SELECT COUNT(DISTINCT m.sender_id) FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.receiver_id = ? AND u.role = 'parent'");
$stmt->execute([$tutor_id]);
$parents_contacted = (int)$stmt->fetchColumn();

$stmt = $conn->prepare("SELECT COUNT(DISTINCT mr.receiver_id) FROM messages mr JOIN users u ON mr.receiver_id = u.id WHERE mr.sender_id = ? AND u.role = 'parent' AND EXISTS (SELECT 1 FROM messages mp WHERE mp.sender_id = u.id AND mp.receiver_id = ?)");
$stmt->execute([$tutor_id, $tutor_id]);
$parents_replied = (int)$stmt->fetchColumn();

$msgs_received = $parents_contacted; // useful display
$msgs_sent = $parents_replied;
$response_rate = $parents_contacted > 0 ? round(($parents_replied / $parents_contacted) * 100,2) : 0;

// Recent session reports
$stmt = $conn->prepare("SELECT sr.*, u.fullname AS parent_name FROM session_reports sr JOIN users u ON sr.parent_id = u.id WHERE sr.tutor_id = ? ORDER BY sr.created_at DESC LIMIT 10");
$stmt->execute([$tutor_id]);
$recent_reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tutor Performance — Brilliance</title>
    <link rel="stylesheet" href="/brilliance/assets/css/style.css">
    <style>
        .container{max-width:1000px;margin:120px auto}
        .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
        .card{background:#fff;padding:16px;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.06)}
        .list{margin-top:18px}
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/tutor-navbar.php'; ?>
<div class="container">
    <h2>Performance Dashboard</h2>
    <div class="grid">
        <div class="card">
            <h3>Total Lessons Completed</h3>
            <div style="font-size:28px;font-weight:700"><?php echo $total_completed; ?></div>
        </div>
        <div class="card">
            <h3>Average Rating</h3>
            <div style="font-size:28px;font-weight:700"><?php echo $avg_rating; ?></div>
        </div>
        <div class="card">
            <h3>Successful Bookings</h3>
            <div style="font-size:28px;font-weight:700"><?php echo $accepted; ?></div>
        </div>
        <div class="card">
            <h3>Acceptance Rate</h3>
            <div style="font-size:28px;font-weight:700"><?php echo $acceptance_rate; ?>%</div>
        </div>
    </div>

    <div style="margin-top:20px" class="grid">
        <div class="card">
            <h3>Attendance</h3>
            <p>Present: <?php echo $present; ?></p>
            <p>Late: <?php echo $late; ?></p>
            <p>Absent: <?php echo $absent; ?></p>
            <div style="margin-top:12px"><canvas id="attendanceChart" width="300" height="150"></canvas></div>
        </div>
        <div class="card">
            <h3>Response Rate</h3>
            <p>Messages received: <?php echo $msgs_received; ?></p>
            <p>Messages sent: <?php echo $msgs_sent; ?></p>
            <p>Response rate: <?php echo $response_rate; ?>%</p>
        </div>
        <div class="card">
            <h3>Requests</h3>
            <p>Total requests: <?php echo $total_requests; ?></p>
            <p>Accepted: <?php echo $accepted; ?></p>
        </div>
        <div class="card">
            <h3>Recent Reports</h3>
            <div class="list">
                <?php if (empty($recent_reports)): ?>
                    <p>No recent reports.</p>
                <?php else: foreach($recent_reports as $r): ?>
                    <div style="border-bottom:1px solid #eee;padding:8px 0">
                        <strong><?php echo htmlspecialchars($r['parent_name']); ?></strong>
                        <div style="font-size:12px;color:#666"><?php echo htmlspecialchars($r['created_at']); ?></div>
                        <div>Attendance: <?php echo htmlspecialchars($r['attendance']); ?> — Rating: <?php echo htmlspecialchars($r['rating'] ?? '—'); ?></div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
            <div style="margin-top:12px"><canvas id="ratingChart" width="300" height="150"></canvas></div>
        </div>
    </div>

</div>
    <!-- Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const attendanceData = {
            labels: ['Present','Late','Absent'],
            datasets: [{
                label: 'Attendance',
                data: [<?php echo $present; ?>, <?php echo $late; ?>, <?php echo $absent; ?>],
                backgroundColor: ['#10B981','#F59E0B','#EF4444']
            }]
        };

        const ctx1 = document.createElement('canvas');
        document.querySelector('.card:nth-child(1)').appendChild(ctx1);
        new Chart(ctx1.getContext('2d'), { type: 'bar', data: attendanceData, options: { responsive: true } });

        // Ratings over recent reports
        const ratingLabels = [<?php
            $labels = [];
            $vals = [];
            foreach($recent_reports as $r){
                $labels[] = '"'.addslashes(substr($r['created_at'],0,10)).'"';
                $vals[] = isset($r['rating']) && $r['rating'] !== null ? (int)$r['rating'] : 'null';
            }
            echo implode(',', $labels);
        ?>];
        const ratingVals = [<?php echo implode(',', $vals); ?>];

        if (ratingLabels.length > 0) {
            const ctx2 = document.createElement('canvas');
            document.querySelector('.card:nth-child(4)').appendChild(ctx2);
            new Chart(ctx2.getContext('2d'), {
                type: 'line', data: { labels: ratingLabels, datasets: [{ label: 'Rating', data: ratingVals, borderColor: '#2563eb', fill:false }] }, options: { responsive: true, scales: { y: { min:0, max:5 } } }
            });
        }
    </script>
</body>
</html>



