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
$ok = $rev->submit($_SESSION['user_id'],$reviewee_id,$rating,$title,$body,null);
if ($ok) $_SESSION['flash_success']='Review submitted'; else $_SESSION['flash_error']='Unable to submit review';
header('Location: ../tutor/view-tutor.php?id=' . $reviewee_id);
exit();
