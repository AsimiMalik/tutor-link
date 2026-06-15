<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'tutor') { header('Location: ../auth/login.php'); exit(); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../tutor/create-quiz.php'); exit(); }
if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) { $_SESSION['flash_error']='Invalid CSRF token'; header('Location: ../tutor/create-quiz.php'); exit(); }

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$time_limit = isset($_POST['time_limit']) && $_POST['time_limit'] !== '' ? (int)$_POST['time_limit'] : null;
$questions_json = $_POST['questions_json'] ?? '';
if ($title === '' || $questions_json === '') { $_SESSION['flash_error']='Title and questions required'; header('Location: ../tutor/create-quiz.php'); exit(); }

require_once __DIR__ . '/../classes/Database.php';
$db = new Database(); $conn = $db->connect();
try{
    $conn->beginTransaction();
    // include the tutor as creator when saving the quiz
    $created_by = $_SESSION['user_id'];
    // if the column doesn't exist yet this will error; assume migration run. If it fails, fallback to insert without created_by
    try {
        $stmt = $conn->prepare('INSERT INTO quizzes (title,description,time_limit,created_by) VALUES (?, ?, ?, ?)');
        $stmt->execute([$title,$description,$time_limit,$created_by]);
    } catch (Exception $e) {
        // fallback for older schema
        $stmt = $conn->prepare('INSERT INTO quizzes (title,description,time_limit) VALUES (?, ?, ?)');
        $stmt->execute([$title,$description,$time_limit]);
    }
    $quiz_id = (int)$conn->lastInsertId();

    $questions = json_decode($questions_json, true);
    if (!is_array($questions)) $questions = [];
    foreach ($questions as $q){
        $qtext = $q['question'] ?? ($q['question_text'] ?? '');
        $qtype = $q['type'] ?? 'mcq';
        $points = isset($q['points']) ? (int)$q['points'] : 1;
        $ins = $conn->prepare('INSERT INTO questions (quiz_id,question_text,question_type,points) VALUES (?, ?, ?, ?)');
        $ins->execute([$quiz_id,$qtext,$qtype,$points]);
        $question_id = (int)$conn->lastInsertId();
        if ($qtype === 'mcq' && !empty($q['choices']) && is_array($q['choices'])){
            foreach ($q['choices'] as $c){
                $label = $c['label'] ?? ''; $is_correct = !empty($c['is_correct']) ? 1 : 0;
                $cins = $conn->prepare('INSERT INTO choices (question_id,label,is_correct) VALUES (?, ?, ?)');
                $cins->execute([$question_id,$label,$is_correct]);
            }
        }
    }
    $conn->commit();
    $_SESSION['flash_success'] = 'Quiz created successfully';
    header('Location: ../tutor/create-quiz.php'); exit();
} catch (Exception $e){
    if ($conn) $conn->rollBack();
    $_SESSION['flash_error'] = 'Error creating quiz: ' . $e->getMessage();
    header('Location: ../tutor/create-quiz.php'); exit();
}
