-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 02 mai 2025 à 19:02
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `Esperanto`
--

-- --------------------------------------------------------

--
-- Structure de la table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `difficulty` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `courses`
--

INSERT INTO `courses` (`course_id`, `language_id`, `title`, `description`, `difficulty`, `image_path`, `is_active`, `created_at`) VALUES
(1, 3, 'French Fundamentals', 'Master the basics of French language with this comprehensive beginner course.', 'beginner', 'french_basic.jpg', 1, '2025-05-02 13:47:07'),
(2, 3, 'Intermediate French', 'Take your French skills to the next level with more advanced concepts and vocabulary.', 'intermediate', 'french_intermediate.jpg', 1, '2025-05-02 13:47:07'),
(3, 2, 'Spanish for Beginners', 'Learn essential Spanish vocabulary and grammar for everyday conversations.', 'beginner', 'spanish_basic.jpg', 1, '2025-05-02 13:47:07'),
(4, 5, 'Italian Fundamentals', 'Master the basics of Italian language with this comprehensive beginner course.', 'beginner', 'italian_basic.jpg', 1, '2025-05-02 13:47:07'),
(5, 4, 'German Fundamentals', 'Master the basics of German language with this comprehensive beginner course.', 'beginner', 'german_basic.jpg', 1, '2025-05-02 13:47:07');

-- --------------------------------------------------------

--
-- Structure de la table `languages`
--

