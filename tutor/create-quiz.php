<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'tutor') { header('Location: ../auth/login.php'); exit(); }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Create Quiz</title>
  <link rel="stylesheet" href="/brilliance/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page">
  <div class="container" style="max-width:980px;margin:20px auto;padding:20px">
    <div class="quiz-card">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
        <div>
          <h2 style="margin:0 0 6px">Create Quiz</h2>
          <div style="color:var(--text);font-size:14px">Use AI-assist to quickly generate questions, or add your own.</div>
        </div>
        <div>
          <a href="/brilliance/tutor/quiz-attempts.php" class="btn-outline" style="margin-right:8px">View Attempts</a>
          <a href="/brilliance/quizzes/index.php" class="btn-primary">All Quizzes</a>
        </div>
      </div>

      <?php if(!empty($_SESSION['flash_error'])): ?><div style="background:#f8d7da;padding:10px;border-radius:6px;color:#721c24;margin-bottom:12px"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div><?php endif; ?>
      <?php if(!empty($_SESSION['flash_success'])): ?><div style="background:#d4edda;padding:10px;border-radius:6px;color:#155724;margin-bottom:12px"><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div><?php endif; ?>

      <form id="createQuizForm" class="message-form" method="post" action="../processes/create-quiz.php">
        <?php echo csrf_field(); ?>
        <label>Title</label>
        <input type="text" name="title" required>

        <label>Description (optional)</label>
        <textarea name="description"></textarea>

        <label>Time limit (minutes)</label>
        <input type="number" name="time_limit" min="1">

        <hr style="margin:18px 0">
        <h3 style="margin-bottom:8px">Generate Questions (AI-assist)</h3>
        <label>Subject / Topic keywords</label>
        <input type="text" id="gen_subject" placeholder="e.g. Algebra: linear equations">

        <label>Number of questions</label>
        <input type="number" id="gen_count" value="5" min="1" max="20">

        <div style="margin-top:12px">
          <button type="button" id="generateBtn" class="btn-outline">Generate Questions</button>
        </div>

        <hr style="margin:18px 0">
        <h3 style="margin-bottom:12px">Questions</h3>
        <div id="questionsContainer"></div>

        <input type="hidden" name="questions_json" id="questions_json">
        <div style="margin-top:12px;display:flex;gap:10px;justify-content:flex-end">
          <a href="/brilliance/quizzes/index.php" class="btn-outline">Cancel</a>
          <button class="btn-primary" type="submit">Save Quiz</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('generateBtn').addEventListener('click', async function(){
  const subj = document.getElementById('gen_subject').value.trim();
  const count = parseInt(document.getElementById('gen_count').value,10) || 3;
  this.disabled = true; this.textContent = 'Generating...';
  try{
    const res = await fetch('../processes/generate-quiz-questions.php',{
      method:'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({ subject: subj, count: count })
    });
    const data = await res.json();
    if (data.error) { alert(data.error); }
    else populateQuestions(data.questions || []);
  }catch(e){ alert('Error generating questions'); }
  this.disabled = false; this.textContent = 'Generate Questions';
});

function populateQuestions(questions){
  const container = document.getElementById('questionsContainer'); container.innerHTML = '';
  questions.forEach((q, idx)=>{
    const el = document.createElement('div'); el.className='quiz-question';
    let html = '<div class="q-title">Q'+(idx+1)+'. '+escapeHtml(q.question)+'</div>';
    if (q.type === 'mcq'){
      q.choices.forEach(c=>{
        html += '<label><input type="radio" name="q_'+idx+'" data-qid="'+(q.temp_id||idx)+'" value="'+escapeHtml(c.id||c.label)+'"> '+escapeHtml(c.label)+'</label>';
      });
    } else {
      html += '<textarea placeholder="Student answer (free text)" rows="3"></textarea>';
    }
    el.innerHTML = html;
    // attach data attributes
    el.dataset.q = JSON.stringify(q);
    container.appendChild(el);
  });
  // also store as JSON for submission
  document.getElementById('questions_json').value = JSON.stringify(questions);
}

function escapeHtml(s){ return String(s).replace(/[&<>\"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[c]; }); }

// On submit, ensure questions_json is up-to-date
document.getElementById('createQuizForm').addEventListener('submit', function(e){
  const container = document.getElementById('questionsContainer');
  const qs = Array.from(container.querySelectorAll('.quiz-question')).map((el,idx)=>JSON.parse(el.dataset.q));
  document.getElementById('questions_json').value = JSON.stringify(qs);
});
</script>

</div>
</body>
</html>
