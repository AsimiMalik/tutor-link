<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'tutor') { header('Location: ../auth/login.php'); exit(); }
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: quiz-attempts.php'); exit(); }

$db = new Database(); $conn = $db->connect();
// fetch attempt and ensure tutor owns the quiz
$stmt = $conn->prepare('SELECT a.*, q.title as quiz_title, q.created_by, u.fullname as student_name FROM attempts a JOIN quizzes q ON a.quiz_id = q.id JOIN users u ON a.user_id = u.id WHERE a.id = ? LIMIT 1');
$stmt->execute([$id]);
$attempt = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$attempt || (int)$attempt['created_by'] !== (int)$_SESSION['user_id']) { $_SESSION['flash_error']='Access denied'; header('Location: quiz-attempts.php'); exit(); }

$stmt2 = $conn->prepare('SELECT aa.*, ques.question_text, ch.label FROM attempt_answers aa JOIN questions ques ON aa.question_id = ques.id LEFT JOIN choices ch ON aa.choice_id = ch.id WHERE aa.attempt_id = ?');
$stmt2->execute([$id]);
$answers = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>View Attempt</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page">
  <div class="container" style="max-width:900px;margin:30px auto;padding:20px">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <div>
        <h2 style="margin:0">Attempt: <?= htmlspecialchars($attempt['quiz_title']) ?></h2>
        <div style="color:var(--text);font-size:14px">Student: <?= htmlspecialchars($attempt['student_name'] ?? $attempt['user_id']) ?> — Score: <strong><?= htmlspecialchars($attempt['score']) ?>%</strong></div>
      </div>
      <div>
        <a href="quiz-attempts.php" class="btn-outline">Back</a>
        <a href="export-attempt.php?id=<?= (int)$attempt['id'] ?>" class="btn-outline" style="margin-left:8px">Export CSV</a>
      </div>
    </div>

    <h3 style="margin-top:18px">Answers</h3>
    <div style="display:flex;flex-direction:column;gap:12px;margin-top:8px">
    <?php foreach($answers as $a): ?>
      <div class="quiz-question">
        <div class="q-title"><?= htmlspecialchars($a['question_text']) ?></div>
        <?php if ($a['choice_id']): ?>
          <div class="choices" style="margin-top:8px;display:flex;justify-content:space-between;align-items:center">
            <div>Selected: <strong><?= htmlspecialchars($a['label'] ?? '') ?></strong></div>
            <div><?php if ($a['is_correct']): ?><span class="unread-badge">Correct</span><?php else: ?><span style="color:#c00">Incorrect</span><?php endif; ?></div>
          </div>
        <?php else: ?>
          <div class="choices" style="margin-top:8px">Answer: <?= nl2br(htmlspecialchars($a['answer_text'] ?? '')) ?></div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
</div>
</body>
</html>
