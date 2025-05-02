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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: units.php");
    exit();
}

$lesson_id = intval($_GET['id']);

$lesson_query = "SELECT l.*, u.title as unit_title, u.unit_id 
                FROM lessons l
                JOIN units u ON l.unit_id = u.unit_id
                WHERE l.lesson_id = ?";
$stmt = $conn->prepare($lesson_query);
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$lesson_result = $stmt->get_result();

if ($lesson_result->num_rows == 0) {
    header("Location: units.php");
    exit();
}

$lesson = $lesson_result->fetch_assoc();
$unit_id = $lesson['unit_id'];
$current_order_index = $lesson['order_index'];

$previous_lessons_query = "SELECT l.lesson_id, l.title, l.order_index 
                          FROM lessons l 
                          WHERE l.unit_id = ? AND l.order_index < ? 
                          ORDER BY l.order_index ASC";
$stmt = $conn->prepare($previous_lessons_query);
$stmt->bind_param("ii", $unit_id, $current_order_index);
$stmt->execute();
$previous_lessons_result = $stmt->get_result();

if ($previous_lessons_result->num_rows > 0) {
    while ($prev_lesson = $previous_lessons_result->fetch_assoc()) {
        $prev_lesson_id = $prev_lesson['lesson_id'];
        
        $check_completion_query = "SELECT status FROM user_progress 
                                  WHERE user_id = ? AND lesson_id = ?";
        $stmt = $conn->prepare($check_completion_query);
        $stmt->bind_param("ii", $user_id, $prev_lesson_id);
        $stmt->execute();
        $progress_check = $stmt->get_result();
        
        if ($progress_check->num_rows == 0 || $progress_check->fetch_assoc()['status'] != 'completed') {
            $_SESSION['error_message'] = "You need to complete the previous lessons first.";
            header("Location: lesson.php?id=" . $prev_lesson_id);
            exit();
        }
    }
}

$progress_query = "SELECT * FROM user_progress WHERE user_id = ? AND lesson_id = ?";
$stmt = $conn->prepare($progress_query);
$stmt->bind_param("ii", $user_id, $lesson_id);
$stmt->execute();
$progress_result = $stmt->get_result();

if ($progress_result->num_rows == 0) {
    $create_progress = "INSERT INTO user_progress (user_id, lesson_id, status) VALUES (?, ?, 'in_progress')";
    $stmt = $conn->prepare($create_progress);
    $stmt->bind_param("ii", $user_id, $lesson_id);
    $stmt->execute();
    
    $lesson_status = 'in_progress';
} else {
    $progress = $progress_result->fetch_assoc();
    $lesson_status = $progress['status'];
    
    if ($lesson_status != 'completed') {
        $update_status = "UPDATE user_progress SET status = 'in_progress', last_activity = NOW() 
                        WHERE user_id = ? AND lesson_id = ?";
        $stmt = $conn->prepare($update_status);
        $stmt->bind_param("ii", $user_id, $lesson_id);
        $stmt->execute();
    }
}

$log_query = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
             VALUES (?, 'lesson_access', ?)";
$details = json_encode([
    'lesson_id' => $lesson_id, 
    'lesson_title' => $lesson['title'], 
    'unit_id' => $unit_id
]);
$stmt = $conn->prepare($log_query);
$stmt->bind_param("is", $user_id, $details);
$stmt->execute();

