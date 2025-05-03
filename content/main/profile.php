<?php
session_start();
require_once '../../database/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$user_query = "SELECT username, first_name, last_name, email, registration_date FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Calculate user level based on completed lessons
$level_query = "SELECT COUNT(*) as completed_lessons FROM user_progress WHERE user_id = ? AND status = 'completed'";
$stmt = $conn->prepare($level_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$level_result = $stmt->get_result();
$completed_lessons = $level_result->fetch_assoc()['completed_lessons'];
$user_level = floor($completed_lessons / 10) + 1; // Every 10 lessons = 1 level

// Get learning languages with progress
$languages_query = "SELECT l.language_id, l.name, l.code, ul.proficiency_level, c.course_id
                   FROM user_languages ul 
                   JOIN languages l ON ul.language_id = l.language_id
                   LEFT JOIN courses c ON l.language_id = c.language_id AND c.is_active = 1
                   WHERE ul.user_id = ? AND ul.is_learning = 1";
$stmt = $conn->prepare($languages_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$learning_result = $stmt->get_result();
$learning_languages = [];
while ($lang = $learning_result->fetch_assoc()) {
    $lang['progress'] = getUserLanguageProgress($conn, $user_id, $lang['course_id']);
    $learning_languages[] = $lang;
}

// Get user streak (consecutive days with activity)
$streak_query = "SELECT MAX(consecutive_days) as streak FROM (
                SELECT 
                    COUNT(*) as consecutive_days
                FROM (
                    SELECT 
                        DATE(timestamp) as activity_date,
                        DATE_SUB(DATE(timestamp), INTERVAL ROW_NUMBER() OVER (ORDER BY DATE(timestamp)) DAY) as grp
                    FROM user_activity 
                    WHERE user_id = ?
                    GROUP BY activity_date
                ) as t
                GROUP BY grp
            ) as streak_calc";
$stmt = $conn->prepare($streak_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$streak_result = $stmt->get_result();
$streak = $streak_result->fetch_assoc()['streak'] ?? 0;

// Get total XP
$total_xp = $completed_lessons * 10; // 10 XP per completed lesson

// Get achievements from database
$achievements_query = "SELECT a.achievement_id, a.title, a.description, a.icon, a.criteria, a.criteria_value, 
                      CASE WHEN ua.user_achievement_id IS NOT NULL THEN 1 ELSE 0 END AS unlocked
                      FROM achievements a
                      LEFT JOIN user_achievements ua ON a.achievement_id = ua.achievement_id AND ua.user_id = ?
                      WHERE a.is_active = 1
                      ORDER BY a.criteria_value";
$stmt = $conn->prepare($achievements_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$achievements_result = $stmt->get_result();
$achievements = [];
while ($achievement = $achievements_result->fetch_assoc()) {
    // Check if achievement should be unlocked based on current stats
    if ($achievement['unlocked'] == 0) {
        switch ($achievement['criteria']) {
            case 'lessons':
                $achievement['unlocked'] = ($completed_lessons >= $achievement['criteria_value']) ? 1 : 0;
                break;
            case 'streak':
                $achievement['unlocked'] = ($streak >= $achievement['criteria_value']) ? 1 : 0;
                break;
            case 'languages':
                $achievement['unlocked'] = (count($learning_languages) >= $achievement['criteria_value']) ? 1 : 0;
                break;
            case 'xp':
                $achievement['unlocked'] = ($total_xp >= $achievement['criteria_value']) ? 1 : 0;
                break;
        }
        
        // If achievement has been unlocked during this session, add it to the database
        if ($achievement['unlocked'] == 1) {
            $unlock_query = "INSERT IGNORE INTO user_achievements (user_id, achievement_id) VALUES (?, ?)";
            $unlock_stmt = $conn->prepare($unlock_query);
            $unlock_stmt->bind_param("ii", $user_id, $achievement['achievement_id']);
            $unlock_stmt->execute();
        }
    }
    
    $achievements[] = $achievement;
}

// Registration date in friendly format
$join_date = date('F d, Y', strtotime($user['registration_date']));

// Function to calculate progress
function getUserLanguageProgress($conn, $user_id, $course_id) {
    if (!$course_id) {
        return ['completed_lessons' => 0, 'total_lessons' => 0, 'percentage' => 0];
    }
    
    $lessons_query = "SELECT COUNT(*) as total FROM lessons 
                     JOIN units ON lessons.unit_id = units.unit_id 
                     WHERE units.course_id = ? AND lessons.is_active = 1";
    $stmt = $conn->prepare($lessons_query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $total_result = $stmt->get_result();
    $total_lessons = $total_result->fetch_assoc()['total'];
    
    $completed_query = "SELECT COUNT(*) as completed FROM user_progress up
                        JOIN lessons l ON up.lesson_id = l.lesson_id
                        JOIN units u ON l.unit_id = u.unit_id
                        WHERE up.user_id = ? AND u.course_id = ? AND up.status = 'completed'";
    $stmt = $conn->prepare($completed_query);
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    $completed_result = $stmt->get_result();
    $completed_lessons = $completed_result->fetch_assoc()['completed'];
    
    $percentage = ($total_lessons > 0) ? round(($completed_lessons / $total_lessons) * 100) : 0;
    
    return [
        'completed_lessons' => $completed_lessons,
        'total_lessons' => $total_lessons,
        'percentage' => $percentage
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="profile.css">
    <title>Esperanto - User Profile</title>
</head>

<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Esperanto</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="games/games.php">Games</a></li>
                    <li><a href="chatbot/chatbot.php">ChatBot</a></li>
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
                    <a href="profile.php" class="active"><i class='bx bx-user'></i> Profile</a>
                    <a href="settings.php"><i class='bx bx-cog'></i> Settings</a>
                    <a href="../auth/logout.php"><i class='bx bx-log-out'></i> Log Out</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <span class="user-initials">
                        <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                    </span>
                </div>
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars(ucfirst($user['first_name']) . ' ' . ucfirst($user['last_name'])); ?></h1>
                    <p class="username">@<?php echo htmlspecialchars($user['username']); ?></p>
                    <div class="level-badge">
                        <span class="level-text">Level <?php echo $user_level; ?></span>
                    </div>
                    <p class="join-date">Joined on <?php echo $join_date; ?></p>
                </div>
            </div>

            <div class="profile-stats">
                <h2>Your Language Learning Stats</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class='bx bx-trophy'></i>
                        <div class="stat-value"><?php echo $total_xp; ?></div>
                        <div class="stat-label">Total XP</div>
                    </div>
                    <div class="stat-card">
                        <i class='bx bx-book-open'></i>
                        <div class="stat-value"><?php echo $completed_lessons; ?></div>
                        <div class="stat-label">Lessons Completed</div>
                    </div>
                    <div class="stat-card">
                        <i class='bx bx-calendar-check'></i>
                        <div class="stat-value"><?php echo $streak; ?> days</div>
                        <div class="stat-label">Current Streak</div>
                    </div>
                    <div class="stat-card">
                        <i class='bx bx-globe'></i>
                        <div class="stat-value"><?php echo count($learning_languages); ?></div>
                        <div class="stat-label">Languages</div>
                    </div>
                </div>
            </div>

            <div class="language-progress">
                <h2>Language Progress</h2>
                <?php if (empty($learning_languages)): ?>
                    <div class="empty-state">
                        <i class='bx bx-book-reader'></i>
                        <p>You haven't started learning any languages yet.</p>
                        <a href="dashboard.php" class="btn btn-primary">Choose a Language</a>
                    </div>
                <?php else: ?>
                    <div class="language-progress-list">
                        <?php foreach ($learning_languages as $language): ?>
                            <div class="language-progress-item">
                                <div class="language-info">
                                    <div class="language-flag">
                                        <img src="https://flagcdn.com/w80/<?php echo strtolower(substr($language['code'], 0, 2)); ?>.png" alt="<?php echo $language['name']; ?> Flag">
                                    </div>
                                    <div class="language-details">
                                        <h3><?php echo $language['name']; ?></h3>
                                        <span class="proficiency"><?php echo ucfirst($language['proficiency_level']); ?></span>
                                    </div>
                                </div>
                                <div class="language-stats">
                                    <div class="progress-info">
                                        <div class="level">
                                            <?php echo $language['progress']['completed_lessons']; ?>/<?php echo $language['progress']['total_lessons']; ?> Lessons
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress" style="width: <?php echo $language['progress']['percentage']; ?>%"></div>
                                        </div>
                                    </div>
                                    <a href="learn.php?lang=<?php echo $language['code']; ?>" class="btn btn-sm">Continue</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="achievements">
                <h2>Achievements</h2>
                <div class="achievements-grid">
                    <?php foreach ($achievements as $achievement): ?>
                        <div class="achievement-card <?php echo $achievement['unlocked'] ? 'unlocked' : 'locked'; ?>">
                            <div class="achievement-icon">
                                <i class='<?php echo $achievement['icon']; ?>'></i>
                            </div>
                            <h3><?php echo $achievement['title']; ?></h3>
                            <p><?php echo $achievement['description']; ?></p>
                            <?php if (!$achievement['unlocked']): ?>
                                <div class="locked-overlay">
                                    <i class='bx bx-lock'></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <p>&copy; <?php echo date('Y'); ?> Esperanto. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.querySelector('.user-info').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('active');
        });
    </script>
</body>
</html>
