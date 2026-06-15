-- Seed: sample tutor, student, quiz, questions, choices, and an attempt
-- Run this in your `brilliance` database (phpMyAdmin or CLI).

START TRANSACTION;

-- create sample tutor and student if not exists
INSERT IGNORE INTO users (id, fullname, email, password) VALUES
(1001, 'Sample Tutor', 'tutor@example.test', ''),
(2001, 'Sample Student', 'student@example.test', '');

-- create sample quiz by tutor
INSERT INTO quizzes (title, description, time_limit, created_by) VALUES
('Sample Math Quiz', 'A short 3-question math quiz for testing.', 10, 1001);
SET @quiz_id = LAST_INSERT_ID();

-- questions
INSERT INTO questions (quiz_id, question_text, question_type, points) VALUES
(@quiz_id, 'What is 2 + 2?', 'mcq', 1),
(@quiz_id, 'What is 5 * 3?', 'mcq', 1),
(@quiz_id, 'Explain in one sentence what addition is.', 'text', 2);

-- choices for Q1 (assumes ids auto increment)
SET @q1 = (SELECT id FROM questions WHERE quiz_id=@quiz_id ORDER BY id LIMIT 1);
SET @q2 = (SELECT id FROM questions WHERE quiz_id=@quiz_id ORDER BY id LIMIT 1 OFFSET 1);

INSERT INTO choices (question_id, label, is_correct) VALUES
(@q1, '3', 0), (@q1, '4', 1), (@q1, '5', 0),
(@q2, '15', 1), (@q2, '10', 0), (@q2, '20', 0);

-- create a sample attempt by student (simulate scoring 100%)
INSERT INTO attempts (quiz_id, user_id, score, started_at, completed_at) VALUES
(@quiz_id, 2001, 100.00, NOW(), NOW());
SET @attempt_id = LAST_INSERT_ID();

-- attach answers: choose correct choices
SET @choice_q1 = (SELECT id FROM choices WHERE question_id=@q1 AND is_correct=1 LIMIT 1);
SET @choice_q2 = (SELECT id FROM choices WHERE question_id=@q2 AND is_correct=1 LIMIT 1);

INSERT INTO attempt_answers (attempt_id, question_id, choice_id, answer_text, is_correct) VALUES
(@attempt_id, @q1, @choice_q1, NULL, 1),
(@attempt_id, @q2, @choice_q2, NULL, 1);

-- free-text answer
SET @q3 = (SELECT id FROM questions WHERE quiz_id=@quiz_id ORDER BY id LIMIT 1 OFFSET 2);
INSERT INTO attempt_answers (attempt_id, question_id, choice_id, answer_text, is_correct) VALUES
(@attempt_id, @q3, NULL, 'Addition is combining numbers to get a total.', 0);

COMMIT;

SELECT 'SEED_COMPLETE' as status;
