<?php
session_start();
require_once '../../database/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = "SELECT username, first_name, last_name FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$languages_query = "SELECT l.language_id, l.name, l.code, ul.proficiency_level 
                   FROM user_languages ul 
                   JOIN languages l ON ul.language_id = l.language_id 
                   WHERE ul.user_id = ? AND ul.is_learning = 1";
$stmt = $conn->prepare($languages_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$learning_result = $stmt->get_result();
$learning_languages = [];
while ($lang = $learning_result->fetch_assoc()) {
    $learning_languages[$lang['code']] = $lang;
    
    $units_query = "SELECT u.unit_id 
                   FROM units u 
                   JOIN courses c ON u.course_id = c.course_id 
                   WHERE c.language_id = ?";
    $stmt_units = $conn->prepare($units_query);
    $stmt_units->bind_param("i", $lang['language_id']);
    $stmt_units->execute();
    $units_result = $stmt_units->get_result();
    $total_units = $units_result->num_rows;
    $learning_languages[$lang['code']]['total_units'] = $total_units;
    
    $completed_units = 0;
    while ($unit = $units_result->fetch_assoc()) {
        $unit_id = $unit['unit_id'];
        
        $lessons_query = "SELECT lesson_id FROM lessons WHERE unit_id = ?";
        $stmt_lessons = $conn->prepare($lessons_query);
        $stmt_lessons->bind_param("i", $unit_id);
        $stmt_lessons->execute();
        $lessons_result = $stmt_lessons->get_result();
        $total_lessons = $lessons_result->num_rows;
        
        if ($total_lessons > 0) {
            $completed_lessons_query = "SELECT COUNT(*) as completed_count
                                      FROM user_progress
                                      WHERE user_id = ? 
                                      AND lesson_id IN (SELECT lesson_id FROM lessons WHERE unit_id = ?)
                                      AND status = 'completed'";
            $stmt_completed = $conn->prepare($completed_lessons_query);
            $stmt_completed->bind_param("ii", $user_id, $unit_id);
            $stmt_completed->execute();
            $completed_result = $stmt_completed->get_result();
            $completed_data = $completed_result->fetch_assoc();
            
            if ($completed_data['completed_count'] == $total_lessons) {
                $completed_units++;
            }
        }
    }
    
    $learning_languages[$lang['code']]['completed_units'] = $completed_units;
    $learning_languages[$lang['code']]['progress_percentage'] = ($total_units > 0) ? 
        ($completed_units / $total_units) * 100 : 0;
}

// Calculate Daily Streak
function calculateStreak($conn, $user_id) {
    $today = date('Y-m-d');
    $streak = 0;
    $day = $today;
    
    // Check if user was active today
    $today_query = "SELECT COUNT(*) as active FROM user_activity 
                   WHERE user_id = ? AND DATE(timestamp) = ?";
    $stmt = $conn->prepare($today_query);
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $today_active = $result->fetch_assoc()['active'] > 0;
    
    if (!$today_active) {
        // If not active today, check yesterday to see if streak is broken
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $yesterday_query = "SELECT COUNT(*) as active FROM user_activity 
                           WHERE user_id = ? AND DATE(timestamp) = ?";
        $stmt = $conn->prepare($yesterday_query);
        $stmt->bind_param("is", $user_id, $yesterday);
        $stmt->execute();
        $result = $stmt->get_result();
        $yesterday_active = $result->fetch_assoc()['active'] > 0;
        
        if (!$yesterday_active) {
            return 0; // Streak broken
        }
        
        $day = $yesterday; // Start counting from yesterday
        $streak = 1;
    } else {
        $streak = 1; // Active today, start with 1
    }
    
    // Count back days with activity
    while (true) {
        $previous_day = date('Y-m-d', strtotime("$day -1 day"));
        $query = "SELECT COUNT(*) as active FROM user_activity 
                 WHERE user_id = ? AND DATE(timestamp) = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $user_id, $previous_day);
        $stmt->execute();
        $result = $stmt->get_result();
        $was_active = $result->fetch_assoc()['active'] > 0;
        
        if (!$was_active) {
            break;
        }
        
        $streak++;
        $day = $previous_day;
    }
    
    return $streak;
}

