-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 13, 2026 at 06:33 PM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `brilliance`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL,
  `tutor_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `session_date` datetime NOT NULL,
  `status` enum('pending','accepted','declined','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `tutor_id` (`tutor_id`),
  KEY `subject_id` (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `parent_id`, `tutor_id`, `subject_id`, `session_date`, `status`, `created_at`) VALUES
(1, 13, 11, 1, '2026-06-26 05:50:00', 'completed', '2026-06-11 13:50:47'),
(2, 13, 11, 3, '2026-06-28 03:52:00', 'declined', '2026-06-12 11:52:38'),
(3, 13, 11, 3, '2026-06-23 04:10:00', 'cancelled', '2026-06-12 12:10:49'),
(4, 13, 11, 3, '2026-06-28 04:13:00', 'completed', '2026-06-12 12:13:56'),
(5, 13, 12, 2, '2001-04-12 00:00:00', 'pending', '2026-06-12 13:04:53'),
(6, 13, 12, 2, '2001-04-12 00:00:00', 'pending', '2026-06-12 13:05:13'),
(7, 13, 11, 64, '2010-02-21 05:30:00', 'completed', '2026-06-12 22:19:08'),
(8, 13, 12, 71, '2001-06-05 00:00:00', 'pending', '2026-06-13 18:02:55'),
(9, 13, 11, 63, '2001-08-07 09:08:00', 'completed', '2026-06-13 18:04:07');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `booking_id` int DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `deleted_by_sender` tinyint(1) DEFAULT '0',
  `deleted_by_receiver` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sender` (`sender_id`),
  KEY `idx_receiver` (`receiver_id`),
  KEY `idx_booking` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `related_id` int DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parent_profile`
--

DROP TABLE IF EXISTS `parent_profile`;
CREATE TABLE IF NOT EXISTS `parent_profile` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `bio` text,
  `location` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `parent_profile`
--

INSERT INTO `parent_profile` (`id`, `user_id`, `bio`, `location`, `profile_pic`, `created_at`, `updated_at`) VALUES
(10, 13, 'seriousness in tutor', 'ASA 1 OTUKPO BENUW STATE', '1781373709_35e9a79c-098c-48ef-878f-799289d77e0c.jpg', '2026-06-12 11:51:11', '2026-06-13 18:01:50');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int DEFAULT NULL,
  `reviewer_id` int NOT NULL,
  `reviewee_id` int NOT NULL,
  `rating` tinyint NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` text,
  `visibility` enum('pending','visible','hidden') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_booking_reviewer` (`booking_id`,`reviewer_id`),
  KEY `idx_reviewer` (`reviewer_id`),
  KEY `idx_reviewee` (`reviewee_id`),
  KEY `idx_booking` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `booking_id`, `reviewer_id`, `reviewee_id`, `rating`, `title`, `body`, `visibility`, `created_at`, `updated_at`) VALUES
(1, NULL, 13, 11, 5, 'A Perfectly done Job', 'Your discipline and talent in handling situations is perfect', 'visible', '2026-06-12 14:17:56', '2026-06-12 14:17:56'),
(2, NULL, 13, 11, 4, '', 'well done jod', 'visible', '2026-06-12 22:16:49', '2026-06-12 22:16:49');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`) VALUES
(68, 'Accounting'),
(77, 'Agricultural Science'),
(79, 'Art and Design'),
(65, 'Biology'),
(69, 'Business Studies'),
(64, 'Chemistry'),
(72, 'Civic Education'),
(66, 'Computer Science'),
(67, 'Economics'),
(2, 'English'),
(63, 'English Language'),
(74, 'French'),
(71, 'Geography'),
(76, 'Government'),
(70, 'History'),
(73, 'Literature'),
(1, 'Mathematics'),
(78, 'Music'),
(80, 'Physical Education'),
(3, 'Physics'),
(75, 'Spanish');

-- --------------------------------------------------------

--
-- Table structure for table `tutor_profile`
--

DROP TABLE IF EXISTS `tutor_profile`;
CREATE TABLE IF NOT EXISTS `tutor_profile` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `bio` text,
  `qualification` text,
  `experience` text,
  `location` varchar(255) DEFAULT NULL,
  `hourly_rate` decimal(10,2) DEFAULT '0.00',
  `profile_pic` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  `rating_avg` decimal(4,2) DEFAULT '0.00',
  `total_reviews` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_id` (`user_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tutor_profile`
--

INSERT INTO `tutor_profile` (`id`, `user_id`, `bio`, `qualification`, `experience`, `location`, `hourly_rate`, `profile_pic`, `is_verified`, `rating_avg`, `total_reviews`, `created_at`, `updated_at`) VALUES
(1, 11, 'Determined, Focused and Disciplined', 'HND, PHD, and MASTERS in SOFTWARE ENGINEERING', '15yrs +', 'Texas US', 100000.00, '1781273525_a56e17ad-cc8f-4b63-b375-93bdb337706e.jpg', 0, 0.00, 0, '2026-06-12 13:51:05', '2026-06-12 14:12:05');

-- --------------------------------------------------------

--
-- Table structure for table `tutor_subjects`
--

DROP TABLE IF EXISTS `tutor_subjects`;
CREATE TABLE IF NOT EXISTS `tutor_subjects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tutor_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tutor_subject` (`tutor_id`,`subject_id`),
  KEY `idx_tutor_id` (`tutor_id`),
  KEY `idx_subject_id` (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tutor_subjects`
--

INSERT INTO `tutor_subjects` (`id`, `tutor_id`, `subject_id`, `created_at`) VALUES
(1, 11, 66, '2026-06-12 14:12:05'),
(2, 11, 1, '2026-06-12 14:12:05'),
(3, 11, 78, '2026-06-12 14:12:05'),
(4, 11, 3, '2026-06-12 14:12:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('parent','tutor','admin') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `role`, `created_at`) VALUES
(11, 'Hezzy Johnson', 'hezzyj1410@gmail.com', '$2y$10$g8dR2zDARR4.OVX3Lx6l6upqwazUuA9wa8hVNGReTk7HCbuTypQwG', 'tutor', '2026-06-11 11:52:09'),
(12, 'queen bee', 'queenbee@gmail.com', '$2y$10$/ZEkn/aZd6wZiP4SP.OA8ugewIkwwL0rauukERPmpWJpltAcsw4gq', 'tutor', '2026-06-11 12:49:50'),
(13, 'kolade', 'kolade@gmail.com', '$2y$10$pLkAb0dTwfpBtf3iVmUa/u9O4/bbER6OBqclxueFYqORQT0NolqY6', 'parent', '2026-06-11 13:37:51'),
(14, 'Juwelu Okonkwo', 'juweluokonkwo@gmail.com', '$2y$10$988fsysL/s/Qnf.l7DkjNOccHGKwevt3KiS3q9zlD8RyUTGNOUcNS', 'tutor', '2026-06-11 13:58:40');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_messages_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parent_profile`
--
ALTER TABLE `parent_profile`
  ADD CONSTRAINT `fk_parent_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `parent_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reviews_reviewee` FOREIGN KEY (`reviewee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_reviewer` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tutor_profile`
--
ALTER TABLE `tutor_profile`
  ADD CONSTRAINT `fk_tutor_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tutor_subjects`
--
ALTER TABLE `tutor_subjects`
  ADD CONSTRAINT `fk_tutor_subjects_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tutor_subjects_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
