<?php
session_start();
require_once '../../../../database/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = "SELECT username, first_name, last_name FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$high_score_query = "SELECT score FROM game_high_scores WHERE user_id = ? AND game_id = 'word_master'";
$stmt = $conn->prepare($high_score_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$high_score_result = $stmt->get_result();
$high_score_data = $high_score_result->fetch_assoc();
$high_score = $high_score_data['score'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../games.css">
    <link rel="stylesheet" href="word-master.css">
    <title>Word Master - Esperanto</title>
</head>

<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Esperanto</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../../dashboard.php">Dashboard</a></li>
                    <li><a href="../games.php" class="active">Games</a></li>
                    <li><a href="../../chatbot/chatbot.php">ChatBot</a></li>
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
                    <a href="../../profile.php"><i class='bx bx-user'></i> Profile</a>
                    <a href="../../settings.php"><i class='bx bx-cog'></i> Settings</a>
                    <a href="../../../auth/logout.php"><i class='bx bx-log-out'></i> Log Out</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="game-container">
            <div class="word-master-header">
                <h1>Word Master</h1>
                <p>Translate words from your chosen language to English as quickly as possible!</p>
            </div>

            <div class="game-stats-bar">
                <div class="stat-item">
                    <div class="stat-label">High Score</div>
                    <div class="stat-value" id="highScore"><?php echo $high_score; ?></div>
                </div>
            </div>

            <div id="setupScreen" class="game-screen">
                <div class="setup-options">

                    <div class="option-group">
                        <h3>Select Language</h3>
                        <div class="language-selection">
                            <button class="language-option" data-lang="fr">French</button>
                            <button class="language-option" data-lang="es">Spanish</button>
                            <button class="language-option" data-lang="de">German</button>
                            <button class="language-option" data-lang="it">Italian</button>
                        </div>
                    </div>

                    <div class="option-group">
                        <h3>Select Time</h3>
                        <div class="time-selection">
                            <button class="time-option" data-time="15">15 seconds</button>
                            <button class="time-option" data-time="30">30 seconds</button>
                            <button class="time-option" data-time="60">60 seconds</button>
                        </div>
                    </div>

                    <div class="selected-options">
                        <p>Selected language: <span id="selectedLanguage">None</span></p>
                        <p>Selected time: <span id="selectedTime">None</span> seconds</p>
                    </div>

                    <button id="startGameBtn" class="btn-primary btn-large" disabled>Start Game</button>
                </div>
            </div>

            <div id="gameScreen" class="game-screen hidden">
                <div class="game-header">
                    <div class="timer-container">
                        <div class="timer-label">Time Remaining</div>
                        <div id="timer" class="timer">00</div>
                    </div>
                    <div class="score-container">
                        <div class="score-label">Score</div>
                        <div id="currentScore" class="score">0</div>
                    </div>
                </div>

                <div class="game-content">
                    <div class="word-display">
                        <div class="word-label">Translate this word:</div>
                        <div id="wordToTranslate" class="word"></div>
                    </div>

                    <div class="translation-input">
                        <input type="text" id="translationInput" placeholder="Type your translation here" autocomplete="off">
                        <button id="submitTranslation" class="btn-primary">Submit</button>
                        <button id="skipWord" class="btn-secondary">Skip</button>
                    </div>

                    <div class="translation-feedback" id="translationFeedback"></div>
                </div>
            </div>



            <div id="resultsScreen" class="game-screen hidden">
                <div class="results-header">
                    <h2>Game Over!</h2>
                </div>

                <div class="results-content">
                    <div class="result-item">
                        <div class="result-label">Your Score</div>
                        <div id="finalScore" class="result-value">0</div>
                    </div>
                    
                    <div class="result-item">
                        <div class="result-label">Words Translated</div>
                        <div id="wordsTranslated" class="result-value">0</div>
                    </div>

                    <div id="newHighScore" class="new-high-score hidden">
                        <i class='bx bx-trophy'></i>
                        <span>New High Score!</span>
                    </div>
                </div>

                <div class="results-actions">
                    <button id="playAgainBtn" class="btn-primary">Play Again</button>
                    <a href="../games.php" class="btn-secondary">Back to Games</a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <p>&copy; <?php echo date('Y'); ?> Esperanto. All rights reserved.</p>
        </div>
    </footer>

    <input type="hidden" id="userId" value="<?php echo $user_id; ?>">
    
    <script src="word-master.js"></script>
    <script>
        document.querySelector('.user-info').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('active');
        });
    </script>
</body>
</html>
