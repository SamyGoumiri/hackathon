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

// Fix the course query - update course title from "Spanish Fundamentals" to "Spanish for Beginners"
$course_query = "SELECT c.* FROM courses c
                 JOIN languages l ON c.language_id = l.language_id
                 WHERE l.code = 'es' AND c.title = 'Spanish for Beginners'";
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

// Improved unit progress tracking
$progress_query = "SELECT l.unit_id, COUNT(l.lesson_id) AS total_lessons, 
                   COUNT(up.lesson_id) AS completed_lessons
                   FROM lessons l
                   LEFT JOIN user_progress up ON l.lesson_id = up.lesson_id 
                   AND up.user_id = ? AND up.status = 'completed'
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
    $details = json_encode(['language' => 'spanish', 'unit' => $unit_id]);
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
    <title>Esperanto - Spanish Courses</title>
    <style>
        .course-content {
            display: none;
            padding: 0 20px 20px;
        }
        
        .course-item.expanded .course-content {
            display: block;
        }
        
        .course-header {
            cursor: pointer;
        }
    </style>
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
        <h1>Spanish Courses <img src="https://flagcdn.com/w40/es.png" alt="Spanish Flag" class="flag-icon"></h1>
        
        <?php if (isset($_GET['error']) && $_GET['error'] == 'locked') { ?>
        <div class="alert alert-warning">
            <i class='bx bx-lock-alt'></i> You need to complete previous units before accessing this one.
        </div>
        <?php } ?>
        
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
                    
                    if ($unit_count <= 2) {
                        $difficulty = "beginner";
                        $difficulty_text = "Beginner";
                    } else {
                        $difficulty = "intermediate";
                        $difficulty_text = "Intermediate";
                    }
            ?>
            <div class="course-item">
                <div class="course-header">
                    <h2><?php echo htmlspecialchars($unit['title']); ?></h2>
                    <span class="difficulty <?php echo $difficulty; ?>"><?php echo $difficulty_text; ?></span>
                </div>
                <div class="course-content">
                    <p><?php echo htmlspecialchars($unit['description']); ?></p>
                    
                    <?php
                    $topics_query = "SELECT title FROM lessons WHERE unit_id = ? ORDER BY order_index ASC LIMIT 4";
                    $stmt = $conn->prepare($topics_query);
                    $stmt->bind_param("i", $unit['unit_id']);
                    $stmt->execute();
                    $topics_result = $stmt->get_result();
                    
                    if ($topics_result->num_rows > 0) {
                    ?>
                    <ul class="course-topics">
                        <?php while ($topic = $topics_result->fetch_assoc()) { ?>
                        <li><i class='bx bx-check-circle'></i> <?php echo htmlspecialchars($topic['title']); ?></li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                    
                    <div class="course-actions">
                        <span class="lesson-count"><?php echo $lesson_count; ?> Lessons</span>
                        <!-- All units now have the Start Unit button -->
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
            <a href="spanish.php" class="btn btn-secondary">
                <i class='bx bx-arrow-back'></i> Back to Spanish
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
