<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'tutor') { header('Location: ../auth/login.php'); exit(); }
$db = new Database(); $conn = $db->connect();
$tutor_id = $_SESSION['user_id'];

$filter_quiz = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
$filter_student = isset($_GET['q']) ? trim($_GET['q']) : '';

try{
    $sql = 'SELECT a.id as attempt_id, a.score, a.started_at, a.completed_at, q.title as quiz_title, u.fullname as student_name, u.email FROM attempts a JOIN quizzes q ON a.quiz_id = q.id JOIN users u ON a.user_id = u.id WHERE q.created_by = ?';
    $params = [$tutor_id];
    if ($filter_quiz) { $sql .= ' AND q.id = ?'; $params[] = $filter_quiz; }
    if ($filter_student !== '') { $sql .= ' AND u.fullname LIKE ?'; $params[] = '%'.$filter_student.'%'; }
    $sql .= ' ORDER BY a.completed_at DESC';

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e){ $rows = []; }

// output CSV
$filename = 'attempts_export_'.date('Ymd_His').'.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename='.$filename);
$out = fopen('php://output', 'w');
if (!$out) exit();
// headers
fputcsv($out, ['Attempt ID','Quiz Title','Student Name','Student Email','Score (%)','Started At','Completed At']);
foreach($rows as $r){
    fputcsv($out, [
        $r['attempt_id'], $r['quiz_title'], $r['student_name'], $r['email'] ?? '', $r['score'], $r['started_at'], $r['completed_at']
    ]);
}
fclose($out);
exit();
