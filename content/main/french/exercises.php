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

if(isset($_GET['type'])) {
    $exercise_type = $_GET['type'];
    
    $log_query = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
                 VALUES (?, 'exercise_access', ?)";
    $details = json_encode(['language' => 'french', 'exercise_type' => $exercise_type]);
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
    <title>Lango - French Practice</title>
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

    <div class="content-container">
        <h1>French Practice <img src="https://flagcdn.com/w40/fr.png" alt="French Flag" class="flag-icon"></h1>
        
        <div class="exercise-filters">
            <button class="filter-btn active" data-filter="all">All Exercises</button>
            <button class="filter-btn" data-filter="vocabulary">Vocabulary</button>
            <button class="filter-btn" data-filter="grammar">Grammar</button>
            <button class="filter-btn" data-filter="listening">Listening</button>
            <button class="filter-btn" data-filter="reading">Reading</button>
        </div>
        
        <div class="exercise-list">
            <div class="exercise-item" data-type="vocabulary">
                <div class="exercise-icon">
                    <i class='bx bx-book-open'></i>
                </div>
                <div class="exercise-content">
                    <h2>Basic French Vocabulary</h2>
                    <p>Test your knowledge of common French words and phrases through flashcards and matching exercises.</p>
                    <div class="exercise-meta">
                        <span class="difficulty beginner">Beginner</span>
                        <span class="exercise-time"><i class='bx bx-time'></i> 10 min</span>
                    </div>
                    <a href="exercise-content.php?type=vocabulary&level=1" class="btn btn-primary">Start Exercise</a>
                </div>
            </div>
            
            <div class="exercise-item" data-type="grammar">
                <div class="exercise-icon">
                    <i class='bx bx-edit'></i>
                </div>
                <div class="exercise-content">
                    <h2>Present Tense Verbs</h2>
                    <p>Practice conjugating regular and irregular French verbs in the present tense.</p>
                    <div class="exercise-meta">
                        <span class="difficulty beginner">Beginner</span>
                        <span class="exercise-time"><i class='bx bx-time'></i> 15 min</span>
                    </div>
                    <a href="exercise-content.php?type=grammar&level=1" class="btn btn-primary">Start Exercise</a>
                </div>
            </div>
            
            <div class="exercise-item" data-type="listening">
                <div class="exercise-icon">
                    <i class='bx bx-headphone'></i>
                </div>
                <div class="exercise-content">
                    <h2>Basic Conversations</h2>
                    <p>Listen to simple French conversations and answer questions about what you heard.</p>
                    <div class="exercise-meta">
                        <span class="difficulty beginner">Beginner</span>
                        <span class="exercise-time"><i class='bx bx-time'></i> 12 min</span>
                    </div>
                    <a href="exercise-content.php?type=listening&level=1" class="btn btn-primary">Start Exercise</a>
                </div>
            </div>
            
            <div class="exercise-item" data-type="reading">
                <div class="exercise-icon">
                    <i class='bx bx-book-reader'></i>
                </div>
                <div class="exercise-content">
                    <h2>Simple French Texts</h2>
                    <p>Read short texts in French and answer comprehension questions to improve your reading skills.</p>
                    <div class="exercise-meta">
                        <span class="difficulty beginner">Beginner</span>
                        <span class="exercise-time"><i class='bx bx-time'></i> 15 min</span>
                    </div>
                    <a href="exercise-content.php?type=reading&level=1" class="btn btn-primary">Start Exercise</a>
                </div>
            </div>
            
            <div class="exercise-item" data-type="vocabulary">
                <div class="exercise-icon">
                    <i class='bx bx-food-menu'></i>
                </div>
                <div class="exercise-content">
                    <h2>Food & Dining Vocabulary</h2>
                    <p>Learn and practice French words and phrases related to food, restaurants, and dining.</p>
                    <div class="exercise-meta">
                        <span class="difficulty intermediate">Intermediate</span>
                        <span class="exercise-time"><i class='bx bx-time'></i> 15 min</span>
                    </div>
                    <a href="exercise-content.php?type=vocabulary&level=2" class="btn btn-primary">Start Exercise</a>
                </div>
            </div>
            
            <div class="exercise-item" data-type="grammar">
                <div class="exercise-icon">
                    <i class='bx bx-edit-alt'></i>
                </div>
                <div class="exercise-content">
                    <h2>Past Tense (Passé Composé)</h2>
                    <p>Practice using the passé composé to talk about past events in French.</p>
                    <div class="exercise-meta">
                        <span class="difficulty intermediate">Intermediate</span>
                        <span class="exercise-time"><i class='bx bx-time'></i> 20 min</span>
                    </div>
                    <a href="exercise-content.php?type=grammar&level=2" class="btn btn-primary">Start Exercise</a>
                </div>
            </div>
        </div>
        
        <div class="navigation-buttons">
            <a href="french.php" class="btn btn-secondary">
                <i class='bx bx-arrow-back'></i> Back to French
            </a>
            <a href="courses.php" class="btn btn-primary">
                View Courses <i class='bx bx-right-arrow-alt'></i>
            </a>
        </div>
    </div>

    <script>
        const filterButtons = document.querySelectorAll('.filter-btn');
        const exerciseItems = document.querySelectorAll('.exercise-item');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                const filterValue = this.getAttribute('data-filter');
                
                exerciseItems.forEach(item => {
                    if (filterValue === 'all' || item.getAttribute('data-type') === filterValue) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
        
        document.querySelector('.user-info').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('active');
        });
    </script>
</body>
</html>
