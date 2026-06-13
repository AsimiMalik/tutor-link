<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../messages/inbox.php'); exit(); }
if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) { $_SESSION['flash_error']='Invalid CSRF token'; header('Location: ../messages/inbox.php'); exit(); }

$receiver_id = (int)($_POST['receiver_id'] ?? 0);
$subject = trim($_POST['subject'] ?? '');
$body = trim($_POST['body'] ?? '');
$booking_id = isset($_POST['booking_id']) && $_POST['booking_id'] !== '' ? (int)$_POST['booking_id'] : null;

if ($receiver_id <= 0 || $body === '') { $_SESSION['flash_error']='Receiver and message body required'; header('Location: ../messages/compose.php?receiver_id=' . $receiver_id); exit(); }

require_once __DIR__ . '/../classes/Message.php';
$msg = new Message();
try {
	$ok = $msg->send($_SESSION['user_id'],$receiver_id,$subject,$body,$booking_id);
	if ($ok) {
		$_SESSION['flash_success'] = 'Message sent';
	} else {
		$_SESSION['flash_error'] = 'Unable to send message';
	}
} catch (Exception $e) {
	$_SESSION['flash_error'] = 'Exception sending message: ' . $e->getMessage();
}
header('Location: ../messages/inbox.php');
exit();
