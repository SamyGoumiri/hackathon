<?php
session_start();
require_once '../../../database/connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Check if all required data is present
if (!isset($_POST['user_id']) || !isset($_POST['score']) || !isset($_POST['test_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit();
}

$user_id = $_POST['user_id'];
$score = $_POST['score'];
$test_id = $_POST['test_id'];

// Validate user_id matches session
if ($_SESSION['user_id'] != $user_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid user']);
    exit();
}

// Save score to test_results table
$query = "INSERT INTO test_results (user_id, test_id, score, passed, completion_date) 
          VALUES (?, ?, ?, 1, NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $test_id, $score);
$result = $stmt->execute();

if ($result) {
    // Log this activity
    $activity_details = json_encode([
        'game' => 'speed_translate',
        'score' => $score
    ]);
    
    $activity_query = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
                       VALUES (?, 'game_play', ?)";
    $stmt = $conn->prepare($activity_query);
    $stmt->bind_param("is", $user_id, $activity_details);
    $stmt->execute();
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
