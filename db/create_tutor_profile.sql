-- Migration: create tutor_profile table
-- Stores extended tutor profile information including qualifications

CREATE TABLE IF NOT EXISTS tutor_profile (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  bio TEXT,
  qualification TEXT,
  experience TEXT,
  location VARCHAR(255),
  hourly_rate DECIMAL(10,2) DEFAULT 0.00,
  profile_pic VARCHAR(255),
  is_verified TINYINT(1) DEFAULT 0,
  rating_avg DECIMAL(4,2) DEFAULT 0.00,
  total_reviews INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_id (user_id),
  INDEX idx_user_id (user_id),
  CONSTRAINT fk_tutor_profile_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
