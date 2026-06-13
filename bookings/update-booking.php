<?php
session_start();

require_once __DIR__ . '/../includes/csrf.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
	header('Location: ../auth/login.php');
	exit();
}

require_once __DIR__ . '/../classes/Database.php';
$db = new Database();
$conn = $db->connect();

$booking_id = $_GET['id'] ?? null;
if (!$booking_id) die('Booking not specified');

// fetch booking and verify ownership
$stmt = $conn->prepare('SELECT * FROM bookings WHERE id = ? LIMIT 1');
$stmt->execute([$booking_id]);
$b = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$b) die('Booking not found');
if ($b['parent_id'] != $_SESSION['user_id']) die('Not authorized');

// handle POST (update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	require_once __DIR__ . '/../includes/csrf.php';
	if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) {
		die('Invalid CSRF token');
	}

	$session_date = trim($_POST['session_date'] ?? '');

	// validate and convert datetime-local (YYYY-MM-DDTHH:MM) to MySQL DATETIME (YYYY-MM-DD HH:MM:SS)
	$dbDate = null;
	if ($session_date !== '') {
		// try parse with DateTime
		$dt = DateTime::createFromFormat('Y-m-d\TH:i', $session_date);
		if ($dt === false) {
			// try with seconds if provided
			$dt = DateTime::createFromFormat('Y-m-d\TH:i:s', $session_date);
		}
		if ($dt === false) {
			$_SESSION['flash_error'] = 'Invalid session date format.';
			header('Location: update-booking.php?id=' . $booking_id);
			exit();
		}
		$dbDate = $dt->format('Y-m-d H:i:s');
	}

	try {
		$upd = $conn->prepare('UPDATE bookings SET session_date = ?, status = ? WHERE id = ?');
		$upd->execute([$dbDate, 'pending', $booking_id]);
		if ($upd->rowCount() > 0) {
			$_SESSION['flash_success'] = 'Booking updated successfully.';
		} else {
			$_SESSION['flash_error'] = 'No changes were made to the booking.';
		}
	} catch (PDOException $e) {
		$_SESSION['flash_error'] = 'Error updating booking: ' . $e->getMessage();
	}

	header('Location: view-booking.php?id=' . $booking_id);
	exit();
}

?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Edit Booking #<?= htmlspecialchars($b['id']) ?></title>
	<link rel="stylesheet" href="/brilliance/assets/css/style.css">
	<style>.page{max-width:800px;margin:90px auto;padding:20px}.form-group{margin-bottom:12px}</style>
</head>
<body>
<?php include __DIR__ . '/../includes/parent-navbar.php'; ?>

<div class="page">
	<h2>Edit Booking</h2>
	<form method="post">
		<?php echo csrf_field(); ?>
		<div class="form-group">
			<label>Session Date & Time</label>
			<input type="datetime-local" name="session_date" required value="<?= htmlspecialchars(str_replace(' ', 'T', $b['session_date'])) ?>">
		</div>

		<button class="btn-primary" type="submit">Save Changes</button>
		<a class="btn-outline" href="view-booking.php?id=<?= $b['id'] ?>">Cancel</a>
	</form>
</div>

</body>
</html>
