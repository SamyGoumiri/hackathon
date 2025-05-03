<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

ob_start();

try {
    session_start();
    require_once '../../../database/connect.php';

    error_log("Speed Translate: Received score save request");
    
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not logged in");
    }

    if (!isset($_POST['user_id']) || !isset($_POST['score'])) {
        $postData = print_r($_POST, true);
        error_log("Speed Translate: Missing required data. POST data: " . $postData);
        throw new Exception("Missing required data");
    }

    $user_id = $_POST['user_id'];
    $score = intval($_POST['score']);
    $game_id = 'speed_translate';

    if ($_SESSION['user_id'] != $user_id) {
        error_log("Speed Translate: Session user ID ({$_SESSION['user_id']}) doesn't match posted user ID ($user_id)");
        throw new Exception("Invalid user");
    }
    ob_clean();

    $check_table_query = "CREATE TABLE IF NOT EXISTS `game_high_scores` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `game_id` varchar(50) NOT NULL,
        `score` int(11) NOT NULL,
        `created_at` datetime NOT NULL DEFAULT current_timestamp(),
        `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `user_game` (`user_id`, `game_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

    if (!$conn->query($check_table_query)) {
        throw new Exception("Failed to create game_high_scores table: " . $conn->error);
    }

    $high_score_query = "SELECT score FROM game_high_scores WHERE user_id = ? AND game_id = ?";
    $stmt = $conn->prepare($high_score_query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("is", $user_id, $game_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $high_score_result = $stmt->get_result();
    $is_high_score = false;
    $current_high_score = 0;

    if ($high_score_result->num_rows > 0) {
        $high_score_data = $high_score_result->fetch_assoc();
        $current_high_score = $high_score_data['score'];
        
        if ($score > $current_high_score) {
            $update_query = "UPDATE game_high_scores SET score = ?, updated_at = NOW() WHERE user_id = ? AND game_id = ?";
            $stmt = $conn->prepare($update_query);
            if (!$stmt) {
                throw new Exception("Prepare update failed: " . $conn->error);
            }
            
            $stmt->bind_param("iis", $score, $user_id, $game_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute update failed: " . $stmt->error);
            }
            $is_high_score = true;
            error_log("Speed Translate: Updated high score for user $user_id to $score (was $current_high_score)");
        }
    } else {
        $insert_query = "INSERT INTO game_high_scores (user_id, game_id, score) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        if (!$stmt) {
            throw new Exception("Prepare insert failed: " . $conn->error);
        }
        
        $stmt->bind_param("isi", $user_id, $game_id, $score);
        if (!$stmt->execute()) {
            throw new Exception("Execute insert failed: " . $stmt->error);
        }
        $is_high_score = true;
        error_log("Speed Translate: Inserted first high score for user $user_id: $score");
    }
    $xp_multiplier = $is_high_score ? 10 : 4;
    $xp_earned = $score * $xp_multiplier;
    try {
        $xp_check_query = "SELECT xp_points, level FROM user_experience WHERE user_id = ?";
        $stmt = $conn->prepare($xp_check_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $xp_result = $stmt->get_result();

        if ($xp_result->num_rows > 0) {
            $xp_data = $xp_result->fetch_assoc();
            $current_xp = $xp_data['xp_points'];
            $new_xp = $current_xp + $xp_earned;
            
            $new_level = floor($new_xp / 100) + 1;
            
            $xp_update_query = "UPDATE user_experience SET xp_points = ?, level = ?, last_updated = NOW() WHERE user_id = ?";
            $stmt = $conn->prepare($xp_update_query);
            $stmt->bind_param("iii", $new_xp, $new_level, $user_id);
            $xp_result = $stmt->execute();
            
            error_log("Speed Translate: Updated XP for user $user_id: +$xp_earned (total: $new_xp)");
        } else {
            $new_level = floor($xp_earned / 100) + 1;
            
            $xp_insert_query = "INSERT INTO user_experience (user_id, xp_points, level, last_updated) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($xp_insert_query);
            $stmt->bind_param("iii", $user_id, $xp_earned, $new_level);
            $xp_result = $stmt->execute();
            
            error_log("Speed Translate: Created XP record for user $user_id: $xp_earned");
        }
        try {
            $activity_details = json_encode([
                'game' => $game_id,
                'score' => $score,
                'is_high_score' => $is_high_score,
                'xp_earned' => $xp_earned
            ]);

            $activity_query = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
                            VALUES (?, 'game_play', ?)";
            $stmt = $conn->prepare($activity_query);
            $stmt->bind_param("is", $user_id, $activity_details);
            $activity_result = $stmt->execute();
        } catch (Exception $e) {
            error_log("Speed Translate: Failed to record activity: " . $e->getMessage());
        }
    } catch (Exception $e) {
        error_log("Speed Translate: XP update failed: " . $e->getMessage());
    }
    ob_clean();
    echo json_encode([
        'success' => true, 
        'is_high_score' => $is_high_score, 
        'high_score' => $is_high_score ? $score : $current_high_score,
        'xp_earned' => $xp_earned,
        'xp_multiplier' => $xp_multiplier
    ]);

} catch (Exception $e) {
    ob_clean();
    error_log("Speed Translate Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error processing score', 
        'error' => $e->getMessage()
    ]);
}
ob_end_flush();
?>
