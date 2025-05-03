<?php
session_start();
require_once '../../../database/connect.php';

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

// Update high score query to use the game_high_scores table
$high_score_query = "SELECT score FROM game_high_scores WHERE user_id = ? AND game_id = 'speed_translate'";
$stmt = $conn->prepare($high_score_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$high_score_result = $stmt->get_result();
$high_score_data = $high_score_result->fetch_assoc();
$high_score = $high_score_data['score'] ?? 0;

// Get Word Master high score
$word_master_score_query = "SELECT score FROM game_high_scores WHERE user_id = ? AND game_id = 'word_master'";
$stmt = $conn->prepare($word_master_score_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$word_master_result = $stmt->get_result();
$word_master_data = $word_master_result->fetch_assoc();
$word_master_score = $word_master_data['score'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="games.css">
    <title>Esperanto - Language Games</title>
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
                    <li><a href="games.php" class="active">Games</a></li>
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

    <main>
        <div class="games-container">
            <section class="welcome-section">
                <div class="welcome-card">
                    <h2>Language Games</h2>
                    <p>Have fun while learning languages with our interactive games designed to improve your vocabulary and translation skills.</p>
                </div>
            </section>

            <section class="games-section">
                <h2>Choose a Game to Play</h2>
                    
                    <div class="game-card">
                        <div class="game-icon">
                            <i class='bx bx-globe'></i>
                        </div>
                        <h3>Word Master</h3>
                        <p>Test your translation skills! Choose a language and time limit, then translate as many words as you can to earn points and XP.</p>
                        <div class="game-stats">
                            <div class="stat-label">Your High Score</div>
                            <div class="stat-value"><?php echo $word_master_score; ?> points</div>
                        </div>
                        <a href="word-master/word-master.php" class="btn btn-primary">Play Now</a>
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
