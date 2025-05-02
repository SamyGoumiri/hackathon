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
$language_query = "SELECT ul.proficiency_level, ul.user_language_id 
                   FROM user_languages ul 
                   JOIN languages l ON ul.language_id = l.language_id 
                   WHERE ul.user_id = ? AND l.code = 'it' AND ul.is_learning = 1";
$stmt = $conn->prepare($language_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$lang_result = $stmt->get_result();
if ($lang_result->num_rows === 0) {
    header("Location: ../start_language.php?lang=it");
    exit();
}

$language_data = $lang_result->fetch_assoc();
$proficiency_level = $language_data['proficiency_level'];
$log_query = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
              VALUES (?, 'language_page_access', ?)";
$details = json_encode(['language' => 'italian', 'proficiency_level' => $proficiency_level]);
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
    <link rel="stylesheet" href="../style.css">
    <title>Lango - Italian</title>
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
                    <li><a href="../achievements.php">Achievements</a></li>
                    <li><a href="../chatbot/chatbot.php">ChatBot</a></li>
                </ul>
            </nav>
            <div class="user-menu">
                <div class="user-info">
                    <span><?php echo htmlspecialchars(ucfirst($user['first_name']) . ' ' . ucfirst($user['last_name'])); ?></span>
                    <div class="user-avatar">
                        <span class="user-initials">
                            <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                        </span>
                    </div>
                </div>
                <div class="dropdown-menu">
                    <a href="../profile.php"><i class='bx bx-user'></i> Profile</a>
                    <a href="../settings.php"><i class='bx bx-cog'></i> Settings</a>
                    <a href="../../auth/logout.php"><i class='bx bx-log-out'></i> Log Out</a>
                </div>
            </div>
        </div>
    </header>

    <div class="selection-container">
        <h1>Italian Language Learning</h1>
        <h2>What would you like to do today?</h2>
        <div class="options">
            <a href="units.php" class="card">
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
    
    <script>
        document.querySelector('.user-info').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('active');
        });
    </script>
</body>
</html>
