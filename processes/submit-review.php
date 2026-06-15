<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../'); exit(); }
if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) { $_SESSION['flash_error']='Invalid CSRF token'; header('Location: ../'); exit(); }

$reviewee_id = (int)($_POST['reviewee_id'] ?? 0);
$rating = (int)($_POST['rating'] ?? 0);
$title = trim($_POST['title'] ?? '');
$body = trim($_POST['body'] ?? '');
$booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : null;

if ($reviewee_id <= 0 || $rating < 1 || $rating > 5 || $body === '') { $_SESSION['flash_error']='Invalid review data'; header('Location: ../reviews/submit_review.php?tutor_id=' . $reviewee_id); exit(); }

require_once __DIR__ . '/../classes/Review.php';
require_once __DIR__ . '/../classes/Database.php';
$rev = new Review();
// ensure only parents can submit reviews
$db = new Database(); $conn = $db->connect();
$u = $conn->prepare("SELECT role FROM users WHERE id = ?");
$u->execute([$_SESSION['user_id']]);
$userRow = $u->fetch(PDO::FETCH_ASSOC);
if (!$userRow || ($userRow['role'] ?? '') !== 'parent') { $_SESSION['flash_error']='Only parents can submit reviews'; header('Location: ../tutor/view-tutor.php?id=' . $reviewee_id); exit(); }
if ($_SESSION['user_id'] === $reviewee_id) { $_SESSION['flash_error']='You cannot review yourself'; header('Location: ../tutor/view-tutor.php?id=' . $reviewee_id); exit(); }
$ok = $rev->submit($_SESSION['user_id'],$reviewee_id,$rating,$title,$body,$booking_id);
if ($ok) {
	// Recalculate and persist tutor average rating and total reviews
	try {
		$avgStmt = $conn->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total FROM reviews WHERE reviewee_id = ? AND visibility = 'visible'");
		$avgStmt->execute([$reviewee_id]);
		$row = $avgStmt->fetch(PDO::FETCH_ASSOC);
		$avg_rating = $row && $row['avg_rating'] !== null ? number_format((float)$row['avg_rating'], 2, '.', '') : 0;
		$total_reviews = $row ? (int)$row['total'] : 0;

		// Update tutor_profile if exists
		$update = $conn->prepare("UPDATE tutor_profile SET rating_avg = ?, total_reviews = ?, updated_at = NOW() WHERE user_id = ?");
		$update->execute([$avg_rating, $total_reviews, $reviewee_id]);
		// If no rows were updated, insert a profile row (handles tutors without a profile yet)
		if ($update->rowCount() === 0) {
			$ins = $conn->prepare("INSERT INTO tutor_profile (user_id, rating_avg, total_reviews, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE rating_avg = VALUES(rating_avg), total_reviews = VALUES(total_reviews), updated_at = NOW()");
			$ins->execute([$reviewee_id, $avg_rating, $total_reviews]);
		}
	} catch (Exception $e) {
		// ignore; review was saved even if profile update failed
	}

	$_SESSION['flash_success']='Review submitted';
} else {
	$_SESSION['flash_error']='Unable to submit review';
}
header('Location: ../tutor/view-tutor.php?id=' . $reviewee_id);
exit();
