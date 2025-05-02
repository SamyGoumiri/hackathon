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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="dashboard.css">
    <title>Lango - Dashboard</title>
</head>

<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Lango</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="achievements.php">Achievements</a></li>
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
                    <h2>Welcome back, <?php echo htmlspecialchars(ucfirst($user['first_name'])); ?>!</h2>
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
                                    <?php echo isset($learning_languages['fr']['proficiency_level']) ? ucfirst($learning_languages['fr']['proficiency_level']) : 'Beginner'; ?>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?php echo getLevelPercentage(isset($learning_languages['fr']['proficiency_level']) ? $learning_languages['fr']['proficiency_level'] : null); ?>%"></div>
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
                                    <?php echo isset($learning_languages['de']['proficiency_level']) ? ucfirst($learning_languages['de']['proficiency_level']) : 'Beginner'; ?>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?php echo getLevelPercentage(isset($learning_languages['de']['proficiency_level']) ? $learning_languages['de']['proficiency_level'] : null); ?>%"></div>
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
                                    <?php echo isset($learning_languages['es']['proficiency_level']) ? ucfirst($learning_languages['es']['proficiency_level']) : 'Beginner'; ?>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?php echo getLevelPercentage(isset($learning_languages['es']['proficiency_level']) ? $learning_languages['es']['proficiency_level'] : null); ?>%"></div>
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
                                    <?php echo isset($learning_languages['it']['proficiency_level']) ? ucfirst($learning_languages['it']['proficiency_level']) : 'Beginner'; ?>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?php echo getLevelPercentage(isset($learning_languages['it']['proficiency_level']) ? $learning_languages['it']['proficiency_level'] : null); ?>%"></div>
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
                            <div class="stat-value">0</div>
                            <div class="stat-label">Daily Streak</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class='bx bx-time'></i></div>
                        <div class="stat-content">
                            <div class="stat-value">0</div>
                            <div class="stat-label">Minutes Learned</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class='bx bx-star'></i></div>
                        <div class="stat-content">
                            <div class="stat-value">0</div>
                            <div class="stat-label">XP Points</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class='bx bx-crown'></i></div>
                        <div class="stat-content">
                            <div class="stat-value">0</div>
                            <div class="stat-label">Achievements</div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <p>&copy; <?php echo date('Y'); ?> Lango. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.querySelector('.user-info').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('active');
        });
    </script>
</body>
</html>

<?php
function getLevelPercentage($level) {
    switch ($level) {
        case 'beginner': return 25;
        case 'intermediate': return 50;
        case 'advanced': return 75;
        case 'fluent': return 100;
        default: return 0; // Return 0% for null or invalid values
    }
}
?>
