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
    $learning_languages[$lang['code']] = $lang;
}

function getUserLanguageProgress($conn, $user_id, $course_id) {
    if (!$course_id) {
        return ['completed_units' => 0, 'total_units' => 0, 'percentage' => 0];
    }
    
    $units_query = "SELECT unit_id FROM units WHERE course_id = ? AND is_active = 1";
    $stmt = $conn->prepare($units_query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $units_result = $stmt->get_result();
    $total_units = $units_result->num_rows;
    
    $completed_units = 0;
    $total_lessons = 0;
    $completed_lessons = 0;
    
    while ($unit = $units_result->fetch_assoc()) {
        $unit_id = $unit['unit_id'];
        
        $lessons_query = "SELECT lesson_id FROM lessons WHERE unit_id = ? AND is_active = 1";
        $stmt = $conn->prepare($lessons_query);
        $stmt->bind_param("i", $unit_id);
        $stmt->execute();
        $lessons_result = $stmt->get_result();
        $unit_lesson_ids = [];
        while ($lesson = $lessons_result->fetch_assoc()) {
            $unit_lesson_ids[] = $lesson['lesson_id'];
        }
        
        $unit_total_lessons = count($unit_lesson_ids);
        if ($unit_total_lessons == 0) continue;
        
        $total_lessons += $unit_total_lessons;
        
        if (!empty($unit_lesson_ids)) {
            $placeholders = str_repeat('?,', count($unit_lesson_ids) - 1) . '?';
            $completed_lessons_query = "SELECT COUNT(*) as completed 
                                       FROM user_progress 
                                       WHERE user_id = ? 
                                       AND lesson_id IN ($placeholders) 
                                       AND status = 'completed'";
            
            $types = "i" . str_repeat("i", count($unit_lesson_ids));
            $params = array_merge([$user_id], $unit_lesson_ids);
            
            $stmt = $conn->prepare($completed_lessons_query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $completed_result = $stmt->get_result();
            $unit_completed_lessons = $completed_result->fetch_assoc()['completed'];
            
            $completed_lessons += $unit_completed_lessons;
            
            if ($unit_completed_lessons == $unit_total_lessons) {
                $completed_units++;
            }
        }
    }
    
    $lesson_percentage = ($total_lessons > 0) ? round(($completed_lessons / $total_lessons) * 100) : 0;
    $unit_percentage = ($total_units > 0) ? round(($completed_units / $total_units) * 100) : 0;
    
    return [
        'completed_units' => $completed_units,
        'total_units' => $total_units,
        'completed_lessons' => $completed_lessons,
        'total_lessons' => $total_lessons,
        'percentage' => $lesson_percentage
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
                                        $progress = $learning_languages['fr']['progress'];
                                        echo $progress['completed_units'] . "/" . $progress['total_units'] . " Units";
                                        echo ($progress['percentage'] == 100) ? " Completed!" : "";
                                    ?>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?php echo $progress['percentage']; ?>%"></div>
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
                                        $progress = $learning_languages['de']['progress'];
                                        echo $progress['completed_units'] . "/" . $progress['total_units'] . " Units";
                                        echo ($progress['percentage'] == 100) ? " Completed!" : "";
                                    ?>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?php echo $progress['percentage']; ?>%"></div>
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
                                        $progress = $learning_languages['es']['progress'];
                                        echo $progress['completed_units'] . "/" . $progress['total_units'] . " Units";
                                        echo ($progress['percentage'] == 100) ? " Completed!" : "";
                                    ?>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?php echo $progress['percentage']; ?>%"></div>
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
                                        $progress = $learning_languages['it']['progress'];
                                        echo $progress['completed_units'] . "/" . $progress['total_units'] . " Units";
                                        echo ($progress['percentage'] == 100) ? " Completed!" : "";
                                    ?>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?php echo $progress['percentage']; ?>%"></div>
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
