<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error'=>'unauthenticated']); exit(); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error'=>'method_not_allowed']); exit(); }
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) { http_response_code(400); echo json_encode(['error'=>'invalid_csrf']); exit(); }
$receiver_id = (int)($_POST['receiver_id'] ?? 0);
$body = trim($_POST['body'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$booking_id = isset($_POST['booking_id']) && $_POST['booking_id'] !== '' ? (int)$_POST['booking_id'] : null;
if ($receiver_id <= 0 || $body === '') { http_response_code(400); echo json_encode(['error'=>'invalid_payload']); exit(); }
// validate receiver exists
require_once __DIR__ . '/../classes/Database.php';
$db = new Database(); $conn = $db->connect();
$u = $conn->prepare('SELECT id FROM users WHERE id = ? LIMIT 1');
$u->execute([$receiver_id]);
$exists = $u->fetchColumn();
if (!$exists) { http_response_code(400); echo json_encode(['error'=>'invalid_receiver']); exit(); }
require_once __DIR__ . '/../classes/Message.php';
$msg = new Message();
try {
    $ok = $msg->send($_SESSION['user_id'],$receiver_id,$subject,$body,$booking_id);
    if ($ok) {
        echo json_encode(['ok'=>true]);
    } else {
        http_response_code(500); echo json_encode(['error'=>'unable_to_send']);
    }
} catch (Exception $e) {
    http_response_code(500); echo json_encode(['error'=>'exception','message'=>$e->getMessage()]);
}