CREATE TABLE `languages` (
  `language_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` varchar(5) NOT NULL,
  `flag_icon` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `languages`
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
-- Structure de la table `lessons`
--

CREATE TABLE `lessons` (
  `lesson_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `order_index` int(11) NOT NULL DEFAULT 1,
  `estimated_time` int(11) DEFAULT NULL COMMENT 'Estimated completion time in minutes',
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `lessons`
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
(20, 4, 'Travel Problems', 'How to handle common issues that may arise when traveling.', 5, 20, 1),
(21, 5, 'Saluti e Presentazioni', 'Learn how to say hello and introduce yourself in Italian.', 1, 15, 1),
(22, 5, 'Pronuncia di Base', 'Master the essential sounds of Italian language.', 2, 20, 1),
(23, 5, 'Numeri 1-20', 'Learn how to count from 1 to 20 in Italian.', 3, 15, 1),
(24, 5, 'Domande Semplici', 'Learn how to ask and answer basic questions in Italian.', 4, 20, 1),
(25, 5, 'Frasi Comuni', 'Essential phrases to help you in everyday situations.', 5, 15, 1),
(26, 6, 'Routine Quotidiane', 'Vocabulary for describing your daily activities.', 1, 20, 1),
(27, 6, 'Verbi al Presente', 'Learn how to conjugate common verbs in present tense.', 2, 25, 1),
(28, 6, 'Dire l\'Ora', 'Learn how to tell and ask for time in Italian.', 3, 15, 1),
(29, 6, 'Giorni e Mesi', 'Learn the days of the week and months of the year.', 4, 15, 1),
(30, 6, 'Espressioni sul Tempo', 'Describe different weather conditions in Italian.', 5, 15, 1),
(31, 7, 'Al Supermercato', 'Learn vocabulary and expressions for grocery shopping.', 1, 20, 1),
(32, 7, 'Al Ristorante', 'How to order food and interact with waitstaff in Italian.', 2, 25, 1),
(33, 7, 'Shopping per Vestiti', 'Vocabulary for clothing items and shopping expressions.', 3, 20, 1),
(34, 7, 'Denaro e Numeri', 'Learn about euros and how to discuss prices in Italian.', 4, 15, 1),
(35, 7, 'Fare Acquisti', 'Practice conversations for making purchases in different settings.', 5, 20, 1),
(36, 8, 'Vocabolario dei Trasporti', 'Learn words for different modes of transportation in Italian.', 1, 15, 1),
(37, 8, 'Chiedere Indicazioni', 'How to ask for and understand directions in Italian.', 2, 20, 1),
(38, 8, 'Prenotazioni Alberghiere', 'Vocabulary and phrases for booking and staying at hotels.', 3, 20, 1),
(39, 8, 'Attrazioni Turistiche', 'Discussing sightseeing and cultural attractions in Italian.', 4, 25, 1),
(40, 8, 'Problemi di Viaggio', 'How to handle common issues that may arise when traveling.', 5, 20, 1),
(41, 9, 'Saludos y Presentaciones', 'Learn how to say hello and introduce yourself in Spanish.', 1, 15, 1),
(42, 9, 'Pronunciación Básica', 'Master the essential sounds of Spanish language.', 2, 20, 1),
(43, 9, 'Números 1-20', 'Learn how to count from 1 to 20 in Spanish.', 3, 15, 1),
(44, 9, 'Preguntas Simples', 'Learn how to ask and answer basic questions in Spanish.', 4, 20, 1),
(45, 9, 'Frases Comunes', 'Essential phrases to help you in everyday situations.', 5, 15, 1),
(46, 10, 'Rutinas Diarias', 'Vocabulary for describing your daily activities.', 1, 20, 1),
(47, 10, 'Verbos en Presente', 'Learn how to conjugate common verbs in present tense.', 2, 25, 1),
(48, 10, 'Decir la Hora', 'Learn how to tell and ask for time in Spanish.', 3, 15, 1),
(49, 10, 'Días y Meses', 'Learn the days of the week and months of the year.', 4, 15, 1),
(50, 10, 'Expresiones sobre el Clima', 'Describe different weather conditions in Spanish.', 5, 15, 1),
(51, 11, 'En el Supermercado', 'Learn vocabulary and expressions for grocery shopping.', 1, 20, 1),
(52, 11, 'En el Restaurante', 'How to order food and interact with waitstaff in Spanish.', 2, 25, 1),
(53, 11, 'Comprando Ropa', 'Vocabulary for clothing items and shopping expressions.', 3, 20, 1),
(54, 11, 'Dinero y Números', 'Learn about euros and how to discuss prices in Spanish.', 4, 15, 1),
(55, 11, 'Haciendo Compras', 'Practice conversations for making purchases in different settings.', 5, 20, 1),
(56, 12, 'Vocabulario de Transporte', 'Learn words for different modes of transportation in Spanish.', 1, 15, 1),
(57, 12, 'Pidiendo Direcciones', 'How to ask for and understand directions in Spanish.', 2, 20, 1),
(58, 12, 'Reservaciones de Hotel', 'Vocabulary and phrases for booking and staying at hotels.', 3, 20, 1),
(59, 12, 'Atracciones Turísticas', 'Discussing sightseeing and cultural attractions in Spanish.', 4, 25, 1),
(60, 12, 'Problemas de Viaje', 'How to handle common issues that may arise when traveling.', 5, 20, 1),
(61, 13, 'Begrüßungen und Vorstellungen', 'Learn how to say hello and introduce yourself in German.', 1, 15, 1),
(62, 13, 'Grundlegende Aussprache', 'Master the essential sounds of German language.', 2, 20, 1),
(63, 13, 'Zahlen 1-20', 'Learn how to count from 1 to 20 in German.', 3, 15, 1),
(64, 13, 'Einfache Fragen', 'Learn how to ask and answer basic questions in German.', 4, 20, 1),
(65, 13, 'Häufige Ausdrücke', 'Essential phrases to help you in everyday situations.', 5, 15, 1),
(66, 14, 'Tägliche Routinen', 'Vocabulary for describing your daily activities.', 1, 20, 1),
(67, 14, 'Verben im Präsens', 'Learn how to conjugate common verbs in present tense.', 2, 25, 1),
(68, 14, 'Die Uhrzeit', 'Learn how to tell and ask for time in German.', 3, 15, 1),
(69, 14, 'Wochentage und Monate', 'Learn the days of the week and months of the year.', 4, 15, 1),
(70, 14, 'Wetterausdrücke', 'Describe different weather conditions in German.', 5, 15, 1),
(71, 15, 'Im Supermarkt', 'Learn vocabulary and expressions for grocery shopping in German.', 1, 20, 1),
(72, 15, 'Im Restaurant', 'How to order food and interact with waitstaff in German.', 2, 25, 1),
(73, 15, 'Kleidung Einkaufen', 'Vocabulary for clothing items and shopping expressions in German.', 3, 20, 1),
(74, 15, 'Geld und Zahlen', 'Learn about euros and how to discuss prices in German.', 4, 15, 1),
(75, 15, 'Etwas kaufen', 'Practice conversations for making purchases in different settings in German.', 5, 20, 1),
(76, 16, 'Verkehrsmittel', 'Learn words for different modes of transportation in German.', 1, 15, 1),
(77, 16, 'Nach dem Weg fragen', 'How to ask for and understand directions in German.', 2, 20, 1),
(78, 16, 'Hotelreservierungen', 'Vocabulary and phrases for booking and staying at hotels in German.', 3, 20, 1),
(79, 16, 'Sehenswürdigkeiten', 'Discussing sightseeing and cultural attractions in German.', 4, 25, 1),
(80, 16, 'Reiseprobleme', 'How to handle common issues that may arise when traveling in German.', 5, 20, 1);

-- --------------------------------------------------------

--
-- Structure de la table `units`
--

CREATE TABLE `units` (
  `unit_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `order_index` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `units`
--

INSERT INTO `units` (`unit_id`, `course_id`, `title`, `description`, `order_index`, `is_active`) VALUES
(1, 1, 'Les Bases (The Basics)', 'Learn the foundation of French with basic greetings, introductions, and essential phrases.', 1, 1),
(2, 1, 'La Vie Quotidienne (Daily Life)', 'Practice everyday conversations and expand your vocabulary for daily activities.', 2, 1),
(3, 1, 'Faire des Courses (Shopping)', 'Learn vocabulary for shopping, dining, and handling money in French-speaking countries.', 3, 1),
(4, 1, 'Les Voyages (Traveling)', 'Navigate travel situations with confidence using specialized vocabulary and phrases.', 4, 1),
(5, 4, 'Le Basi (The Basics)', 'Learn the foundation of Italian with basic greetings, introductions, and essential phrases.', 1, 1),
(6, 4, 'La Vita Quotidiana (Daily Life)', 'Practice everyday conversations and expand your vocabulary for daily activities.', 2, 1),
(7, 4, 'Fare Acquisti (Shopping)', 'Learn vocabulary for shopping, dining, and handling money in Italian-speaking countries.', 3, 1),
(8, 4, 'I Viaggi (Traveling)', 'Navigate travel situations with confidence using specialized vocabulary and phrases.', 4, 1),
(9, 3, 'Los Fundamentos (The Basics)', 'Learn the foundation of Spanish with basic greetings, introductions, and essential phrases.', 1, 1),
(10, 3, 'La Vida Cotidiana (Daily Life)', 'Practice everyday conversations and expand your vocabulary for daily activities.', 2, 1),
(11, 3, 'De Compras (Shopping)', 'Learn vocabulary for shopping, dining, and handling money in Spanish-speaking countries.', 3, 1),
(12, 3, 'Los Viajes (Traveling)', 'Navigate travel situations with confidence using specialized vocabulary and phrases.', 4, 1),
(13, 5, 'Die Grundlagen (The Basics)', 'Learn the foundation of German with basic greetings, introductions, and essential phrases.', 1, 1),
(14, 5, 'Das tägliche Leben (Daily Life)', 'Practice everyday conversations and expand your vocabulary for daily activities.', 2, 1),
(15, 5, 'Einkaufen (Shopping)', 'Learn vocabulary for shopping, dining, and handling money in German-speaking countries.', 3, 1),
(16, 5, 'Das Reisen (Traveling)', 'Navigate travel situations with confidence using specialized vocabulary and phrases.', 4, 1);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT 'default.png',
  `registration_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`user_id`, `username`, `first_name`, `last_name`, `email`, `password`, `profile_image`, `registration_date`, `last_login`, `remember_token`, `is_active`) VALUES
(1, 'admin', 'Aziz', 'Boula', 'admin@esperanto.com', '$2y$10$Cerx0cFH8GZ7rDr1zEajhe3J/t9w4bqYZWtb4rEdscG/oOfK7Jb.S', 'default.png', '2025-05-02 14:48:13', '2025-05-02 14:55:19', NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `user_activity`
--

CREATE TABLE `user_activity` (
  `activity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL COMMENT 'Types: lesson_access, unit_access, language_page_access, unit_test, start_language, etc.',
  `activity_details` text DEFAULT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_activity`
--

INSERT INTO `user_activity` (`activity_id`, `user_id`, `activity_type`, `activity_details`, `timestamp`) VALUES
(1, 1, 'start_language', '{\"language_id\":4,\"language_code\":\"de\"}', '2025-05-02 14:56:03'),
(2, 1, 'language_page_access', '{\"language\":\"german\",\"proficiency_level\":\"beginner\"}', '2025-05-02 14:56:03'),
(3, 1, 'language_page_access', '{\"language\":\"german\",\"proficiency_level\":\"beginner\"}', '2025-05-02 14:56:06'),
(4, 1, 'language_page_access', '{\"language\":\"german\",\"proficiency_level\":\"beginner\"}', '2025-05-02 15:51:40'),
(5, 1, 'language_page_access', '{\"language\":\"german\",\"proficiency_level\":\"beginner\"}', '2025-05-02 15:52:11'),
(6, 1, 'language_page_access', '{\"language\":\"german\",\"proficiency_level\":\"beginner\"}', '2025-05-02 15:52:14'),
(7, 1, 'language_page_access', '{\"language\":\"german\",\"proficiency_level\":\"beginner\"}', '2025-05-02 15:52:32'),
(8, 1, 'start_language', '{\"language_id\":2,\"language_code\":\"es\"}', '2025-05-02 15:52:35'),
(9, 1, 'language_page_access', '{\"language\":\"spanish\",\"proficiency_level\":\"beginner\"}', '2025-05-02 15:52:35'),
(10, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 15:55:04'),
(11, 1, 'lesson_access', '{\"lesson_id\":41,\"lesson_title\":\"Saludos y Presentaciones\",\"unit_id\":9}', '2025-05-02 15:55:05'),
(12, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 15:55:08'),
(13, 1, 'lesson_access', '{\"lesson_id\":41,\"lesson_title\":\"Saludos y Presentaciones\",\"unit_id\":9}', '2025-05-02 15:55:10'),
(14, 1, 'lesson_access', '{\"lesson_id\":41,\"lesson_title\":\"Saludos y Presentaciones\",\"unit_id\":9}', '2025-05-02 15:57:23'),
(15, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 15:57:23'),
(16, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 15:57:24'),
(17, 1, 'lesson_access', '{\"lesson_id\":41,\"lesson_title\":\"Saludos y Presentaciones\",\"unit_id\":9}', '2025-05-02 15:57:25'),
(18, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 15:57:35'),
(19, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 15:57:37'),
(20, 1, 'lesson_access', '{\"lesson_id\":41,\"lesson_title\":\"Saludos y Presentaciones\",\"unit_id\":9}', '2025-05-02 15:57:38'),
(21, 1, 'lesson_access', '{\"lesson_id\":41,\"lesson_title\":\"Saludos y Presentaciones\",\"unit_id\":9}', '2025-05-02 16:05:01'),
(22, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:05:03'),
(23, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:05:06'),
(24, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:05:10'),
(25, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:05:15'),
(26, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:07:03'),
(27, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:07:04'),
(28, 1, 'lesson_access', '{\"lesson_id\":41,\"lesson_title\":\"Saludos y Presentaciones\",\"unit_id\":9}', '2025-05-02 16:07:04'),
(29, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:07:06'),
(30, 1, 'lesson_access', '{\"lesson_id\":41,\"lesson_title\":\"Saludos y Presentaciones\",\"unit_id\":9}', '2025-05-02 16:07:07'),
(31, 1, 'lesson_access', '{\"lesson_id\":41,\"lesson_title\":\"Saludos y Presentaciones\",\"unit_id\":9}', '2025-05-02 16:07:09'),
(32, 1, 'lesson_access', '{\"lesson_id\":41,\"lesson_title\":\"Saludos y Presentaciones\",\"unit_id\":9}', '2025-05-02 16:07:09'),
(33, 1, 'lesson_access', '{\"lesson_id\":41,\"lesson_title\":\"Saludos y Presentaciones\",\"unit_id\":9}', '2025-05-02 16:07:11'),
(34, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:08:57'),
(35, 1, 'lesson_access', '{\"lesson_id\":42,\"lesson_title\":\"Pronunciaci\\u00f3n B\\u00e1sica\",\"unit_id\":9}', '2025-05-02 16:08:58'),
(36, 1, 'lesson_access', '{\"lesson_id\":42,\"lesson_title\":\"Pronunciaci\\u00f3n B\\u00e1sica\",\"unit_id\":9}', '2025-05-02 16:09:00'),
(37, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:09:00'),
(38, 1, 'lesson_access', '{\"lesson_id\":41,\"lesson_title\":\"Saludos y Presentaciones\",\"unit_id\":9}', '2025-05-02 16:09:02'),
(39, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:09:03'),
(40, 1, 'lesson_access', '{\"lesson_id\":43,\"lesson_title\":\"N\\u00fameros 1-20\",\"unit_id\":9}', '2025-05-02 16:09:06'),
(41, 1, 'lesson_access', '{\"lesson_id\":43,\"lesson_title\":\"N\\u00fameros 1-20\",\"unit_id\":9}', '2025-05-02 16:09:08'),
(42, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:09:08'),
(43, 1, 'lesson_access', '{\"lesson_id\":44,\"lesson_title\":\"Preguntas Simples\",\"unit_id\":9}', '2025-05-02 16:09:09'),
(44, 1, 'lesson_access', '{\"lesson_id\":44,\"lesson_title\":\"Preguntas Simples\",\"unit_id\":9}', '2025-05-02 16:09:15'),
(45, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:09:15'),
(46, 1, 'lesson_access', '{\"lesson_id\":45,\"lesson_title\":\"Frases Comunes\",\"unit_id\":9}', '2025-05-02 16:09:16'),
(47, 1, 'lesson_access', '{\"lesson_id\":45,\"lesson_title\":\"Frases Comunes\",\"unit_id\":9}', '2025-05-02 16:09:18'),
(48, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:09:18'),
(49, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:09:23'),
(50, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:11:33'),
(51, 1, 'unit_access', '{\"unit_id\":1,\"unit_title\":\"Les Bases (The Basics)\"}', '2025-05-02 16:11:34'),
(52, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:11:37'),
(53, 1, 'unit_access', '{\"unit_id\":1,\"unit_title\":\"Les Bases (The Basics)\"}', '2025-05-02 16:11:41'),
(54, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:11:46'),
(55, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:12:02'),
(56, 1, 'unit_access', '{\"unit_id\":1,\"unit_title\":\"Les Bases (The Basics)\"}', '2025-05-02 16:12:04'),
(57, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:12:10'),
(58, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:27:04'),
(59, 1, 'lesson_access', '{\"lesson_id\":41,\"lesson_title\":\"Saludos y Presentaciones\",\"unit_id\":9}', '2025-05-02 16:27:04'),
(60, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 16:27:06'),
(61, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 17:25:25'),
(62, 1, 'unit_access', '{\"unit_id\":9,\"unit_title\":\"Los Fundamentos (The Basics)\"}', '2025-05-02 17:25:28'),
(63, 1, 'lesson_access', '{\"lesson_id\":45,\"lesson_title\":\"Frases Comunes\",\"unit_id\":9}', '2025-05-02 17:25:29'),
(64, 1, 'language_page_access', '{\"language\":\"german\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:25:39'),
(65, 1, 'language_page_access', '{\"language\":\"german\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:28:44'),
(66, 1, 'language_page_access', '{\"language\":\"spanish\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:28:47'),
(67, 1, 'unit_access', '{\"unit_id\":10,\"unit_title\":\"La Vida Cotidiana (Daily Life)\"}', '2025-05-02 17:28:50'),
(68, 1, 'language_page_access', '{\"language\":\"spanish\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:28:52'),
(69, 1, 'language_page_access', '{\"language\":\"spanish\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:29:48'),
(70, 1, 'unit_access', '{\"unit_id\":11,\"unit_title\":\"De Compras (Shopping)\"}', '2025-05-02 17:30:08'),
(71, 1, 'language_page_access', '{\"language\":\"spanish\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:38:48'),
(72, 1, 'unit_access', '{\"unit_id\":11,\"unit_title\":\"De Compras (Shopping)\"}', '2025-05-02 17:39:49'),
(73, 1, 'language_page_access', '{\"language\":\"spanish\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:47:47'),
(74, 1, 'unit_access', '{\"unit_id\":12,\"unit_title\":\"Los Viajes (Traveling)\"}', '2025-05-02 17:47:55'),
(75, 1, 'language_page_access', '{\"language\":\"spanish\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:47:58'),
(76, 1, 'start_language', '{\"language_id\":3,\"language_code\":\"fr\"}', '2025-05-02 17:48:58'),
(77, 1, 'language_page_access', '{\"language\":\"french\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:48:59'),
(78, 1, 'language_page_access', '{\"language\":\"french\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:49:11'),
(79, 1, 'language_page_access', '{\"language\":\"french\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:49:16'),
(80, 1, 'language_page_access', '{\"language\":\"german\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:50:40'),
(81, 1, 'language_page_access', '{\"language\":\"german\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:50:43'),
(82, 1, 'start_language', '{\"language_id\":5,\"language_code\":\"it\"}', '2025-05-02 17:50:45'),
(83, 1, 'language_page_access', '{\"language\":\"italian\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:50:45'),
(84, 1, 'language_page_access', '{\"language\":\"italian\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:50:52'),
(85, 1, 'language_page_access', '{\"language\":\"french\",\"proficiency_level\":\"beginner\"}', '2025-05-02 17:56:02'),
(86, 1, 'lesson_access', '{\"lesson_id\":1,\"lesson_title\":\"Greetings and Introductions\",\"unit_id\":1}', '2025-04-28 10:15:22'),
(87, 1, 'lesson_access', '{\"lesson_id\":2,\"lesson_title\":\"Basic Pronunciation\",\"unit_id\":1}', '2025-04-29 11:30:45'),
(88, 1, 'lesson_access', '{\"lesson_id\":3,\"lesson_title\":\"Numbers 1-20\",\"unit_id\":1}', '2025-04-30 09:22:18'),
(89, 1, 'lesson_access', '{\"lesson_id\":4,\"lesson_title\":\"Simple Questions\",\"unit_id\":1}', '2025-05-01 14:45:30'),
(90, 1, 'lesson_access', '{\"lesson_id\":5,\"lesson_title\":\"Common Phrases\",\"unit_id\":1}', '2025-05-02 16:20:15');

-- --------------------------------------------------------

--
-- Structure de la table `user_languages`
--

CREATE TABLE `user_languages` (
  `user_language_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `proficiency_level` enum('beginner','intermediate','advanced','fluent') NOT NULL DEFAULT 'beginner',
  `is_learning` tinyint(1) NOT NULL DEFAULT 1,
  `is_native` tinyint(1) NOT NULL DEFAULT 0,
  `start_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_languages`
--

INSERT INTO `user_languages` (`user_language_id`, `user_id`, `language_id`, `proficiency_level`, `is_learning`, `is_native`, `start_date`) VALUES
(1, 1, 4, 'beginner', 1, 0, '2025-05-02 14:56:03'),
(2, 1, 2, 'beginner', 1, 0, '2025-05-02 15:52:35'),
(3, 1, 3, 'beginner', 1, 0, '2025-05-02 17:48:58'),
(4, 1, 5, 'beginner', 1, 0, '2025-05-02 17:50:45');

-- --------------------------------------------------------

--
-- Structure de la table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `preference_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_notifications` tinyint(1) NOT NULL DEFAULT 1,
  `progress_reminders` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_progress`
--

CREATE TABLE `user_progress` (
  `progress_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `status` enum('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started',
  `completion_date` datetime DEFAULT NULL,
  `score` int(11) DEFAULT NULL COMMENT 'Score in percentage if applicable',
  `last_activity` datetime NOT NULL DEFAULT current_timestamp(),
  `attempts` int(11) DEFAULT 0 COMMENT 'Number of attempts at the lesson or test'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_progress`
--

INSERT INTO `user_progress` (`progress_id`, `user_id`, `lesson_id`, `status`, `completion_date`, `score`, `last_activity`, `attempts`) VALUES
(1, 1, 41, 'completed', '2025-05-02 16:07:09', NULL, '2025-05-02 16:07:09', 1),
(2, 1, 42, 'completed', '2025-05-02 16:09:00', NULL, '2025-05-02 16:09:00', 1),
(3, 1, 43, 'completed', '2025-05-02 16:09:08', NULL, '2025-05-02 16:09:08', 1),
(4, 1, 44, 'completed', '2025-05-02 16:09:15', NULL, '2025-05-02 16:09:15', 1),
(5, 1, 45, 'completed', '2025-05-02 16:09:18', NULL, '2025-05-02 16:09:18', 1),
(6, 1, 1, 'completed', '2025-04-28 10:35:22', 95, '2025-04-28 10:35:22', 1),
(7, 1, 2, 'completed', '2025-04-29 11:50:45', 88, '2025-04-29 11:50:45', 1),
(8, 1, 3, 'completed', '2025-04-30 09:40:18', 92, '2025-04-30 09:40:18', 2),
(9, 1, 4, 'completed', '2025-05-01 15:05:30', 85, '2025-05-01 15:05:30', 1),
(10, 1, 5, 'completed', '2025-05-02 16:40:15', 90, '2025-05-02 16:40:15', 1),
(11, 1, 61, 'completed', '2025-05-02 14:56:30', 88, '2025-05-02 14:56:30', 1),
(12, 1, 62, 'completed', '2025-05-02 15:10:45', 92, '2025-05-02 15:10:45', 1),
(13, 1, 63, 'completed', '2025-05-02 15:25:18', 85, '2025-05-02 15:25:18', 1),
(14, 1, 64, 'completed', '2025-05-02 15:40:30', 90, '2025-05-02 15:40:30', 2),
(15, 1, 65, 'completed', '2025-05-02 15:55:15', 95, '2025-05-02 15:55:15', 1),
(16, 1, 21, 'completed', '2025-05-02 17:50:55', 80, '2025-05-02 17:50:55', 1),
(17, 1, 22, 'completed', '2025-05-02 17:55:45', 85, '2025-05-02 17:55:45', 2),
(18, 1, 23, 'completed', '2025-05-02 18:10:18', 90, '2025-05-02 18:10:18', 1),
(19, 1, 24, 'completed', '2025-05-02 18:25:30', 95, '2025-05-02 18:25:30', 1),
(20, 1, 25, 'completed', '2025-05-02 18:40:15', 88, '2025-05-02 18:40:15', 1);

-- --------------------------------------------------------

--
-- Structure de la table `unit_tests`
--

CREATE TABLE `unit_tests` (
  `test_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `passing_score` int(11) NOT NULL DEFAULT 60,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `unit_tests`
--

INSERT INTO `unit_tests` (`test_id`, `unit_id`, `title`, `description`, `passing_score`, `is_active`) VALUES
(1, 13, 'Die Grundlagen Test', 'Test your knowledge of German basics, greetings, and essential phrases.', 60, 1),
(2, 14, 'Das tägliche Leben Test', 'Test your knowledge of daily routines, time telling, and weather in German.', 60, 1),
(3, 15, 'Einkaufen Test', 'Test your knowledge of shopping vocabulary and expressions in German.', 60, 1),
(4, 16, 'Das Reisen Test', 'Test your knowledge of travel-related vocabulary and phrases in German.', 60, 1);

-- --------------------------------------------------------

--
-- Structure de la table `test_results`
--

CREATE TABLE `test_results` (
  `result_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `passed` tinyint(1) NOT NULL DEFAULT 0,
  `completion_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `test_results`
--

INSERT INTO `test_results` (`result_id`, `user_id`, `test_id`, `score`, `passed`, `completion_date`) VALUES
(1, 1, 1, 85, 1, '2025-05-02 16:00:00'),
(2, 1, 2, 78, 1, '2025-05-02 17:30:00');

-- --------------------------------------------------------

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Index pour la table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`language_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Index pour la table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`lesson_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Index pour la table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`unit_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `user_activity`
--
ALTER TABLE `user_activity`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `user_languages`
--
ALTER TABLE `user_languages`
  ADD PRIMARY KEY (`user_language_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Index pour la table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`preference_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Index pour la table `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`progress_id`),
  ADD UNIQUE KEY `user_lesson` (`user_id`,`lesson_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Index pour la table `unit_tests`
--
ALTER TABLE `unit_tests`
  ADD PRIMARY KEY (`test_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Index pour la table `test_results`
--
ALTER TABLE `test_results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `test_id` (`test_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `languages`
--
ALTER TABLE `languages`
  MODIFY `language_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `lesson_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT pour la table `units`
--
ALTER TABLE `units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `user_activity`
--
ALTER TABLE `user_activity`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT pour la table `user_languages`
--
ALTER TABLE `user_languages`
  MODIFY `user_language_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `preference_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `unit_tests`
--
ALTER TABLE `unit_tests`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `test_results`
--
ALTER TABLE `test_results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`language_id`);

--
-- Contraintes pour la table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `units_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_activity`
--
ALTER TABLE `user_activity`
  ADD CONSTRAINT `user_activity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_languages`
--
ALTER TABLE `user_languages`
  ADD CONSTRAINT `user_languages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_languages_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`language_id`);

--
-- Contraintes pour la table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_progress_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`lesson_id`);

--
-- Contraintes pour la table `unit_tests`
--
ALTER TABLE `unit_tests`
  ADD CONSTRAINT `unit_tests_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `test_results`
--
ALTER TABLE `test_results`
  ADD CONSTRAINT `test_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `test_results_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `unit_tests` (`test_id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
