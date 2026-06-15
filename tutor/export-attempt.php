<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'tutor') { header('HTTP/1.1 403 Forbidden'); exit('Access denied'); }
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('HTTP/1.1 400 Bad Request'); exit('Invalid attempt id'); }

$db = new Database(); $conn = $db->connect();
try{
    $stmt = $conn->prepare('SELECT a.*, q.title as quiz_title, q.created_by, u.fullname as student_name, u.email as student_email FROM attempts a JOIN quizzes q ON a.quiz_id = q.id JOIN users u ON a.user_id = u.id WHERE a.id = ? LIMIT 1');
    $stmt->execute([$id]);
    $attempt = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e){ $attempt = false; }

if (!$attempt || (int)$attempt['created_by'] !== (int)$_SESSION['user_id']) { header('HTTP/1.1 403 Forbidden'); exit('Access denied'); }

try{
    $stmt2 = $conn->prepare('SELECT aa.*, ques.id as question_id, ques.question_text, ch.id as choice_id, ch.label, aa.is_correct FROM attempt_answers aa JOIN questions ques ON aa.question_id = ques.id LEFT JOIN choices ch ON aa.choice_id = ch.id WHERE aa.attempt_id = ? ORDER BY aa.id');
    $stmt2->execute([$id]);
    $answers = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e){ $answers = []; }

$filename = 'attempt_'. $id . '_export_'.date('Ymd_His').'.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename='.$filename);
$out = fopen('php://output','w');
if (!$out) exit();

// CSV header
fputcsv($out, ['Attempt ID','Quiz Title','Student Name','Student Email','Score (%)','Started At','Completed At','Question ID','Question Text','Choice ID','Choice Label','Is Correct','Answer Text']);

foreach($answers as $r){
    fputcsv($out, [
        $attempt['id'], $attempt['quiz_title'], $attempt['student_name'], $attempt['student_email'] ?? '', $attempt['score'], $attempt['started_at'], $attempt['completed_at'],
        $r['question_id'] ?? '', $r['question_text'] ?? '', $r['choice_id'] ?? '', $r['label'] ?? '', ($r['is_correct'] ? 'Yes' : 'No'), $r['answer_text'] ?? ''
    ]);
}

fclose($out);
exit();
