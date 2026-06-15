-- Creates session_reports table to store tutor session reports
CREATE TABLE IF NOT EXISTS session_reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NULL,
  tutor_id INT NOT NULL,
  parent_id INT NOT NULL,
  topics TEXT,
  duration_minutes INT DEFAULT 0,
  attendance ENUM('present','late','absent') DEFAULT 'present',
  homework TEXT,
  rating TINYINT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (tutor_id),
  INDEX (parent_id),
  INDEX (booking_id)
);

-- Note: run this SQL in your database admin (phpMyAdmin or mysql client).
