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
$test_id = $_POST['test_id'];

if ($_SESSION['user_id'] != $user_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid user']);
    exit();
}

$high_score_query = "SELECT MAX(score) as high_score FROM test_results WHERE user_id = ? AND test_id = ?";
$stmt = $conn->prepare($high_score_query);
$stmt->bind_param("ii", $user_id, $test_id);
$stmt->execute();
$high_score_result = $stmt->get_result();
$high_score_data = $high_score_result->fetch_assoc();
$current_high_score = $high_score_data['high_score'] ?? 0;

if ($score > $current_high_score) {
    $query = "INSERT INTO test_results (user_id, test_id, score, passed, completion_date) 
              VALUES (?, ?, ?, 1, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $user_id, $test_id, $score);
    $result = $stmt->execute();

    if ($result) {
        $activity_details = json_encode([
            'game' => 'speed_translate',
            'score' => $score,
            'is_high_score' => true
        ]);
        
        $activity_query = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
                           VALUES (?, 'game_play', ?)";
        $stmt = $conn->prepare($activity_query);
        $stmt->bind_param("is", $user_id, $activity_details);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'is_high_score' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    $activity_details = json_encode([
        'game' => 'speed_translate',
        'score' => $score,
        'is_high_score' => false
    ]);
    
    $activity_query = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
                       VALUES (?, 'game_play', ?)";
    $stmt = $conn->prepare($activity_query);
    $stmt->bind_param("is", $user_id, $activity_details);
    $stmt->execute();
    
    echo json_encode(['success' => true, 'is_high_score' => false]);
}
?>
