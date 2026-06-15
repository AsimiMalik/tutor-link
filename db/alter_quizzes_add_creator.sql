-- Migration: add created_by to quizzes
-- Run these two statements in phpMyAdmin's SQL tab or via CLI.

ALTER TABLE quizzes
  ADD COLUMN created_by INT NULL AFTER description;

ALTER TABLE quizzes
  ADD CONSTRAINT fk_quizzes_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;
