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

$course_query = "SELECT c.* FROM courses c
                 JOIN languages l ON c.language_id = l.language_id
                 WHERE l.code = 'de' AND c.title = 'German Fundamentals'";
$course_result = $conn->query($course_query);

if ($course_result->num_rows == 0) {
    $course_id = 0;
} else {
    $course = $course_result->fetch_assoc();
    $course_id = $course['course_id'];
}

$units_query = "SELECT * FROM units 
               WHERE course_id = ? 
               ORDER BY order_index ASC";
$stmt = $conn->prepare($units_query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$units_result = $stmt->get_result();

// Modified progress query to ensure we're getting complete progress data
$progress_query = "SELECT l.unit_id, 
                   COUNT(l.lesson_id) AS total_lessons, 
                   SUM(CASE WHEN up.status = 'completed' THEN 1 ELSE 0 END) AS completed_lessons
                   FROM lessons l
                   LEFT JOIN user_progress up ON l.lesson_id = up.lesson_id AND up.user_id = ? 
                   WHERE l.is_active = 1
                   GROUP BY l.unit_id";
$stmt = $conn->prepare($progress_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$progress_result = $stmt->get_result();

$unit_progress = [];
while ($row = $progress_result->fetch_assoc()) {
    $unit_progress[$row['unit_id']] = [
        'total' => $row['total_lessons'],
        'completed' => $row['completed_lessons']
    ];
}

$highest_accessed_unit = 1;
foreach ($unit_progress as $unit_id => $progress) {
    if ($progress['completed'] > 0 && $unit_id > $highest_accessed_unit) {
        $highest_accessed_unit = $unit_id;
    }
}

// Modified: All units are unlocked now
$unlocked_units = array(); // We'll fill this with all unit IDs

// Get all unit IDs to unlock them
$all_units_query = "SELECT unit_id FROM units WHERE course_id = ?";
$stmt = $conn->prepare($all_units_query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$all_units_result = $stmt->get_result();
while($unit = $all_units_result->fetch_assoc()) {
    $unlocked_units[] = $unit['unit_id'];
}

if(isset($_GET['unit'])) {
    $unit_id = intval($_GET['unit']);
    
    $log_query = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
                 VALUES (?, 'course_access', ?)";
    $details = json_encode(['language' => 'german', 'unit' => $unit_id]);
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
    <link rel="stylesheet" href="../style.css">
    <title>Esperanto - German Courses</title>

</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Esperanto</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../dashboard.php">Dashboard</a></li>
                    <li><a href="../games/games.php">Games</a></li>
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
        <h1>German Courses <img src="https://flagcdn.com/w40/de.png" alt="German Flag" class="flag-icon"></h1>
        
        <div class="course-list">
            <?php 
            $unit_count = 1;
            $last_completed_unit = 0;

            foreach ($unit_progress as $unit_id => $progress) {
                if ($progress['completed'] > 0 && $progress['completed'] >= $progress['total']) {
                    if ($unit_id > $last_completed_unit) {
                        $last_completed_unit = $unit_id;
                    }
                }
            }
            
            if ($units_result->num_rows > 0) {
                while($unit = $units_result->fetch_assoc()) {
                    // All units are unlocked now
                    $is_locked = false;
                    
                    $lessons_query = "SELECT COUNT(*) as lesson_count FROM lessons WHERE unit_id = ?";
                    $stmt = $conn->prepare($lessons_query);
                    $stmt->bind_param("i", $unit['unit_id']);
                    $stmt->execute();
                    $lessons_result = $stmt->get_result();
                    $lessons_data = $lessons_result->fetch_assoc();
                    $lesson_count = $lessons_data['lesson_count'];
                    
                    $completed = isset($unit_progress[$unit['unit_id']]) ? $unit_progress[$unit['unit_id']]['completed'] : 0;
                    $total = isset($unit_progress[$unit['unit_id']]) ? $unit_progress[$unit['unit_id']]['total'] : $lesson_count;
                    
                    // Determine class based on completion level with new color scheme
                    if ($completed == 0) {
                        $progress_class = "beginner"; // Red for not started (0/5)
                    } else if ($completed < $total) {
                        $progress_class = "intermediate"; // Yellow for in progress (1/5 to 4/5)
                    } else {
                        $progress_class = "advanced"; // Green for completed (5/5)
                    }
            ?>
            <div class="course-item">
                <div class="course-header">
                    <h2><?php echo htmlspecialchars($unit['title']); ?></h2>
                    <span class="difficulty <?php echo $progress_class; ?>"><?php echo $completed . "/" . $total; ?></span>
                </div>
                <div class="course-content">
                    <p><?php echo htmlspecialchars($unit['description']); ?></p>
                    
                    <?php
                    $topics_query = "SELECT title FROM lessons WHERE unit_id = ? ORDER BY order_index ASC LIMIT 4";
                    $stmt = $conn->prepare($topics_query);
                    $stmt->bind_param("i", $unit['unit_id']);
                    $stmt->execute();
                    $topics_result = $stmt->get_result();
                    
                    // Define German to English translations for lesson titles
                    $translations = [
                        "Begrüßungen und Vorstellungen" => "Greetings and Introductions",
                        "Grundlegende Aussprache" => "Basic Pronunciation",
                        "Zahlen 1-20" => "Numbers 1-20",
                        "Einfache Fragen" => "Simple Questions",
                        "Häufige Ausdrücke" => "Common Phrases",
                        "Tägliche Routinen" => "Daily Routines",
                        "Verben im Präsens" => "Present Tense Verbs",
                        "Die Uhrzeit" => "Telling Time",
                        "Wochentage und Monate" => "Days and Months",
                        "Wetterausdrücke" => "Weather Expressions",
                        "Im Supermarkt" => "At the Supermarket",
                        "Im Restaurant" => "At the Restaurant",
                        "Kleidung Einkaufen" => "Shopping for Clothes",
                        "Geld und Zahlen" => "Money and Numbers",
                        "Etwas kaufen" => "Making Purchases",
                        "Verkehrsmittel" => "Transportation Vocabulary",
                        "Nach dem Weg fragen" => "Asking for Directions",
                        "Hotelreservierungen" => "Hotel Reservations",
                        "Sehenswürdigkeiten" => "Tourist Attractions",
                        "Reiseprobleme" => "Travel Problems"
                    ];
                    
                    if ($topics_result->num_rows > 0) {
                    ?>
                    <ul class="course-topics">
                        <?php while ($topic = $topics_result->fetch_assoc()) { 
                            // Display English translation if available, otherwise display original
                            $displayTitle = isset($translations[$topic['title']]) ? $translations[$topic['title']] : $topic['title'];
                        ?>
                        <li><i class='bx bx-check-circle'></i> <?php echo htmlspecialchars($displayTitle); ?></li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                    
                    <div class="course-actions">
                        <span class="lesson-count"><?php echo $lesson_count; ?> Lessons</span>
                        <a href="units-content.php?unit=<?php echo $unit['unit_id']; ?>" class="btn btn-primary">Start Unit</a>
                    </div>
                </div>
            </div>
            <?php 
                $unit_count++;
                }
            } else {
                echo "<p>No courses available at this time. Please check back later.</p>";
            }
            ?>
        </div>
        
        <div class="navigation-buttons">
            <a href="german.php" class="btn btn-secondary">
                <i class='bx bx-arrow-back'></i> Back to German
            </a>

            <a href="exercises.php" class="btn btn-primary">
                Practice Exercises <i class='bx bx-right-arrow-alt'></i>
            </a>
        </div>
    </div>
    
    <script>
        document.querySelectorAll('.course-header').forEach(header => {
            header.addEventListener('click', function() {
                const courseItem = this.parentElement;
                courseItem.classList.toggle('expanded');
            });
        });
        document.querySelector('.user-info').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('active');
        });
    </script>
</body>
</html>