// Calculate Minutes Learned
function calculateMinutesLearned($conn, $user_id) {
    $query = "SELECT SUM(l.estimated_time) as total_minutes
              FROM user_progress up
              JOIN lessons l ON up.lesson_id = l.lesson_id
              WHERE up.user_id = ? AND up.status = 'completed'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    return $data['total_minutes'] ?? 0;
}

// Calculate XP Points (10 XP per completed lesson)
function calculateXP($conn, $user_id) {
    $query = "SELECT COUNT(*) as completed_lessons
              FROM user_progress
              WHERE user_id = ? AND status = 'completed'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    return $data['completed_lessons'] * 10;
}

// Calculate Achievements (for now, count completed units)
function calculateAchievements($conn, $user_id) {
    $achievements = 0;
    
    // Get all units
    $units_query = "SELECT u.unit_id, u.course_id
                   FROM units u
                   JOIN courses c ON u.course_id = c.course_id";
    $stmt_units = $conn->prepare($units_query);
    $stmt_units->execute();
    $units_result = $stmt_units->get_result();
    
    while ($unit = $units_result->fetch_assoc()) {
        $unit_id = $unit['unit_id'];
        
        // Count total lessons in this unit
        $lessons_query = "SELECT COUNT(*) as total_lessons
                         FROM lessons WHERE unit_id = ?";
        $stmt_lessons = $conn->prepare($lessons_query);
        $stmt_lessons->bind_param("i", $unit_id);
        $stmt_lessons->execute();
        $lessons_result = $stmt_lessons->get_result();
        $total_lessons = $lessons_result->fetch_assoc()['total_lessons'];
        
        if ($total_lessons > 0) {
            // Count completed lessons in this unit
            $completed_lessons_query = "SELECT COUNT(*) as completed_count
                                      FROM user_progress
                                      WHERE user_id = ? 
                                      AND lesson_id IN (SELECT lesson_id FROM lessons WHERE unit_id = ?)
                                      AND status = 'completed'";
            $stmt_completed = $conn->prepare($completed_lessons_query);
            $stmt_completed->bind_param("ii", $user_id, $unit_id);
            $stmt_completed->execute();
            $completed_result = $stmt_completed->get_result();
            $completed_data = $completed_result->fetch_assoc();
            
            if ($completed_data['completed_count'] == $total_lessons) {
                $achievements++;
            }
        }
    }
    
    return $achievements;
}

