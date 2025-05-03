-- Create the game_high_scores table

CREATE TABLE IF NOT EXISTS `game_high_scores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `game_id` varchar(50) NOT NULL,
  `score` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_game` (`user_id`, `game_id`),
  CONSTRAINT `game_high_scores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add indexes for faster lookups
CREATE INDEX `game_id_index` ON `game_high_scores` (`game_id`);
CREATE INDEX `user_score_index` ON `game_high_scores` (`user_id`, `score`);
