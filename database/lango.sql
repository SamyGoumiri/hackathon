-- phpMyAdmin SQL Dump
-- Database schema for Lango - Language Learning Platform

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Lango`
--
CREATE DATABASE IF NOT EXISTS `Lango` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `Lango`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT 'default.png',
  `registration_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `language_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `code` varchar(5) NOT NULL,
  `flag_icon` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`language_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`language_id`, `name`, `code`, `flag_icon`, `is_active`) VALUES
(1, 'English', 'en', 'en-flag.png', 1),
(2, 'Spanish', 'es', 'es-flag.png', 1),
(3, 'French', 'fr', 'fr-flag.png', 1),
(4, 'German', 'de', 'de-flag.png', 1),
(5, 'Italian', 'it', 'it-flag.png', 1),
(6, 'Japanese', 'ja', 'ja-flag.png', 1),
(7, 'Chinese', 'zh', 'zh-flag.png', 1),
(8, 'Arabic', 'ar', 'ar-flag.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_languages`
--

CREATE TABLE `user_languages` (
  `user_language_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `proficiency_level` enum('beginner','intermediate','advanced','fluent') NOT NULL DEFAULT 'beginner',
  `is_learning` tinyint(1) NOT NULL DEFAULT 1,
  `is_native` tinyint(1) NOT NULL DEFAULT 0,
  `start_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_language_id`),
  KEY `user_id` (`user_id`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `preference_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email_notifications` tinyint(1) NOT NULL DEFAULT 1,
  `progress_reminders` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`preference_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_activity`
--

CREATE TABLE `user_activity` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `activity_details` text DEFAULT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`activity_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `difficulty` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`course_id`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `language_id`, `title`, `description`, `difficulty`, `image_path`, `is_active`) VALUES
(1, 3, 'French Fundamentals', 'Master the basics of French language with this comprehensive beginner course.', 'beginner', 'french_basic.jpg', 1),
(2, 3, 'Intermediate French', 'Take your French skills to the next level with more advanced concepts and vocabulary.', 'intermediate', 'french_intermediate.jpg', 1),
(3, 2, 'Spanish for Beginners', 'Learn essential Spanish vocabulary and grammar for everyday conversations.', 'beginner', 'spanish_basic.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `unit_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `order_index` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`unit_id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`unit_id`, `course_id`, `title`, `description`, `order_index`, `is_active`) VALUES
(1, 1, 'Les Bases (The Basics)', 'Learn the foundation of French with basic greetings, introductions, and essential phrases.', 1, 1),
(2, 1, 'La Vie Quotidienne (Daily Life)', 'Practice everyday conversations and expand your vocabulary for daily activities.', 2, 1),
(3, 1, 'Faire des Courses (Shopping)', 'Learn vocabulary for shopping, dining, and handling money in French-speaking countries.', 3, 1),
(4, 1, 'Les Voyages (Traveling)', 'Navigate travel situations with confidence using specialized vocabulary and phrases.', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `lesson_id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `order_index` int(11) NOT NULL DEFAULT 1,
  `estimated_time` int(11) DEFAULT NULL COMMENT 'Estimated completion time in minutes',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`lesson_id`),
  KEY `unit_id` (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`lesson_id`, `unit_id`, `title`, `content`, `order_index`, `estimated_time`, `is_active`) VALUES
(1, 1, 'Greetings and Introductions', 'Learn how to say hello and introduce yourself in French.', 1, 15, 1),
(2, 1, 'Basic Pronunciation', 'Master the essential sounds of French language.', 2, 20, 1),
(3, 1, 'Numbers 1-20', 'Learn how to count from 1 to 20 in French.', 3, 15, 1),
(4, 1, 'Simple Questions', 'Learn how to ask and answer basic questions in French.', 4, 20, 1),
(5, 1, 'Common Phrases', 'Essential phrases to help you in everyday situations.', 5, 15, 1),
(6, 2, 'Daily Routines', 'Vocabulary for describing your daily activities.', 1, 20, 1),
(7, 2, 'Present Tense Verbs', 'Learn how to conjugate common verbs in present tense.', 2, 25, 1),
(8, 2, 'Telling Time', 'Learn how to tell and ask for time in French.', 3, 15, 1),
(9, 2, 'Days and Months', 'Learn the days of the week and months of the year.', 4, 15, 1),
(10, 2, 'Weather Expressions', 'Describe different weather conditions in French.', 5, 15, 1),
(11, 3, 'At the Supermarket', 'Learn vocabulary and expressions for grocery shopping.', 1, 20, 1),
(12, 3, 'At the Restaurant', 'How to order food and interact with waitstaff in French.', 2, 25, 1),
(13, 3, 'Shopping for Clothes', 'Vocabulary for clothing items and shopping expressions.', 3, 20, 1),
(14, 3, 'Money and Numbers', 'Learn about euros and how to discuss prices in French.', 4, 15, 1),
(15, 3, 'Making Purchases', 'Practice conversations for making purchases in different settings.', 5, 20, 1),
(16, 4, 'Transportation Vocabulary', 'Learn words for different modes of transportation in French.', 1, 15, 1),
(17, 4, 'Asking for Directions', 'How to ask for and understand directions in French.', 2, 20, 1),
(18, 4, 'Hotel Reservations', 'Vocabulary and phrases for booking and staying at hotels.', 3, 20, 1),
(19, 4, 'Tourist Attractions', 'Discussing sightseeing and cultural attractions in French.', 4, 25, 1),
(20, 4, 'Travel Problems', 'How to handle common issues that may arise when traveling.', 5, 20, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--

CREATE TABLE `user_progress` (
  `progress_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `status` enum('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started',
  `completion_date` datetime DEFAULT NULL,
  `score` int(11) DEFAULT NULL COMMENT 'Score in percentage if applicable',
  `last_activity` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`progress_id`),
  UNIQUE KEY `user_lesson` (`user_id`,`lesson_id`),
  KEY `lesson_id` (`lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Constraints for table relations
--

ALTER TABLE `user_languages`
  ADD CONSTRAINT `user_languages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_languages_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`language_id`);

ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `user_activity`
  ADD CONSTRAINT `user_activity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`language_id`);

ALTER TABLE `units`
  ADD CONSTRAINT `units_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE;

ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_progress_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`lesson_id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
