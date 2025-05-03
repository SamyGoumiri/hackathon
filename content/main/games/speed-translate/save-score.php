<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

session_start();
require_once '../../../database/connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if (!isset($_POST['user_id']) || !isset($_POST['score'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit();
}

$user_id = (int)$_POST['user_id'];
$score = (int)$_POST['score'];
$game_id = 'speed_translate';

if ($_SESSION['user_id'] != $user_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid user']);
    exit();
}

$conn->query("CREATE TABLE IF NOT EXISTS `game_high_scores` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `game_id` varchar(50) NOT NULL,
    `score` int(11) NOT NULL,
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_game` (`user_id`, `game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

$stmt = $conn->prepare("SELECT score FROM game_high_scores WHERE user_id = ? AND game_id = ?");
$stmt->bind_param("is", $user_id, $game_id);
$stmt->execute();
$result = $stmt->get_result();
$is_high_score = false;
$current_high_score = 0;

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $current_high_score = $data['score'];
    
    if ($score > $current_high_score) {
        $stmt = $conn->prepare("UPDATE game_high_scores SET score = ?, updated_at = NOW() WHERE user_id = ? AND game_id = ?");
        $stmt->bind_param("iis", $score, $user_id, $game_id);
        $stmt->execute();
        $is_high_score = true;
    }
} else {
    $stmt = $conn->prepare("INSERT INTO game_high_scores (user_id, game_id, score) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $user_id, $game_id, $score);
    $stmt->execute();
    $is_high_score = true;
}

$xp_multiplier = $is_high_score ? 10 : 4;
$xp_earned = $score * $xp_multiplier;

$stmt = $conn->prepare("SELECT xp_points, level FROM user_experience WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$xp_result = $stmt->get_result();

if ($xp_result->num_rows > 0) {
    $xp_data = $xp_result->fetch_assoc();
    $current_xp = $xp_data['xp_points'];
    $new_xp = $current_xp + $xp_earned;
    $new_level = floor($new_xp / 100) + 1;
    
    $stmt = $conn->prepare("UPDATE user_experience SET xp_points = ?, level = ?, last_updated = NOW() WHERE user_id = ?");
    $stmt->bind_param("iii", $new_xp, $new_level, $user_id);
    $stmt->execute();
} else {
    $new_level = floor($xp_earned / 100) + 1;
    $stmt = $conn->prepare("INSERT INTO user_experience (user_id, xp_points, level, last_updated) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iii", $user_id, $xp_earned, $new_level);
    $stmt->execute();
}

$activity_details = json_encode([
    'game' => $game_id,
    'score' => $score,
    'is_high_score' => $is_high_score,
    'xp_earned' => $xp_earned
]);

$stmt = $conn->prepare("INSERT INTO user_activity (user_id, activity_type, activity_details) VALUES (?, 'game_play', ?)");
$stmt->bind_param("is", $user_id, $activity_details);
$stmt->execute();

echo json_encode([
    'success' => true, 
    'is_high_score' => $is_high_score, 
    'high_score' => $is_high_score ? $score : $current_high_score,
    'xp_earned' => $xp_earned,
    'xp_multiplier' => $xp_multiplier
]);
?>
