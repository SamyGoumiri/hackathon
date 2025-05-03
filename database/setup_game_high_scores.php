<?php
require_once 'connect.php';

// Check if the table exists
$table_exists = $conn->query("SHOW TABLES LIKE 'game_high_scores'")->num_rows > 0;

if (!$table_exists) {
    // Create the game_high_scores table
    $create_table_sql = "
        CREATE TABLE `game_high_scores` (
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
    ";
    
    if ($conn->query($create_table_sql)) {
        echo "Created game_high_scores table successfully.<br>";
        
        // Add indexes for faster lookups
        $conn->query("CREATE INDEX `game_id_index` ON `game_high_scores` (`game_id`)");
        $conn->query("CREATE INDEX `user_score_index` ON `game_high_scores` (`user_id`, `score`)");
        
        // Migrate existing high scores from test_results
        $migrate_sql = "
            INSERT INTO game_high_scores (user_id, game_id, score, created_at, updated_at)
            SELECT user_id, 'speed_translate', MAX(score), MIN(completion_date), NOW()
            FROM test_results
            WHERE test_id = 0
            GROUP BY user_id
        ";
        
        if ($conn->query($migrate_sql)) {
            echo "Migrated existing high scores successfully.";
        } else {
            echo "Error migrating existing scores: " . $conn->error;
        }
    } else {
        echo "Error creating table: " . $conn->error;
    }
} else {
    echo "The game_high_scores table already exists.";
}

$conn->close();
?>
