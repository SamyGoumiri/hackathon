-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 28, 2025 at 12:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Hippocare`
--

-- --------------------------------------------------------

--
-- Table structure for table `adresse`
--

CREATE TABLE `adresse` (
  `id_adresse` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `street_adresse` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `wilaya` varchar(100) NOT NULL,
  `postal_code` int(10) NOT NULL,
  `country` varchar(100) NOT NULL,
  `id_patient` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adresse`
--

INSERT INTO `adresse` (`id_adresse`, `street_adresse`, `city`, `wilaya`, `postal_code`, `country`, `id_patient`) 
VALUES 
(1, '123 Rue des Oliviers', 'Algiers', 'Algiers', 16000, 'Algeria', 1),
(2, '45 Boulevard Mohamed V', 'Oran', 'Oran', 31000, 'Algeria', 2);

-- --------------------------------------------------------

--
-- Table structure for table `agendareserve`
--

CREATE TABLE `agendareserve` (
  `id_reservation` int(11) NOT NULL AUTO_INCREMENT,
  `id_medecin` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `date_rdv` date NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `statut` enum('Pending','Canceled','Completed') NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`id_reservation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agendareserve`
--

INSERT INTO `agendareserve` (`id_reservation`, `id_medecin`, `id_patient`, `date_rdv`, `heure_debut`, `heure_fin`, `statut`) VALUES
(7, 1, 1, '2025-04-27', '09:00:00', '10:00:00', 'Canceled'),
(8, 1, 1, '2025-04-27', '09:00:00', '10:00:00', 'Canceled'),
(9, 1, 1, '2025-04-27', '10:00:00', '11:00:00', 'Pending'),
(10, 1, 2, '2025-05-24', '11:00:00', '12:00:00', 'Canceled'),
(11, 1, 2, '2025-04-30', '09:00:00', '10:00:00', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `assistant`
--

CREATE TABLE `assistant` (
  `id_assistant` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `Prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `id_medecin` int(11) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  PRIMARY KEY (`id_assistant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assistant`
--

INSERT INTO `assistant` (`id_assistant`, `nom`, `Prenom`, `email`, `telephone`, `id_medecin`, `pwd`) VALUES
(2, 'Goumiri', 'Samy', 'samy.goumiri@esst-sup.com', '0548595328', 1, '$2y$10$fNk4XdbQdIrwe/01PSK/.ejpjaGiRBDEjqM7cRrfrGG3HTXPn3juC');

-- --------------------------------------------------------

--
-- Table structure for table `consultation`
--

CREATE TABLE `consultation` (
  `id_consultation` int(11) NOT NULL AUTO_INCREMENT,
  `id_patient` int(11) NOT NULL,
  `id_medecin` int(11) NOT NULL,
  `horaire_debut` time NOT NULL,
  `horaire_fin` time DEFAULT NULL,
  `statut` enum('InWaiting','aborted','Ongoing','Completed') NOT NULL DEFAULT 'InWaiting',
  PRIMARY KEY (`id_consultation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consultation`
--

INSERT INTO `consultation` (`id_consultation`, `id_patient`, `id_medecin`, `horaire_debut`, `horaire_fin`, `statut`) VALUES
(1, 1, 1, '18:19:27', '18:20:34', 'completed'),
(2, 1, 1, '19:30:17', '19:30:51', 'completed'),
(3, 1, 1, '14:00:00', '15:00:00', 'InWaiting');

-- --------------------------------------------------------

--
-- Table structure for table `contacturgence`
--

CREATE TABLE `contacturgence` (
  `id_contact` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_patient` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `telephone` int(20) NOT NULL,
  `relation` enum('Parent','Conjoint','Enfant','Ami','Autre','Parrain') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacturgence`
--

INSERT INTO `contacturgence` (`id_contact`, `id_patient`, `nom`, `prenom`, `telephone`, `relation`) 
VALUES 
(1, 1, 'Moussa', 'Sara', 0561234567, 'Parent'),
(2, 1, 'Moussa', 'Karim', 0551234567, 'Ami'),
(3, 2, 'Mimouni', 'Ahmed', 0661234567, 'Parent');

-- --------------------------------------------------------

--
-- Table structure for table `detail_consultation`
--

CREATE TABLE `detail_consultation` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_consultation` int(11) NOT NULL,
  `diagnostic` text DEFAULT NULL,
  `traitement` text DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_detail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_consultation`
--

INSERT INTO `detail_consultation` (`id_detail`, `id_consultation`, `diagnostic`, `traitement`, `prix`) VALUES
(1, 1, 'diabete de type 4', 'y a pas grand choses a sauver', 20000.00),
(2, 2, 'hemorrhagic', 'd', 2000000.00),
(3, 3, 'Reason: checkup\n\nNotes: i m sick ', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dossiermedical`
--

CREATE TABLE `dossiermedical` (
  `id_dossier` int(11) NOT NULL AUTO_INCREMENT,
  `id_patient` int(11) NOT NULL,
  `antecedent` text NOT NULL,
  `traitement_en_cours` text NOT NULL,
  `notes_medicales` text NOT NULL,
  PRIMARY KEY (`id_dossier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `horaire`
--

CREATE TABLE `horaire` (
  `id_horaire` int(11) NOT NULL AUTO_INCREMENT,
  `id_medecin` int(11) NOT NULL,
  `jour` enum('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi') NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  PRIMARY KEY (`id_horaire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medecin`
--

CREATE TABLE `medecin` (
  `id_medecin` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `specialite` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  PRIMARY KEY (`id_medecin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medecin`
--

INSERT INTO `medecin` (`id_medecin`, `nom`, `prenom`, `telephone`, `specialite`, `email`, `pwd`) VALUES
(1, 'Djouama', 'Amir', '0779607771', 'Generaliste', 'amir.djouama@esst-sup.com', '$2y$10$jSQAYOCc42uf/uDNZWmY6eUh6i0CJv8ZzgRmxj3FRUSMr5EKWsyDi');

-- --------------------------------------------------------

--
-- Table structure for table `ordonnance`
--

CREATE TABLE `ordonnance` (
  `id_ordonnance` int(11) NOT NULL AUTO_INCREMENT,
  `id_rdv` int(11) NOT NULL,
  `date_prescription` date NOT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`id_ordonnance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `id_patient` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `date_naissance` date NOT NULL,
  `email` varchar(50) NOT NULL,
  `national_id` varchar(20) DEFAULT NULL,
  `pwd` varchar(255) NOT NULL,
  PRIMARY KEY (`id_patient`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`id_patient`, `nom`, `prenom`, `telephone`, `date_naissance`, `email`, `national_id`, `pwd`) VALUES
(1, 'Moussa', 'Elias', '0779607771', '2006-12-03', 'elias.moussa@esst-sup.com', '393r9572979389482', '$2y$10$vA.Py4t8eUmvuouNhE8LrOSj0edVYfqiZE3eh65w53KHGj9fYCuoK'),
(2, 'meriem', 'mimouni', '0758483959', '2005-12-01', 'meriem.mimoumi@esst-sup.com', '39493984938298422', '$2y$10$CXEUHDioClPRKhygkve6MeCRDuTeEPeDuAQ2KxFkeDAq.OBpxsWPq');

-- --------------------------------------------------------

--
-- Table structure for table `patient_documents`
--

CREATE TABLE `patient_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_date` date NOT NULL,
  `document_source` varchar(255) NOT NULL,
  `document_notes` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(10) NOT NULL,
  `upload_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription`
--

CREATE TABLE `prescription` (
  `id_prescription` int(11) NOT NULL AUTO_INCREMENT,
  `id_consultation` int(11) NOT NULL,
  `medication_name` varchar(255) NOT NULL,
  `dosage` varchar(100) NOT NULL,
  `frequency` varchar(100) NOT NULL,
  `duration` varchar(100) NOT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id_prescription`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription`
--

INSERT INTO `prescription` (`id_prescription`, `id_consultation`, `medication_name`, `dosage`, `frequency`, `duration`, `notes`) VALUES
(1, 1, 'ibuprofene', '2 mg', '4', '4 ans', 'ayaya'),
(2, 2, '2', '2', '3', '4', '2');

-- --------------------------------------------------------

--
-- Table structure for table `questionnaire`
--

CREATE TABLE `questionnaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_consultation` int(11) NOT NULL,
  `question` text NOT NULL,
  `reponse` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questionnaire`
--

INSERT INTO `questionnaire` (`id`, `id_consultation`, `question`, `reponse`) VALUES
(1, 1, 'cv ? ', 'oui'),
(2, 2, 'elias', 'oui');

-- --------------------------------------------------------

--
-- Table structure for table `rendezvous`
--

CREATE TABLE `rendezvous` (
  `id_rdv` int(11) NOT NULL AUTO_INCREMENT,
  `id_patient` int(11) NOT NULL,
  `id_medecin` int(11) NOT NULL,
  `date_heure` datetime NOT NULL,
  `statut` enum('Confirmed','Canceled','Completed') NOT NULL,
  PRIMARY KEY (`id_rdv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adresse`
--
ALTER TABLE `adresse`
  ADD KEY `id_patient` (`id_patient`);

--
-- Indexes for table `agendareserve`
--
ALTER TABLE `agendareserve`
  ADD KEY `id_medecin` (`id_medecin`,`id_patient`),
  ADD KEY `id_patient` (`id_patient`);

--
-- Indexes for table `assistant`
--
ALTER TABLE `assistant`
  ADD KEY `id_medecin` (`id_medecin`);

--
-- Indexes for table `consultation`
--
ALTER TABLE `consultation`
  ADD KEY `id_patient` (`id_patient`,`id_medecin`),
  ADD KEY `id_medecin` (`id_medecin`);

--
-- Indexes for table `contacturgence`
--
ALTER TABLE `contacturgence`
  ADD KEY `id_patient` (`id_patient`);

--
-- Indexes for table `detail_consultation`
--
ALTER TABLE `detail_consultation`
  ADD KEY `id_consultation` (`id_consultation`);

--
-- Indexes for table `dossiermedical`
--
ALTER TABLE `dossiermedical`
  ADD KEY `id_patient` (`id_patient`);

--
-- Indexes for table `horaire`
--
ALTER TABLE `horaire`
  ADD KEY `id_medecin` (`id_medecin`);

--
-- Indexes for table `ordonnance`
--
ALTER TABLE `ordonnance`
  ADD KEY `id_rdv` (`id_rdv`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD KEY `idx_patient_nom` (`nom`),
  ADD KEY `idx_patient_prenom` (`prenom`),
  ADD KEY `idx_patient_email` (`email`),
  ADD KEY `idx_patient_telephone` (`telephone`);

--
-- Indexes for table `patient_documents`
--
ALTER TABLE `patient_documents`
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `prescription`
--
ALTER TABLE `prescription`
  ADD KEY `id_consultation` (`id_consultation`);

--
-- Indexes for table `questionnaire`
--
ALTER TABLE `questionnaire`
  ADD KEY `id_consultation` (`id_consultation`);

--
-- Indexes for table `rendezvous`
--
ALTER TABLE `rendezvous`
  ADD KEY `id_patient` (`id_patient`,`id_medecin`),
  ADD KEY `id_medecin` (`id_medecin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adresse`
--
ALTER TABLE `adresse`
  MODIFY `id_adresse` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agendareserve`
--
ALTER TABLE `agendareserve`
  MODIFY `id_reservation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `assistant`
--
ALTER TABLE `assistant`
  MODIFY `id_assistant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `consultation`
--
ALTER TABLE `consultation`
  MODIFY `id_consultation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contacturgence`
--
ALTER TABLE `contacturgence`
  MODIFY `id_contact` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_consultation`
--
ALTER TABLE `detail_consultation`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dossiermedical`
--
ALTER TABLE `dossiermedical`
  MODIFY `id_dossier` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `horaire`
--
ALTER TABLE `horaire`
  MODIFY `id_horaire` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medecin`
--
ALTER TABLE `medecin`
  MODIFY `id_medecin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ordonnance`
--
ALTER TABLE `ordonnance`
  MODIFY `id_ordonnance` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `id_patient` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patient_documents`
--
ALTER TABLE `patient_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescription`
--
ALTER TABLE `prescription`
  MODIFY `id_prescription` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `questionnaire`
--
ALTER TABLE `questionnaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rendezvous`
--
ALTER TABLE `rendezvous`
  MODIFY `id_rdv` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adresse`
--
ALTER TABLE `adresse`
  ADD CONSTRAINT `adresse_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id_patient`);

--
-- Constraints for table `agendareserve`
--
ALTER TABLE `agendareserve`
  ADD CONSTRAINT `agendareserve_ibfk_1` FOREIGN KEY (`id_medecin`) REFERENCES `medecin` (`id_medecin`),
  ADD CONSTRAINT `agendareserve_ibfk_2` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id_patient`);

--
-- Constraints for table `consultation`
--
ALTER TABLE `consultation`
  ADD CONSTRAINT `consultation_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id_patient`),
  ADD CONSTRAINT `consultation_ibfk_2` FOREIGN KEY (`id_medecin`) REFERENCES `medecin` (`id_medecin`);

--
-- Constraints for table `contacturgence`
--
ALTER TABLE `contacturgence`
  ADD CONSTRAINT `contacturgence_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id_patient`);

--
-- Constraints for table `detail_consultation`
--
ALTER TABLE `detail_consultation`
  ADD CONSTRAINT `detail_consultation_ibfk_1` FOREIGN KEY (`id_consultation`) REFERENCES `consultation` (`id_consultation`) ON DELETE CASCADE;

--
-- Constraints for table `dossiermedical`
--
ALTER TABLE `dossiermedical`
  ADD CONSTRAINT `dossiermedical_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id_patient`);

--
-- Constraints for table `horaire`
--
ALTER TABLE `horaire`
  ADD CONSTRAINT `horaire_ibfk_1` FOREIGN KEY (`id_medecin`) REFERENCES `medecin` (`id_medecin`);

--
-- Constraints for table `ordonnance`
--
ALTER TABLE `ordonnance`
  ADD CONSTRAINT `ordonnance_ibfk_1` FOREIGN KEY (`id_rdv`) REFERENCES `rendezvous` (`id_rdv`);

--
-- Constraints for table `patient_documents`
--
ALTER TABLE `patient_documents`
  ADD CONSTRAINT `patient_documents_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id_patient`);

--
-- Constraints for table `prescription`
--
ALTER TABLE `prescription`
  ADD CONSTRAINT `prescription_ibfk_1` FOREIGN KEY (`id_consultation`) REFERENCES `consultation` (`id_consultation`) ON DELETE CASCADE;

--
-- Constraints for table `questionnaire`
--
ALTER TABLE `questionnaire`
  ADD CONSTRAINT `questionnaire_ibfk_1` FOREIGN KEY (`id_consultation`) REFERENCES `consultation` (`id_consultation`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

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
-- Database: `language_learning_db`
--
CREATE DATABASE IF NOT EXISTS `language_learning_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `language_learning_db`;

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
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`course_id`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `lesson_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `content` text NOT NULL,
  `order_number` int(11) NOT NULL,
  `xp_reward` int(11) NOT NULL DEFAULT 10,
  `estimated_minutes` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`lesson_id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--

CREATE TABLE `user_progress` (
  `progress_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `status` enum('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started',
  `score` int(11) DEFAULT NULL,
  `xp_earned` int(11) DEFAULT NULL,
  `completion_date` datetime DEFAULT NULL,
  `last_attempt` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`progress_id`),
  UNIQUE KEY `user_lesson` (`user_id`,`lesson_id`),
  KEY `lesson_id` (`lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `exercise_id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `type` enum('multiple_choice','fill_in_blank','matching','speaking','listening','writing') NOT NULL,
  `instructions` text DEFAULT NULL,
  `difficulty` enum('easy','medium','hard') NOT NULL DEFAULT 'medium',
  `order_number` int(11) NOT NULL,
  PRIMARY KEY (`exercise_id`),
  KEY `lesson_id` (`lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `achievement_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `xp_reward` int(11) NOT NULL DEFAULT 0,
  `requirement_type` enum('streak','lessons','level','words','perfect_score') NOT NULL,
  `requirement_value` int(11) NOT NULL,
  PRIMARY KEY (`achievement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_achievements`
--

CREATE TABLE `user_achievements` (
  `user_achievement_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `achievement_id` int(11) NOT NULL,
  `date_earned` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_achievement_id`),
  UNIQUE KEY `user_achievement` (`user_id`,`achievement_id`),
  KEY `achievement_id` (`achievement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_stats`
--

CREATE TABLE `user_stats` (
  `stat_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `current_streak` int(11) NOT NULL DEFAULT 0,
  `longest_streak` int(11) NOT NULL DEFAULT 0,
  `total_xp` int(11) NOT NULL DEFAULT 0,
  `words_learned` int(11) NOT NULL DEFAULT 0,
  `lessons_completed` int(11) NOT NULL DEFAULT 0,
  `perfect_lessons` int(11) NOT NULL DEFAULT 0,
  `last_activity_date` date DEFAULT NULL,
  PRIMARY KEY (`stat_id`),
  UNIQUE KEY `user_language` (`user_id`,`language_id`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Constraints for table relations
--

ALTER TABLE `user_languages`
  ADD CONSTRAINT `user_languages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_languages_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`language_id`);

ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`language_id`);

ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_progress_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`lesson_id`) ON DELETE CASCADE;

ALTER TABLE `exercises`
  ADD CONSTRAINT `exercises_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`lesson_id`) ON DELETE CASCADE;

ALTER TABLE `user_achievements`
  ADD CONSTRAINT `user_achievements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_achievements_ibfk_2` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`achievement_id`);

ALTER TABLE `user_stats`
  ADD CONSTRAINT `user_stats_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_stats_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`language_id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
