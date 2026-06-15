<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
$reviewee = isset($_GET['tutor_id']) ? (int)$_GET['tutor_id'] : 0;
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Leave Review</title>
<link rel="stylesheet" href="/brilliance/assets/css/style.css">
<style>
    .rating-stars { display:flex; gap:6px; font-size:26px; }
    .rating-stars input { display:none; }
    .rating-stars label { cursor:pointer; color:#ddd; transition: color .12s; }
    .rating-stars label:hover, .rating-stars label.active { color:var(--secondary); }
    .form-card .btn-primary{ width:auto; }
</style>
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="page">
    <div class="container" style="max-width:720px">
        <h2>Leave a Review</h2>
        <div class="form-card" style="margin-top:12px">
            <form method="post" action="../processes/submit-review.php">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="reviewee_id" value="<?= $reviewee ?>">
                <?php if ($booking_id): ?>
                    <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
                <?php endif; ?>

                <label>Rating</label>
                <div class="rating-stars" role="radiogroup">
                    <?php for ($i=5;$i>=1;$i--): ?>
                        <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" <?= $i===5 ? 'checked' : '' ?>>
                        <label for="star<?= $i ?>">★</label>
                    <?php endfor; ?>
                </div>

                <label style="margin-top:12px">Title (optional)</label>
                <input type="text" name="title">

                <label>Comment</label>
                <textarea name="body" required style="min-height:140px"></textarea>

                <div style="margin-top:12px;display:flex;gap:12px;align-items:center">
                    <button class="btn-primary" type="submit">Submit Review</button>
                    <a class="btn-outline" href="../view-tutors.php">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // simple star highlight
    document.querySelectorAll('.rating-stars input').forEach(function(inp){
        inp.addEventListener('change', function(){
            document.querySelectorAll('.rating-stars label').forEach(l=>l.classList.remove('active'));
            var sel = document.querySelector('.rating-stars input:checked');
            if (!sel) return;
            var v = parseInt(sel.value,10);
            document.querySelectorAll('.rating-stars label').forEach(function(lbl){
                if (parseInt(lbl.htmlFor.replace('star',''),10) <= v) lbl.classList.add('active');
            });
        });
    });
    // initialize
    document.querySelectorAll('.rating-stars input')[0].dispatchEvent(new Event('change'));
</script>
</body>
</html>
