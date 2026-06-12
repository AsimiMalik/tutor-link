<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';

/*
----------------------------------------------------
CHECK LOGIN STATUS
----------------------------------------------------
*/
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

/*
----------------------------------------------------
GET TUTOR ID
----------------------------------------------------
*/
$tutor_id = $_GET['tutor_id'] ?? null;

if (!$tutor_id) {
    die("Invalid tutor selected");
}

/*
----------------------------------------------------
DATABASE CONNECTION
----------------------------------------------------
*/
require_once "../classes/Database.php";

$db = new Database();
$conn = $db->connect();

/*
----------------------------------------------------
FETCH SUBJECTS
----------------------------------------------------
*/
$stmt = $conn->query("SELECT * FROM subjects");
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// optional prefill values from Rebook
$prefill_subject = isset($_GET['subject_id']) ? $_GET['subject_id'] : '';
$prefill_session_date = isset($_GET['session_date']) ? $_GET['session_date'] : '';
// convert SQL datetime (YYYY-MM-DD HH:MM:SS) to datetime-local (YYYY-MM-DDTHH:MM)
if ($prefill_session_date) {
    $prefill_session_date = str_replace(' ', 'T', substr($prefill_session_date, 0, 16));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Tutor | Brilliance</title>

    <link rel="stylesheet" href="/tutorlink/assets/css/booking.css">
    <link rel="stylesheet" href="/tutorlink/assets/css/style.css">
</head>

<body>

<?php
// include dashboard navbar based on logged-in role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'parent') {
        include __DIR__ . '/../includes/parent-navbar.php';
    } elseif ($_SESSION['role'] === 'tutor') {
        include __DIR__ . '/../includes/tutor-navbar.php';
    } else {
        include __DIR__ . '/../includes/navbar.php';
    }
} else {
    include __DIR__ . '/../includes/navbar.php';
}

?>

<div class="booking-container">

    <div class="booking-card">

        <h2>Book a Tutor</h2>
        <p class="subtitle">Choose your subject and schedule a session</p>

        <form method="POST" action="../processes/process-booking.php">

            <?php echo csrf_field(); ?>

            <!-- TUTOR ID -->
            <input type="hidden" name="tutor_id" value="<?= $tutor_id ?>">

            <!-- SUBJECT -->
            <div class="form-group">
                <label>Subject</label>
                <select name="subject_id" required>
                    <option value="">-- Select Subject --</option>

                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= $subject['id'] ?>" <?php if ((string)$subject['id'] === (string)$prefill_subject) echo 'selected'; ?>>
                            <?= $subject['name'] ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <!-- DATE & TIME -->
            <div class="form-group">
                <label>Session Date & Time</label>
                <input type="datetime-local" name="session_date" required value="<?= htmlspecialchars($prefill_session_date) ?>">
            </div>

            <!-- SUBMIT -->
            <button type="submit" name="book" class="btn">
                Send Booking Request
            </button>

        </form>

    </div>

</div>

</body>
</html>