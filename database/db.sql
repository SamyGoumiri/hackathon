-- Database creation
CREATE DATABASE IF NOT EXISTS language_learning_db;
USE language_learning_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT 'default.png',
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME,
    streak_days INT DEFAULT 0,
    xp_points INT DEFAULT 0
);

-- Languages table
CREATE TABLE IF NOT EXISTS languages (
    language_id INT AUTO_INCREMENT PRIMARY KEY,
    language_name VARCHAR(50) UNIQUE NOT NULL,
    language_code VARCHAR(10) UNIQUE NOT NULL,
    flag_icon VARCHAR(100)
);

-- User progress table
CREATE TABLE IF NOT EXISTS user_progress (
    progress_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    language_id INT NOT NULL,
    current_level INT DEFAULT 1,
    completed_lessons INT DEFAULT 0,
    xp_in_language INT DEFAULT 0,
    last_activity DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(language_id) ON DELETE CASCADE
);

-- Insert default languages
INSERT INTO languages (language_name, language_code, flag_icon) VALUES
('English', 'en', 'english_flag.png'),
('Spanish', 'es', 'spanish_flag.png'),
('French', 'fr', 'french_flag.png'),
('Italian', 'it', 'italian_flag.png'),
('Dutch', 'nl', 'dutch_flag.png');
