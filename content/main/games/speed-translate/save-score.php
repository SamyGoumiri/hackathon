<?php
session_start();
require_once '../../../database/connect.php';

if (!isset($_SESSION['user_id'])) {
    error_log("Speed Translate: User not logged in");
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if (!isset($_POST['user_id']) || !isset($_POST['score']) || !isset($_POST['test_id'])) {
    error_log("Speed Translate: Missing required data - user_id: " . (isset($_POST['user_id']) ? $_POST['user_id'] : 'missing') . 
              ", score: " . (isset($_POST['score']) ? $_POST['score'] : 'missing'));
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit();
}

$user_id = $_POST['user_id'];
$score = intval($_POST['score']);
$game_id = 'speed_translate';

if ($_SESSION['user_id'] != $user_id) {
    error_log("Speed Translate: Session user ID ({$_SESSION['user_id']}) doesn't match posted user ID ($user_id)");
    echo json_encode(['success' => false, 'message' => 'Invalid user']);
    exit();
}

$high_score_query = "SELECT score FROM game_high_scores WHERE user_id = ? AND game_id = ?";
$stmt = $conn->prepare($high_score_query);
$stmt->bind_param("is", $user_id, $game_id);
$stmt->execute();
$high_score_result = $stmt->get_result();
$is_high_score = false;
$current_high_score = 0;

if ($high_score_result->num_rows > 0) {
    $high_score_data = $high_score_result->fetch_assoc();
    $current_high_score = $high_score_data['score'];
    
    if ($score > $current_high_score) {
        $update_query = "UPDATE game_high_scores SET score = ?, updated_at = NOW() WHERE user_id = ? AND game_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("iis", $score, $user_id, $game_id);
        $result = $stmt->execute();
        $is_high_score = true;
        
        if (!$result) {
            error_log("Speed Translate: Failed to update high score: " . $conn->error);
        } else {
            error_log("Speed Translate: Updated high score for user $user_id to $score (was $current_high_score)");
        }
    }
} else {
    $insert_query = "INSERT INTO game_high_scores (user_id, game_id, score, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("isi", $user_id, $game_id, $score);
    $result = $stmt->execute();
    $is_high_score = true;
    
    if (!$result) {
        error_log("Speed Translate: Failed to insert new high score: " . $conn->error);
    } else {
        error_log("Speed Translate: Inserted first high score for user $user_id: $score");
    }
}

$xp_multiplier = $is_high_score ? 10 : 4;
$xp_earned = $score * $xp_multiplier;

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
    
    if (!$xp_result) {
        error_log("Speed Translate: Failed to update XP: " . $conn->error);
    } else {
        error_log("Speed Translate: Updated XP for user $user_id: +$xp_earned (total: $new_xp)");
    }
} else {
    $new_level = floor($xp_earned / 100) + 1;
    
    $xp_insert_query = "INSERT INTO user_experience (user_id, xp_points, level, last_updated) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($xp_insert_query);
    $stmt->bind_param("iii", $user_id, $xp_earned, $new_level);
    $xp_result = $stmt->execute();
    
    if (!$xp_result) {
        error_log("Speed Translate: Failed to insert new XP record: " . $conn->error);
    } else {
        error_log("Speed Translate: Created XP record for user $user_id: $xp_earned");
    }
}

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

if (!$activity_result) {
    error_log("Speed Translate: Failed to record user activity: " . $conn->error);
}

echo json_encode([
    'success' => true, 
    'is_high_score' => $is_high_score, 
    'high_score' => $is_high_score ? $score : $current_high_score,
    'xp_earned' => $xp_earned,
    'xp_multiplier' => $xp_multiplier
]);
?>
