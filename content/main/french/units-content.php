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

if (!isset($_GET['unit']) || !is_numeric($_GET['unit'])) {
    header("Location: units.php");
    exit();
}

$unit_id = intval($_GET['unit']);

$unit_query = "SELECT u.*, c.title as course_title 
              FROM units u 
              JOIN courses c ON u.course_id = c.course_id 
              WHERE u.unit_id = ?";
$stmt = $conn->prepare($unit_query);
$stmt->bind_param("i", $unit_id);
$stmt->execute();
$unit_result = $stmt->get_result();

if ($unit_result->num_rows == 0) {
    header("Location: units.php");
    exit();
}

$unit = $unit_result->fetch_assoc();

$lessons_query = "SELECT l.*, 
                 CASE 
                    WHEN l.title LIKE '%(%' THEN l.title 
                    WHEN l.title = 'Greetings and Introductions' THEN 'Les Salutations et Présentations (Greetings and Introductions)'
                    WHEN l.title = 'Basic Pronunciation' THEN 'Prononciation de Base (Basic Pronunciation)'
                    WHEN l.title = 'Numbers 1-20' THEN 'Les Nombres 1-20 (Numbers 1-20)'
                    WHEN l.title = 'Simple Questions' THEN 'Questions Simples (Simple Questions)'
                    WHEN l.title = 'Common Phrases' THEN 'Phrases Courantes (Common Phrases)'
                    WHEN l.title = 'Daily Routines' THEN 'Routines Quotidiennes (Daily Routines)'
                    WHEN l.title = 'Present Tense Verbs' THEN 'Verbes au Présent (Present Tense Verbs)'
                    WHEN l.title = 'Telling Time' THEN 'Dire l\'Heure (Telling Time)'
                    WHEN l.title = 'Days and Months' THEN 'Jours et Mois (Days and Months)'
                    WHEN l.title = 'Weather Expressions' THEN 'Expressions de la Météo (Weather Expressions)'
                    ELSE CONCAT(l.title, ' (', l.title, ')')
                 END AS display_title
                 FROM lessons l
                 WHERE l.unit_id = ? 
                 ORDER BY l.order_index ASC";
$stmt = $conn->prepare($lessons_query);
$stmt->bind_param("i", $unit_id);
$stmt->execute();
$lessons_result = $stmt->get_result();

$progress_query = "SELECT lesson_id, status, score 
                  FROM user_progress 
                  WHERE user_id = ? AND lesson_id IN 
                  (SELECT lesson_id FROM lessons WHERE unit_id = ?)";
$stmt = $conn->prepare($progress_query);
$stmt->bind_param("ii", $user_id, $unit_id);
$stmt->execute();
$progress_result = $stmt->get_result();

$user_progress = [];
while ($progress = $progress_result->fetch_assoc()) {
    $user_progress[$progress['lesson_id']] = [
        'status' => $progress['status'],
        'score' => $progress['score']
    ];
}

// Get the last completed lesson to determine what should be unlocked
$last_completed_lesson_query = "SELECT MAX(l.order_index) as last_completed_index
                              FROM user_progress up
                              JOIN lessons l ON up.lesson_id = l.lesson_id
                              WHERE up.user_id = ? AND l.unit_id = ? AND up.status = 'completed'";
$stmt = $conn->prepare($last_completed_lesson_query);
$stmt->bind_param("ii", $user_id, $unit_id);
$stmt->execute();
$last_completed_result = $stmt->get_result();
$last_completed_data = $last_completed_result->fetch_assoc();
$last_completed_index = $last_completed_data['last_completed_index'] ?? 0;

$log_query = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
             VALUES (?, 'unit_access', ?)";
