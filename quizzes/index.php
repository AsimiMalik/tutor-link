<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Quiz.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
$q = new Quiz();
$list = $q->getAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Quizzes</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:900px;margin:90px auto;padding:20px">
  <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
    <div style="width:56px;height:56px;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;font-weight:700">Q</div>
    <div>
      <h2 style="margin:0">Quizzes</h2>
      <div style="color:var(--text);font-size:14px">Take short assessments to help students learn — tutor-created and AI-assisted.</div>
    </div>
  </div>
  <?php if (empty($list)): ?>
    <p>No quizzes available.</p>
  <?php else: ?>
    <div class="quiz-grid">
    <?php foreach($list as $quiz): ?>
      <div class="quiz-card">
        <h3><?= htmlspecialchars($quiz['title']) ?></h3>
        <p><?= htmlspecialchars(strlen($quiz['description'])>180 ? substr($quiz['description'],0,180).'...' : $quiz['description']) ?></p>
        <div class="quiz-meta">
          <span>Created: <?= htmlspecialchars(date('Y-m-d', strtotime($quiz['created_at']))) ?></span>
          <?php if (!empty($quiz['time_limit'])): ?><span>• <?= htmlspecialchars($quiz['time_limit']) ?> min</span><?php endif; ?>
        </div>
        <div class="quiz-actions">
          <a class="btn-outline" href="take.php?id=<?= $quiz['id'] ?>">Take Quiz</a>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
