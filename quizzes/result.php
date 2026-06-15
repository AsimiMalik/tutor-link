<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Quiz.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: index.php'); exit(); }
$q = new Quiz();
$attempt = $q->getAttempt($id);
if (!$attempt) { $_SESSION['flash_error']='Attempt not found'; header('Location: index.php'); exit(); }
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Result</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:900px;margin:90px auto;padding:20px">
  <div class="quiz-card" style="margin-bottom:14px;padding:18px;text-align:center">
    <div style="font-size:40px;color:var(--primary);margin-bottom:6px">🏆</div>
    <h2 style="margin:6px 0">Result: <?= htmlspecialchars($attempt['title'] ?? 'Quiz') ?></h2>
    <p style="font-size:20px;margin-top:6px">Score: <strong><?= htmlspecialchars($attempt['score']) ?>%</strong></p>
  </div>
  <h3 style="margin-top:16px">Answers</h3>
  <?php foreach($attempt['answers'] as $a): ?>
    <div class="quiz-question">
      <div class="q-title"><?= htmlspecialchars($a['question_text']) ?></div>
      <?php if ($a['choice_id']): ?>
        <div class="choices">Selected: <?= htmlspecialchars($a['label'] ?? '') ?> — <?php if ($a['is_correct']): ?><span class="unread-badge">Correct</span><?php else: ?><span style="color:#c00">Incorrect</span><?php endif; ?></div>
      <?php else: ?>
        <div class="choices">Answer: <?= nl2br(htmlspecialchars($a['answer_text'] ?? '')) ?></div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>
</body>
</html>
