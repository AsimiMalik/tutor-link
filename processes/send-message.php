<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../messages/inbox.php'); exit(); }
if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) { $_SESSION['flash_error']='Invalid CSRF token'; header('Location: ../messages/inbox.php'); exit(); }

$receiver_id = (int)($_POST['receiver_id'] ?? 0);
$subject = trim($_POST['subject'] ?? '');
$body = trim($_POST['body'] ?? '');
// normalize booking_id: treat empty or zero as null to avoid FK violations
$booking_id = isset($_POST['booking_id']) && $_POST['booking_id'] !== '' ? (int)$_POST['booking_id'] : null;
if ($booking_id !== null && $booking_id <= 0) $booking_id = null;

if ($receiver_id <= 0 || $body === '') { $_SESSION['flash_error']='Receiver and message body required'; header('Location: ../messages/compose.php?receiver_id=' . $receiver_id); exit(); }

// validate receiver exists to avoid FK errors
require_once __DIR__ . '/../classes/Database.php';
$db = new Database(); $conn = $db->connect();
$u = $conn->prepare('SELECT id FROM users WHERE id = ? LIMIT 1');
$u->execute([$receiver_id]);
$exists = $u->fetchColumn();
if (!$exists) {
	$_SESSION['flash_error'] = 'Recipient not found';
	header('Location: ../messages/compose.php?receiver_id=' . $receiver_id);
	exit();
}

// if a booking_id was provided, ensure it exists (avoid FK constraint errors)
if ($booking_id !== null) {
	try {
		$b = $conn->prepare('SELECT id FROM bookings WHERE id = ? LIMIT 1');
		$b->execute([$booking_id]);
		$bk = $b->fetchColumn();
		if (!$bk) {
			// invalid booking - clear to NULL and log a debug note
			$logDir = __DIR__ . '/../logs'; if (!is_dir($logDir)) @mkdir($logDir,0755,true);
			$logFile = $logDir . '/message_errors.log';
			$note = date('c') . " - warning: invalid booking_id {$booking_id} for sender={$_SESSION['user_id']} receiver={$receiver_id} - clearing to NULL\n";
			file_put_contents($logFile, $note, FILE_APPEND | LOCK_EX);
			$booking_id = null;
		}
	} catch (Exception $e) {
		// DB check failed, be conservative and clear booking_id to avoid FK issues
		$booking_id = null;
	}
}

require_once __DIR__ . '/../classes/Message.php';
$msg = new Message();
try {
	$ok = $msg->send($_SESSION['user_id'],$receiver_id,$subject,$body,$booking_id);
	if ($ok) {
		$_SESSION['flash_success'] = 'Message sent';
	} else {
		$err = method_exists($msg,'getErrorInfo') ? $msg->getErrorInfo() : null;
		$_SESSION['flash_error'] = 'Unable to send message' . ($err ? (': ' . implode(' | ', $err)) : '');
		// also log to file for debugging
		$logDir = __DIR__ . '/../logs';
		if (!is_dir($logDir)) @mkdir($logDir,0755,true);
		$logFile = $logDir . '/message_errors.log';
		$logMsg = date('c') . " - send failed - receiver={$receiver_id} sender={$_SESSION['user_id']}\n";
		if ($err) $logMsg .= "PDO: " . implode(' | ', $err) . "\n";
		file_put_contents($logFile, $logMsg, FILE_APPEND | LOCK_EX);
	}
} catch (Exception $e) {
	// log exception details to help debugging
	$logDir = __DIR__ . '/../logs';
	if (!is_dir($logDir)) @mkdir($logDir,0755,true);
	$logFile = $logDir . '/message_errors.log';
	$logMsg = date('c') . " - exception sending message - receiver={$receiver_id} sender={$_SESSION['user_id']}\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n";
	file_put_contents($logFile, $logMsg, FILE_APPEND | LOCK_EX);
	$_SESSION['flash_error'] = 'Exception sending message: ' . $e->getMessage();
}
header('Location: ../messages/inbox.php');
exit();
