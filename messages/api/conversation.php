<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error'=>'unauthenticated']); exit(); }
$other = isset($_GET['user']) ? (int)$_GET['user'] : 0;
if ($other <= 0) { http_response_code(400); echo json_encode(['error'=>'invalid_user']); exit(); }
require_once __DIR__ . '/../../classes/Database.php';
$db = new Database(); $conn = $db->connect();
// fetch last 200 messages between the two users
$stmt = $conn->prepare("SELECT m.id, m.sender_id, m.receiver_id, m.subject, m.body, m.created_at, m.is_read,
    su.fullname AS sender_name, ru.fullname AS receiver_name
    FROM messages m
    JOIN users su ON m.sender_id = su.id
    JOIN users ru ON m.receiver_id = ru.id
    WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?) ORDER BY m.id ASC LIMIT 1000");
$stmt->execute([$_SESSION['user_id'],$other,$other,$_SESSION['user_id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
// mark unread messages as read when they were sent to current user
$idsToMark = [];
foreach ($rows as $r) {
    if ($r['receiver_id'] == $_SESSION['user_id'] && !$r['is_read']) $idsToMark[] = (int)$r['id'];
}
if (!empty($idsToMark)) {
    $placeholders = implode(',', array_fill(0,count($idsToMark),'?'));
    $uparams = $idsToMark; $uparams[] = $_SESSION['user_id'];
    $conn->prepare("UPDATE messages SET is_read = 1 WHERE id IN ($placeholders) AND receiver_id = ?")->execute($uparams);
}
echo json_encode(['messages'=>$rows]);
