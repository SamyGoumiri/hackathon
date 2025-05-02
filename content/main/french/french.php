<?php
session_start();
require_once "../../../database/connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = "SELECT username, first_name, last_name FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Log activity
$log_query = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
              VALUES (?, 'language_page_access', ?)";
$details = json_encode(['language' => 'french']);
$stmt = $conn->prepare($log_query);
$stmt->bind_param("is", $user_id, $details);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="french.css">
    <title>Lango - French</title>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Lango</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../dashboard.php">Dashboard</a></li>
                    <li><a href="#" class="active">French</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="selection-container">
        <h1>French Language Learning</h1>
        <h2>What would you like to do today?</h2>
        <div class="options">
            <a href="courses.php" class="card">
                <img width="100" height="100" src="https://img.icons8.com/isometric/100/book-stack.png" alt="book-stack"/>
                <h3>Courses</h3>
                <p>Structured lessons to guide your learning journey.</p>
            </a>
            <a href="exercises.php" class="card">
                <img width="100" height="100" src="https://img.icons8.com/fluency/100/goal--v1.png" alt="goal--v1"/>
                <h3>Practice</h3>
                <p>Interactive exercises to reinforce what you've learned.</p>
            </a>
        </div>
    </div>
</body>
</html>