$details = json_encode(['unit_id' => $unit_id, 'unit_title' => $unit['title']]);
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
    <title>Esperanto - <?php echo htmlspecialchars($unit['title']); ?></title>
    <style>
        .progress-bar {
            width: 100%;
            height: 8px;
            background-color: #f0f0f0;
            border-radius: 4px;
            margin-bottom: 30px;
        }
        
        .progress-fill {
            height: 100%;
            background-color: #7F57F1;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .lesson-list {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .lesson-item {
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(127, 87, 241, 0.1);
        }
        
        .lesson-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: white;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .lesson-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .lesson-title h2 {
            font-size: 18px;
            margin: 0;
            color: #333;
        }
        
        .lesson-number {
            background-color: #7F57F1;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
        
        .lesson-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        
        .status-not-started {
            color: #888;
        }
        
        .status-completed {
            color: #2e7d32;
        }
        
        .status-in-progress {
            color: #ff8f00;
        }
        
        .lesson-item.completed .lesson-number {
            background-color: #2e7d32;
        }
        
        .lesson-item.in-progress .lesson-number {
            background-color: #ff8f00;
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
        <h1><?php echo htmlspecialchars($unit['title']); ?> <img src="https://flagcdn.com/w40/fr.png" alt="French Flag" class="flag-icon"></h1>
        
        <p class="unit-description"><?php echo htmlspecialchars($unit['description']); ?></p>
        
        <?php
        $total_lessons = $lessons_result->num_rows;
        $completed_lessons = 0;
        
        foreach ($user_progress as $progress) {
            if ($progress['status'] == 'completed') {
                $completed_lessons++;
            }
        }
        
        $completion_percentage = $total_lessons > 0 ? ($completed_lessons / $total_lessons) * 100 : 0;
        ?>
        
        <div class="completion-status">
            <div class="progress-text">
                <span>Unit Progress: <?php echo $completed_lessons; ?> / <?php echo $total_lessons; ?> lessons completed</span>
                <span><?php echo round($completion_percentage); ?>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $completion_percentage; ?>%"></div>
            </div>
        </div>
        
        <div class="lesson-list">
            <?php
            $lesson_number = 1;
            $previous_completed = true; // First lesson is always unlocked
            
            if ($lessons_result->num_rows > 0) {
                
                while($lesson = $lessons_result->fetch_assoc()) {
                    $lesson_id = $lesson['lesson_id'];
                    $lesson_status = isset($user_progress[$lesson_id]) ? $user_progress[$lesson_id]['status'] : 'not_started';
                    
                    // A lesson is locked if it's not the first one and the previous lesson is not completed
                    // OR if its order index is more than 1 position ahead of the last completed lesson
                    $is_locked = ($lesson_number > 1 && $lesson['order_index'] > $last_completed_index + 1);
                    
                    // Update the previous_completed status for next iteration
                    $previous_completed = ($lesson_status == 'completed');
            ?>
            <div class="lesson-item <?php echo $lesson_status; ?><?php echo $is_locked ? ' locked' : ''; ?>">
                <div class="lesson-header">
                    <div class="lesson-title">
                        <span class="lesson-number"><?php echo $lesson_number; ?></span>
                        <h2><?php echo htmlspecialchars($lesson['display_title']); ?></h2>
                    </div>
                    <div class="lesson-status">
                        <?php if ($is_locked) { ?>
                            <i class='bx bx-lock-alt'></i> <span>Complete previous lesson to unlock</span>
                        <?php } else if ($lesson_status == 'completed') { ?>
                            <i class='bx bx-check-circle status-completed'></i> <span class="status-completed">Completed</span>
                        <?php } else if ($lesson_status == 'in_progress') { ?>
                            <i class='bx bx-loader status-in-progress'></i> <span class="status-in-progress">In Progress</span>
                        <?php } else { ?>
                            <i class='bx bx-circle status-not-started'></i> <span class="status-not-started">Not Started</span>
                        <?php } ?>
                        
                        <?php if (!$is_locked) { ?>
                            <a href="lesson.php?id=<?php echo $lesson_id; ?>" class="btn btn-primary">
                                <?php echo ($lesson_status == 'completed' ? 'Review' : 'Start'); ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php
                    $lesson_number++;
                }
            } else {
                echo "<p>No lessons available for this unit yet. Please check back later.</p>";
            }
            ?>
        </div>
        
        <div class="navigation-buttons">
            <a href="units.php" class="btn btn-secondary">
                <i class='bx bx-arrow-back'></i> Back to Courses
            </a>
            <?php if ($completion_percentage == 100) { ?>
            <a href="unit<?php echo $unit_id; ?>-test.php?unit=<?php echo $unit_id; ?>" class="btn btn-primary">
                Take Unit Test <i class='bx bx-right-arrow-alt'></i>
            </a>
            <?php } ?>
        </div>
    </div>
    
    <script>
        document.querySelector('.user-info').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('active');
        });
    </script>
</body>
</html>
