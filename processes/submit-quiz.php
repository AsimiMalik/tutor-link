<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../quizzes/index.php'); exit(); }
if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) { $_SESSION['flash_error']='Invalid CSRF token'; header('Location: ../quizzes/index.php'); exit(); }

$quiz_id = isset($_POST['quiz_id']) ? (int)$_POST['quiz_id'] : 0;
$answers = $_POST['answers'] ?? [];
require_once __DIR__ . '/../classes/Quiz.php';
$quiz = new Quiz();
try {
    $result = $quiz->submitAttempt($quiz_id,$_SESSION['user_id'],$answers);
    $_SESSION['flash_success'] = 'Quiz submitted. Score: ' . ($result['score'] ?? 0) . '%';
    header('Location: ../quizzes/result.php?id=' . $result['attempt_id']);
    exit();
} catch (Exception $e){
    $_SESSION['flash_error'] = 'Error submitting quiz: ' . $e->getMessage();
    header('Location: ../quizzes/take.php?id=' . $quiz_id);
    exit();
}
