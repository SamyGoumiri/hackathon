<?php
session_start();
require_once '../../../database/connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if (!isset($_POST['user_id']) || !isset($_POST['score']) || !isset($_POST['test_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit();
}

$user_id = $_POST['user_id'];
$score = $_POST['score'];
$game_id = 'speed_translate'; // Use a string identifier for the game

if ($_SESSION['user_id'] != $user_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid user']);
    exit();
}

// First, check if the user has a high score for this game
$high_score_query = "SELECT score FROM game_high_scores WHERE user_id = ? AND game_id = ?";
$stmt = $conn->prepare($high_score_query);
$stmt->bind_param("is", $user_id, $game_id);
$stmt->execute();
$high_score_result = $stmt->get_result();
$is_high_score = false;

if ($high_score_result->num_rows > 0) {
    // User has an existing high score, check if current score is higher
    $high_score_data = $high_score_result->fetch_assoc();
    $current_high_score = $high_score_data['score'];
    
    if ($score > $current_high_score) {
        // Update the existing high score
        $update_query = "UPDATE game_high_scores SET score = ?, updated_at = NOW() WHERE user_id = ? AND game_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("iis", $score, $user_id, $game_id);
        $result = $stmt->execute();
        $is_high_score = true;
    }
} else {
    // This is the user's first score for this game, insert it
    $insert_query = "INSERT INTO game_high_scores (user_id, game_id, score, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("isi", $user_id, $game_id, $score);
    $result = $stmt->execute();
    $is_high_score = true;
}

// Always record the activity
$activity_details = json_encode([
    'game' => $game_id,
    'score' => $score,
    'is_high_score' => $is_high_score
]);

$activity_query = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
                   VALUES (?, 'game_play', ?)";
$stmt = $conn->prepare($activity_query);
$stmt->bind_param("is", $user_id, $activity_details);
$stmt->execute();

echo json_encode(['success' => true, 'is_high_score' => $is_high_score, 'high_score' => $is_high_score ? $score : $current_high_score ?? 0]);
?>