// Get user stats
$daily_streak = calculateStreak($conn, $user_id);
$minutes_learned = calculateMinutesLearned($conn, $user_id);
$xp_points = calculateXP($conn, $user_id);
$achievements = calculateAchievements($conn, $user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="dashboard.css">
    <title>Esperanto - Dashboard</title>
</head>

<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Esperanto</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="achievements.php">Achievements</a></li>
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
                    <a href="profile.php"><i class='bx bx-user'></i> Profile</a>
                    <a href="settings.php"><i class='bx bx-cog'></i> Settings</a>
                    <a href="../auth/logout.php"><i class='bx bx-log-out'></i> Log Out</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="dashboard-container">
            <section class="welcome-section">
                <div class="welcome-card">
                    <h2>Hi <?php echo htmlspecialchars(ucfirst($user['first_name'])); ?>!</h2>
                    <p>Continue your language journey by selecting a language below.</p>
                </div>
            </section>

            <section class="languages-section">
                <h2>Choose a Language to Learn</h2>
                
                <div class="language-cards">
                    <div class="language-card <?php echo isset($learning_languages['fr']) ? 'learning' : ''; ?>">
                        <div class="flag">
                            <img src="https://flagcdn.com/w320/fr.png" alt="French Flag">
                        </div>
                        <h3>French</h3>
                        <?php if (isset($learning_languages['fr'])): ?>
                            <div class="progress-info">
                                <div class="level">
                                    <?php 
                                    $completed = $learning_languages['fr']['completed_units'];
                                    $total = $learning_languages['fr']['total_units'];
                                    echo ($completed == $total && $total > 0) ? "Completed" : "$completed/$total"; 
                                    ?>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?php echo $learning_languages['fr']['progress_percentage']; ?>%"></div>
                                </div>
                            </div>
                            <a href="learn.php?lang=fr" class="btn btn-primary">Continue Learning</a>
                        <?php else: ?>
                            <p>Learn the language of love and culture</p>
                            <a href="start_language.php?lang=fr" class="btn btn-primary">Start Learning</a>
                        <?php endif; ?>
                    </div>

                    <div class="language-card <?php echo isset($learning_languages['de']) ? 'learning' : ''; ?>">
                        <div class="flag">
                            <img src="https://flagcdn.com/w320/de.png" alt="German Flag">
                        </div>
                        <h3>German</h3>
                        <?php if (isset($learning_languages['de'])): ?>
                            <div class="progress-info">
                                <div class="level">
                                    <?php 
                                    $completed = $learning_languages['de']['completed_units'];
                                    $total = $learning_languages['de']['total_units'];
                                    echo ($completed == $total && $total > 0) ? "Completed" : "$completed/$total"; 
                                    ?>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?php echo $learning_languages['de']['progress_percentage']; ?>%"></div>
                                </div>
                            </div>
                            <a href="learn.php?lang=de" class="btn btn-primary">Continue Learning</a>
                        <?php else: ?>
                            <p>Master the language of precision and philosophy</p>
                            <a href="start_language.php?lang=de" class="btn btn-primary">Start Learning</a>
                        <?php endif; ?>
                    </div>

                    <div class="language-card <?php echo isset($learning_languages['es']) ? 'learning' : ''; ?>">
                        <div class="flag">
                            <img src="https://flagcdn.com/w320/es.png" alt="Spanish Flag">
                        </div>
                        <h3>Spanish</h3>
                        <?php if (isset($learning_languages['es'])): ?>
                            <div class="progress-info">
                                <div class="level">
                                    <?php 
                                    $completed = $learning_languages['es']['completed_units'];
                                    $total = $learning_languages['es']['total_units'];
                                    echo ($completed == $total && $total > 0) ? "Completed" : "$completed/$total"; 
                                    ?>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?php echo $learning_languages['es']['progress_percentage']; ?>%"></div>
                                </div>
                            </div>
                            <a href="learn.php?lang=es" class="btn btn-primary">Continue Learning</a>
                        <?php else: ?>
                            <p>Explore the vibrant world of Hispanic culture</p>
                            <a href="start_language.php?lang=es" class="btn btn-primary">Start Learning</a>
                        <?php endif; ?>
                    </div>

                    <div class="language-card <?php echo isset($learning_languages['it']) ? 'learning' : ''; ?>">
                        <div class="flag">
                            <img src="https://flagcdn.com/w320/it.png" alt="Italian Flag">
                        </div>
                        <h3>Italian</h3>
                        <?php if (isset($learning_languages['it'])): ?>
                            <div class="progress-info">
                                <div class="level">
                                    <?php 
                                    $completed = $learning_languages['it']['completed_units'];
                                    $total = $learning_languages['it']['total_units'];
                                    echo ($completed == $total && $total > 0) ? "Completed" : "$completed/$total"; 
                                    ?>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?php echo $learning_languages['it']['progress_percentage']; ?>%"></div>
                                </div>
                            </div>
                            <a href="learn.php?lang=it" class="btn btn-primary">Continue Learning</a>
                        <?php else: ?>
                            <p>Dive into the language of art and cuisine</p>
                            <a href="start_language.php?lang=it" class="btn btn-primary">Start Learning</a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="stats-section">
                <h2>Your Learning Stats</h2>
                <div class="stat-cards">
                    <div class="stat-card">
                        <div class="stat-icon"><i class='bx bx-calendar-check'></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $daily_streak; ?></div>
                            <div class="stat-label">Daily Streak</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class='bx bx-time'></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $minutes_learned; ?></div>
                            <div class="stat-label">Minutes Learned</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class='bx bx-star'></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $xp_points; ?></div>
                            <div class="stat-label">XP Points</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class='bx bx-crown'></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $achievements; ?></div>
                            <div class="stat-label">Achievements</div>
                        </div>
                    </div>
                </div>
            </section>
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
