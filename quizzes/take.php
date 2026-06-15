<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../classes/Quiz.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id<=0) { header('Location: index.php'); exit(); }
$q = new Quiz();
$quiz = $q->getById($id);
if (!$quiz) { $_SESSION['flash_error']='Quiz not found'; header('Location: index.php'); exit(); }
$questions = $q->getQuestionsWithChoices($id);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?= htmlspecialchars($quiz['title']) ?></title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page" style="max-width:900px;margin:90px auto;padding:20px">
  <div class="quiz-card" style="margin-bottom:14px;padding:18px">
    <div class="card-header">
      <div class="card-icon" aria-hidden>
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14.5h-2V13H8.5V11H11V8.5h2V11H15.5v2H13v3.5z"/></svg>
      </div>
      <div>
        <h2 style="margin:0"><?= htmlspecialchars($quiz['title']) ?></h2>
        <div class="card-meta"><?= htmlspecialchars($quiz['description'] ?: '') ?></div>
      </div>
    </div>
    <div style="margin-top:8px;color:var(--text);font-size:13px"><?php if (!empty($quiz['time_limit'])): ?>Time limit: <?= htmlspecialchars($quiz['time_limit']) ?> minutes<?php else: ?>No time limit<?php endif; ?></div>
  </div>
  <form method="post" action="../processes/submit-quiz.php">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">
    <?php foreach($questions as $qno => $ques): ?>
      <div class="quiz-question">
        <div class="q-title">Q<?= $qno+1 ?>. <?= htmlspecialchars($ques['question_text']) ?></div>
        <div class="choices">
          <?php if ($ques['question_type'] === 'mcq'): ?>
            <?php foreach($ques['choices'] as $choice): ?>
              <label><input type="radio" name="answers[<?= $ques['id'] ?>]" value="<?= $choice['id'] ?>"> <?= htmlspecialchars($choice['label']) ?></label>
            <?php endforeach; ?>
          <?php else: ?>
            <textarea name="answers[<?= $ques['id'] ?>]" style="width:100%;min-height:120px" placeholder="Your answer"></textarea>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
    <div class="quiz-footer-sticky">
      <div class="message-actions"><button class="btn-primary" type="submit">Submit Quiz</button></div>
    </div>
  </form>
</div>
</body>
</html>
