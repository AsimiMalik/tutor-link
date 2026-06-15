<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Submit Session Report — Brilliance</title>
    <link rel="stylesheet" href="/brilliance/assets/css/style.css">
    <style>.form-card{max-width:800px;margin:120px auto;padding:30px;background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.06)}</style>
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="form-card">
    <h2>Submit Session Report</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="/brilliance/processes/submit-session-report.php" method="post">
        <?php require_once __DIR__ . '/../includes/csrf.php'; echo csrf_input(); ?>

        <label>Booking ID (optional)</label>
        <input type="number" name="booking_id" placeholder="Booking ID" class="input">

        <label>Parent ID (optional)</label>
        <input type="number" name="parent_id" placeholder="Parent user ID" class="input">

        <label>Topics Covered</label>
        <textarea name="topics" placeholder="Topics covered" rows="4"></textarea>

        <label>Duration (minutes)</label>
        <input type="number" name="duration_minutes" value="60" class="input">

        <label>Attendance</label>
        <select name="attendance">
            <option value="present">Present</option>
            <option value="late">Late</option>
            <option value="absent">Absent</option>
        </select>

        <label>Homework</label>
        <textarea name="homework" rows="3" placeholder="Homework assigned"></textarea>

        <label>Student Performance Rating (1-5)</label>
        <input type="number" name="rating" min="1" max="5" class="input">

        <button class="btn-primary" type="submit">Submit Report</button>
    </form>
</div>

</body>
</html>
