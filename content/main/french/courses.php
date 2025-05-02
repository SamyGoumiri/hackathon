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

if(isset($_GET['unit'])) {
    $unit_id = intval($_GET['unit']);
    
    $log_query = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
                 VALUES (?, 'course_access', ?)";
    $details = json_encode(['language' => 'french', 'unit' => $unit_id]);
    $stmt = $conn->prepare($log_query);
    $stmt->bind_param("is", $user_id, $details);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="french.css">
    <title>Lango - French Courses</title>
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

    <div class="content-container">
        <h1>French Courses <img src="https://flagcdn.com/w40/fr.png" alt="French Flag" class="flag-icon"></h1>
        
        <div class="course-list">
            <div class="course-item">
                <div class="course-header">
                    <h2>Unit 1: Les Bases (The Basics)</h2>
                    <span class="difficulty beginner">Beginner</span>
                </div>
                <div class="course-content">
                    <p>Learn the foundation of French with basic greetings, introductions, and essential phrases.</p>
                    <ul class="course-topics">
                        <li><i class='bx bx-check-circle'></i> Greetings and introductions</li>
                        <li><i class='bx bx-check-circle'></i> Basic pronunciation rules</li>
                        <li><i class='bx bx-check-circle'></i> Numbers 1-20</li>
                        <li><i class='bx bx-check-circle'></i> Simple questions</li>
                    </ul>
                    <div class="course-actions">
                        <span class="lesson-count">5 Lessons</span>
                        <a href="course-content.php?unit=1" class="btn btn-primary">Start Unit</a>
                    </div>
                </div>
            </div>
            
            <div class="course-item">
                <div class="course-header">
                    <h2>Unit 2: La Vie Quotidienne (Daily Life)</h2>
                    <span class="difficulty beginner">Beginner</span>
                </div>
                <div class="course-content">
                    <p>Practice everyday conversations and expand your vocabulary for daily activities.</p>
                    <ul class="course-topics">
                        <li><i class='bx bx-check-circle'></i> Talking about your day</li>
                        <li><i class='bx bx-check-circle'></i> Present tense verbs</li>
                        <li><i class='bx bx-check-circle'></i> Times of day</li>
                        <li><i class='bx bx-check-circle'></i> Basic adjectives</li>
                    </ul>
                    <div class="course-actions">
                        <span class="lesson-count">6 Lessons</span>
                        <a href="course-content.php?unit=2" class="btn btn-primary">Start Unit</a>
                    </div>
                </div>
            </div>
            
            <div class="course-item">
                <div class="course-header">
                    <h2>Unit 3: Faire des Courses (Shopping)</h2>
                    <span class="difficulty intermediate">Intermediate</span>
                </div>
                <div class="course-content">
                    <p>Learn vocabulary for shopping, dining, and handling money in French-speaking countries.</p>
                    <ul class="course-topics">
                        <li><i class='bx bx-check-circle'></i> Shopping vocabulary</li>
                        <li><i class='bx bx-check-circle'></i> Restaurant phrases</li>
                        <li><i class='bx bx-check-circle'></i> Numbers and currency</li>
                        <li><i class='bx bx-check-circle'></i> Asking for help</li>
                    </ul>
                    <div class="course-actions">
                        <span class="lesson-count">5 Lessons</span>
                        <a href="course-content.php?unit=3" class="btn btn-primary">Start Unit</a>
                    </div>
                </div>
            </div>
            
            <div class="course-item locked">
                <div class="course-header">
                    <h2>Unit 4: Les Voyages (Traveling)</h2>
                    <span class="difficulty intermediate">Intermediate</span>
                </div>
                <div class="course-content">
                    <p>Navigate travel situations with confidence using specialized vocabulary and phrases.</p>
                    <ul class="course-topics">
                        <li><i class='bx bx-check-circle'></i> Transportation vocab</li>
                        <li><i class='bx bx-check-circle'></i> Directions and locations</li>
                        <li><i class='bx bx-check-circle'></i> Booking accommodations</li>
                        <li><i class='bx bx-check-circle'></i> Travel expressions</li>
                    </ul>
                    <div class="course-actions">
                        <span class="lesson-count">6 Lessons</span>
                        <div class="lock-message">
                            <i class='bx bx-lock-alt'></i> Complete previous units to unlock
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="navigation-buttons">
            <a href="french.php" class="btn btn-secondary">
                <i class='bx bx-arrow-back'></i> Back to French
            </a>
            <a href="exercises.php" class="btn btn-primary">
                Practice Exercises <i class='bx bx-right-arrow-alt'></i>
            </a>
        </div>
    </div>
    
    <script>
        // Toggle course content visibility
        document.querySelectorAll('.course-header').forEach(header => {
            header.addEventListener('click', function() {
                const courseItem = this.parentElement;
                if (!courseItem.classList.contains('locked')) {
                    courseItem.classList.toggle('expanded');
                }
            });
        });
        
        // Dropdown menu toggle
        document.querySelector('.user-info').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('active');
        });
    </script>
</body>
</html>
