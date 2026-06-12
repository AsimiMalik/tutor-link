-- Migration: create tutor_subjects table
-- Maps tutors to subjects they teach

CREATE TABLE IF NOT EXISTS tutor_subjects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tutor_id INT NOT NULL,
  subject_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_tutor_subject (tutor_id, subject_id),
  INDEX idx_tutor_id (tutor_id),
  INDEX idx_subject_id (subject_id),
  CONSTRAINT fk_tutor_subjects_tutor FOREIGN KEY (tutor_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_tutor_subjects_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
