<?php
session_start();
require_once '../../database/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['lang'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$language_code = sanitize_input($conn, $_GET['lang']);

$language_query = "SELECT language_id FROM languages WHERE code = ?";
$stmt = $conn->prepare($language_query);
$stmt->bind_param("s", $language_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$language = $result->fetch_assoc();
$language_id = $language['language_id'];
$check_query = "SELECT user_language_id FROM user_languages WHERE user_id = ? AND language_id = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ii", $user_id, $language_id);
$stmt->execute();
$check_result = $stmt->get_result();

if ($check_result->num_rows === 0) {
    $add_query = "INSERT INTO user_languages (user_id, language_id, proficiency_level, is_learning) 
                  VALUES (?, ?, 'beginner', 1)";
    $stmt = $conn->prepare($add_query);
    $stmt->bind_param("ii", $user_id, $language_id);
    $stmt->execute();
    $log_query = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
                  VALUES (?, 'start_language', ?)";
    $details = json_encode(['language_id' => $language_id, 'language_code' => $language_code]);
    $stmt = $conn->prepare($log_query);
    $stmt->bind_param("is", $user_id, $details);
    $stmt->execute();
}
header("Location: learn.php?lang=" . $language_code);
exit();
?>
