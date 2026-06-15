<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'tutor') { header('Location: ../auth/login.php'); exit(); }
$db = new Database(); $conn = $db->connect();
$tutor_id = $_SESSION['user_id'];

// filters
$filter_quiz = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
$filter_student = isset($_GET['q']) ? trim($_GET['q']) : '';

$attempts = [];
$quizzes = [];
try{
  // load quizzes created by this tutor for filter
  $qstmt = $conn->prepare('SELECT id, title FROM quizzes WHERE created_by = ? ORDER BY title');
  $qstmt->execute([$tutor_id]);
  $quizzes = $qstmt->fetchAll(PDO::FETCH_ASSOC);

  $sql = 'SELECT a.id as attempt_id, a.score, a.started_at, a.completed_at, q.title as quiz_title, u.fullname as student_name FROM attempts a JOIN quizzes q ON a.quiz_id = q.id JOIN users u ON a.user_id = u.id WHERE q.created_by = ?';
  $params = [$tutor_id];
  if ($filter_quiz) { $sql .= ' AND q.id = ?'; $params[] = $filter_quiz; }
  if ($filter_student !== '') { $sql .= ' AND u.fullname LIKE ?'; $params[] = '%'.$filter_student.'%'; }
  $sql .= ' ORDER BY a.completed_at DESC';

  $stmt = $conn->prepare($sql);
  $stmt->execute($params);
  $attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e){ $attempts = []; }
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Quiz Attempts</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page">
  <div class="container" style="max-width:1100px;margin:30px auto;padding:20px">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <div>
        <h2 style="margin:0">Student Attempts</h2>
        <div style="color:var(--text);font-size:14px">Recent quiz attempts for quizzes you created</div>
      </div>
      <div>
        <a href="/brilliance/quizzes/index.php" class="btn-outline">All Quizzes</a>
      </div>
    </div>

    <form method="get" style="display:flex;gap:8px;align-items:center;margin-bottom:14px;flex-wrap:wrap">
      <label style="font-weight:600;color:var(--text)">Filter:</label>
      <select name="quiz_id" style="padding:8px;border-radius:8px;border:1px solid var(--border)">
        <option value="">All quizzes</option>
        <?php foreach($quizzes as $qq): ?>
          <option value="<?= (int)$qq['id'] ?>" <?= ($filter_quiz == $qq['id']) ? 'selected' : '' ?>><?= htmlspecialchars($qq['title']) ?></option>
        <?php endforeach; ?>
      </select>
      <input name="q" placeholder="Student name" value="<?= htmlspecialchars($filter_student) ?>" style="padding:8px;border-radius:8px;border:1px solid var(--border)">
      <button class="btn-primary" type="submit">Apply</button>
      <a class="btn-outline" href="export-attempts.php?<?= http_build_query(['quiz_id'=>$filter_quiz,'q'=>$filter_student]) ?>">Export CSV</a>
    </form>

    <?php if (empty($attempts)): ?>
      <p>No attempts yet.</p>
    <?php else: ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:18px">
      <?php foreach($attempts as $a): ?>
        <div class="quiz-card">
          <div class="card-header">
            <div class="card-icon"><svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg></div>
            <div>
              <div style="font-weight:700;font-size:16px"><?= htmlspecialchars($a['quiz_title']) ?></div>
              <div style="font-size:13px;color:var(--text)">by <?= htmlspecialchars($a['student_name']) ?> • <?= htmlspecialchars($a['completed_at']) ?></div>
            </div>
          </div>
          <p style="margin-top:12px;color:var(--text)">Score: <strong><?= htmlspecialchars($a['score']) ?>%</strong></p>
          <div class="quiz-actions" style="margin-top:12px">
            <a class="btn-primary" href="view-attempt.php?id=<?= $a['attempt_id'] ?>">View Details</a>
            <a class="btn-outline" href="mailto:?subject=Quiz%20Attempt%20<?= rawurlencode($a['quiz_title']) ?>&body=Hi%20<?= rawurlencode($a['student_name']) ?>,%0A%0AI noticed your quiz attempt...">Message Student</a>
          </div>
        </div>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
