<?php
require_once __DIR__ . '/Database.php';

class Quiz {
    private $conn;
    public function __construct($pdo=null){
        if ($pdo) $this->conn = $pdo;
        else { $db = new Database(); $this->conn = $db->connect(); }
    }

    public function getAll(){
        $stmt = $this->conn->prepare('SELECT * FROM quizzes ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id){
        $stmt = $this->conn->prepare('SELECT * FROM quizzes WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getQuestionsWithChoices(int $quiz_id){
        $stmt = $this->conn->prepare('SELECT q.* FROM questions q WHERE q.quiz_id = ? ORDER BY q.id ASC');
        $stmt->execute([$quiz_id]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($questions as &$q){
            if ($q['question_type'] === 'mcq'){
                $c = $this->conn->prepare('SELECT id,label FROM choices WHERE question_id = ?');
                $c->execute([$q['id']]);
                $q['choices'] = $c->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        return $questions;
    }

    public function submitAttempt(int $quiz_id,int $user_id,array $answers){
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare('INSERT INTO attempts (quiz_id,user_id,score,started_at,completed_at) VALUES (?, ?, 0, NOW(), NOW())');
            $stmt->execute([$quiz_id,$user_id]);
            $attempt_id = (int)$this->conn->lastInsertId();

            $totalScore = 0; $totalPossible = 0;
            foreach ($answers as $question_id => $ans){
                $qstmt = $this->conn->prepare('SELECT question_type,points FROM questions WHERE id = ? AND quiz_id = ? LIMIT 1');
                $qstmt->execute([$question_id,$quiz_id]);
                $q = $qstmt->fetch(PDO::FETCH_ASSOC);
                if (!$q) continue;
                $points = (int)($q['points'] ?? 1);
                $totalPossible += $points;
                $is_correct = 0;
                $choice_id = null; $answer_text = null;
                if ($q['question_type'] === 'mcq'){
                    $choice_id = $ans ? (int)$ans : null;
                    if ($choice_id){
                        $cc = $this->conn->prepare('SELECT is_correct FROM choices WHERE id = ? LIMIT 1');
                        $cc->execute([$choice_id]);
                        $cres = $cc->fetch(PDO::FETCH_ASSOC);
                        if ($cres && $cres['is_correct']) { $is_correct = 1; $totalScore += $points; }
                    }
                } else {
                    $answer_text = is_string($ans) ? trim($ans) : null;
                }
                $ins = $this->conn->prepare('INSERT INTO attempt_answers (attempt_id,question_id,choice_id,answer_text,is_correct) VALUES (?, ?, ?, ?, ?)');
                $ins->execute([$attempt_id,$question_id,$choice_id,$answer_text,$is_correct]);
            }

            $score = $totalPossible>0 ? ($totalScore / $totalPossible) * 100 : 0;
            $upd = $this->conn->prepare('UPDATE attempts SET score = ?, completed_at = NOW() WHERE id = ?');
            $upd->execute([round($score,2), $attempt_id]);

            $this->conn->commit();
            return ['attempt_id'=>$attempt_id,'score'=>round($score,2),'total'=>$totalPossible,'earned'=>$totalScore];
        } catch (Exception $e){
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function getAttempt(int $attempt_id){
        $stmt = $this->conn->prepare('SELECT a.*, q.title FROM attempts a JOIN quizzes q ON a.quiz_id = q.id WHERE a.id = ? LIMIT 1');
        $stmt->execute([$attempt_id]);
        $attempt = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($attempt){
            $stmt2 = $this->conn->prepare('SELECT aa.*, ques.question_text, ch.label FROM attempt_answers aa JOIN questions ques ON aa.question_id = ques.id LEFT JOIN choices ch ON aa.choice_id = ch.id WHERE aa.attempt_id = ?');
            $stmt2->execute([$attempt_id]);
            $attempt['answers'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }
        return $attempt;
    }
}
