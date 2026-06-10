-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 05, 2026 at 04:54 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tutorlink`
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
  `subject` varchar(100) NOT NULL,
  `session_date` datetime NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `tutor_id` (`tutor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL,
  `tutor_id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `tutor_id` (`tutor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tutor_availability`
--

DROP TABLE IF EXISTS `tutor_availability`;
CREATE TABLE IF NOT EXISTS `tutor_availability` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tutor_id` int NOT NULL,
  `day_of_week` varchar(20) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `tutor_id` (`tutor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tutor_profiles`
--

DROP TABLE IF EXISTS `tutor_profiles`;
CREATE TABLE IF NOT EXISTS `tutor_profiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `bio` text,
  `experience` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `hourly_rate` decimal(10,2) DEFAULT '0.00',
  `subjects` text,
  `is_verified` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('user','tutor','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `profile_pic` varchar(255) DEFAULT NULL,
  `subjects` text,
  `qualifications` text,
  `profile_completed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `role`, `created_at`, `profile_pic`, `subjects`, `qualifications`, `profile_completed`) VALUES
(1, 'jewel', 'asimi@gmail.com', '$2y$10$iGzb1EMMLoyJ.Dc7asaVfePXRN4ioktt2Fn0lInymFh5PgYDqFWxy', 'tutor', '2026-06-05 09:57:17', NULL, NULL, NULL, 0),
(2, 'Johnson Hezekiah', 'hezzyj1410@gmail.com', '$2y$10$71hQBUTZcCIZ7bSql8HMOecm7E79TifOW72ouUkilyvvCQ60urPxK', 'tutor', '2026-06-05 10:06:02', NULL, NULL, NULL, 0),
(3, 'tems 2011', 'jewel@gmail.com', '$2y$10$HkrGtSKFFBLOTHIVfWgCx.kFmkP9nUUmJzIVssZ5rnhUa6/VvbkL.', 'tutor', '2026-06-05 10:17:58', NULL, NULL, NULL, 0),
(4, 'Hezzy Johnson', 'hezzyj@gmail.com', '$2y$10$HLjfkSiMTKm/.DVfUUvKken/uNv/jVJUZv/.W17YDbAIFAEXeCzeW', '', '2026-06-05 11:25:23', NULL, NULL, NULL, 0),
(5, 'Hezzy Johnson', 'hezzy@gmail.com', '$2y$10$yfuOTq1eWAYcuvQWR6m5uO0Kf.hKgr7ZMumt9GgiwxrBNlB1x90.m', '', '2026-06-05 11:28:39', NULL, NULL, NULL, 0),
(6, 'jewel', 'hezzy3@gmail.com', '$2y$10$jVrUVhlAYsmqdc2r01jDCuYjQXT9wCHJfvIaPetownkGc4vBWFvhy', '', '2026-06-05 12:03:26', NULL, NULL, NULL, 0),
(7, 'Hezzy Johnson', 'johnson@gmail.com', '$2y$10$zTZkj41JYWJA1I1moVxBt.RAMl/ilrQMWNwU4u9vGedxr2tVSCBtC', '', '2026-06-05 12:15:02', NULL, NULL, NULL, 0),
(8, 'tems jewel', 'tems@gmail.com', '$2y$10$v415r5Y0qL0w874q/BilaO6cYX/PLiFWdDHu8Gljd3BgaiUFA1M8.', '', '2026-06-05 12:16:59', NULL, NULL, NULL, 0),
(9, 'asimi', 'kola@gmail.com', '$2y$10$JMAvy//SEAnDS3YpRGYsdOHa0D3u.4OF7Ben2il4/1Aif7sjTbXne', 'tutor', '2026-06-05 12:31:53', NULL, NULL, NULL, 0),
(10, 'asimi', 'asimiabdulmalik935@gmail.com', '$2y$10$k9VcoPhkUQ.5kfxMP1OXA.r.m.lcY9k6XW.KrFSnX9NUq25uUQH9e', '', '2026-06-05 12:35:17', NULL, NULL, NULL, 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