if (isset($_POST['complete_lesson'])) {
    $update_status = "UPDATE user_progress SET status = 'completed', completion_date = NOW(), 
                    last_activity = NOW() WHERE user_id = ? AND lesson_id = ?";
    $stmt = $conn->prepare($update_status);
    $stmt->bind_param("ii", $user_id, $lesson_id);
    $stmt->execute();
    
    header("Location: lesson.php?id=" . $lesson_id . "&completed=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="french.css">
    <title>Lango - <?php echo htmlspecialchars($lesson['title']); ?></title>
    <style>
        .lesson-container {
            background-color: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(127, 87, 241, 0.1);
            margin-bottom: 30px;
        }
        
        .lesson-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .lesson-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .est-time {
            color: #777;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .lesson-content {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
        }
        
        .lesson-content h2 {
            color: #7F57F1;
            margin: 25px 0 15px;
            font-size: 24px;
        }
        
        .lesson-content h3 {
            color: #555;
            margin: 20px 0 10px;
            font-size: 20px;
        }
        
        .lesson-content p {
            margin-bottom: 15px;
        }
        
        .lesson-content ul, .lesson-content ol {
            margin-left: 20px;
            margin-bottom: 20px;
        }
        
        .lesson-content li {
            margin-bottom: 8px;
        }
        
        .vocabulary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .vocabulary-table th, .vocabulary-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .vocabulary-table th {
            text-align: left;
            color: #7F57F1;
            font-weight: 600;
        }
        
        .vocabulary-table tr:last-child td {
            border-bottom: none;
        }
        
        .french-word {
            color: #7F57F1;
            font-weight: 600;
        }
        
        .pronunciation {
            color: #777;
            font-style: italic;
        }
        
        .example {
            padding: 15px;
            background-color: #f9f4ff;
            border-left: 4px solid #7F57F1;
            margin: 20px 0;
            border-radius: 0 10px 10px 0;
        }
        
        .example-title {
            font-weight: 600;
            color: #7F57F1;
            margin-bottom: 10px;
        }
        
        .completion-form {
            margin-top: 30px;
            text-align: center;
        }
        
        .completion-notice {
            background-color: #e6f7e6;
            color: #2e7d32;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .completion-notice i {
            margin-right: 8px;
            font-size: 20px;
        }
    </style>
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
        <div class="breadcrumb">
            <a href="units.php">Courses</a> &gt; 
            <a href="course-content.php?unit=<?php echo $unit_id; ?>"><?php echo htmlspecialchars($lesson['unit_title']); ?></a> &gt; 
            <span><?php echo htmlspecialchars($lesson['title']); ?></span>
        </div>
        
        <?php if (isset($_GET['completed']) && $_GET['completed'] == 1) { ?>
        <div class="completion-notice">
            <i class='bx bx-check-circle'></i> Lesson marked as completed!
        </div>
        <?php } ?>
        
        <div class="lesson-container">
            <div class="lesson-header">
                <h1><?php echo htmlspecialchars($lesson['title']); ?></h1>
            </div>
            
            <div class="lesson-content">
                <?php
                $unit_folder = "unit" . intval($unit_id);
                $lesson_file = "lesson" . intval($lesson['order_index']) . ".php";
                $lesson_path = __DIR__ . "/units/" . $unit_folder . "/" . $lesson_file;
                
                if ($unit_id == 2) {
                    $lesson_file = "lesson" . (intval($lesson['order_index']) + 5) . ".php";
                    $lesson_path = __DIR__ . "/units/" . $unit_folder . "/" . $lesson_file;
                }
                
                if ($unit_id == 3) {
                    $lesson_file = "lesson" . (intval($lesson['order_index']) + 10) . ".php";
                    $lesson_path = __DIR__ . "/units/" . $unit_folder . "/" . $lesson_file;
                }
                
                if ($unit_id == 4) {
                    $lesson_file = "lesson" . (intval($lesson['order_index']) + 15) . ".php";
                    $lesson_path = __DIR__ . "/units/" . $unit_folder . "/" . $lesson_file;
                }
                
                if (file_exists($lesson_path)) {
                    include($lesson_path);
                } else {
                    echo "<p>This lesson will help you learn important French vocabulary and grammar concepts.</p>";
                    echo "<p>The full lesson content will be available soon. Please check back later.</p>";
                    error_log("Missing lesson file: " . $lesson_path);
                }
                ?>
            </div>
            
            <?php if ($lesson_status != 'completed') { ?>
            <form method="post" class="completion-form">
                <input type="hidden" name="complete_lesson" value="1">
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-check-circle'></i> Mark Lesson as Complete
                </button>
            </form>
            <?php } ?>
        </div>
        
        <div class="navigation-buttons">
            <a href="course-content.php?unit=<?php echo $unit_id; ?>" class="btn btn-secondary">
                <i class='bx bx-arrow-back'></i> Back to Unit
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
