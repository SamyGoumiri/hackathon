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

$high_score_query = "SELECT MAX(score) as high_score FROM test_results WHERE user_id = ? AND test_id = 0";
$stmt = $conn->prepare($high_score_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$high_score_result = $stmt->get_result();
$high_score_data = $high_score_result->fetch_assoc();
$high_score = $high_score_data['high_score'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="games.css">
    <link rel="stylesheet" href="speed-translate.css">
    <title>Speed Translate - Esperanto</title>
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
        <div class="game-container">
            <div id="game-setup" class="game-screen active">
                <h2>Speed Translate</h2>
                <p>Translate as many words as you can before time runs out!</p>
                
                <div class="setup-options">
                    <div class="option-group">
                        <h3>Choose Language</h3>
                        <div class="language-options">
                            <button class="language-option selected" data-language="fr" data-language-name="French">
                                <img src="../../../assets/images/flags/fr-flag.png" alt="French Flag">
                                <span>French</span>
                            </button>
                            <button class="language-option" data-language="es" data-language-name="Spanish">
                                <img src="../../../assets/images/flags/es-flag.png" alt="Spanish Flag">
                                <span>Spanish</span>
                            </button>
                            <button class="language-option" data-language="de" data-language-name="German">
                                <img src="../../../assets/images/flags/de-flag.png" alt="German Flag">
                                <span>German</span>
                            </button>
                            <button class="language-option" data-language="it" data-language-name="Italian">
                                <img src="../../../assets/images/flags/it-flag.png" alt="Italian Flag">
                                <span>Italian</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="option-group">
                        <h3>Select Difficulty</h3>
                        <div class="difficulty-options">
                            <button class="difficulty-option" data-time="10">10 seconds</button>
                            <button class="difficulty-option selected" data-time="20">20 seconds</button>
                            <button class="difficulty-option" data-time="30">30 seconds</button>
                        </div>
                    </div>
                </div>
                
                <button id="start-game" class="btn btn-primary">Start Game</button>
                
                <div class="game-instructions">
                    <h3>How to Play</h3>
                    <ol>
                        <li>You'll see words in your selected language</li>
                        <li>Type the English translation in the text box</li>
                        <li>Press Enter or click Submit to check your answer</li>
                        <li>Each correct answer gives you 1 point</li>
                        <li>Try to get as many points as possible before time runs out!</li>
                    </ol>
                </div>
                
                <div class="high-score-display">
                    <p>Your High Score: <span id="user-high-score"><?php echo $high_score; ?></span> points</p>
                </div>
            </div>
            
            <div id="game-play" class="game-screen">
                <div class="game-header">
                    <div class="game-info">
                        <span class="language-display">French → English</span>
                    </div>
                    <div class="timer">
                        <i class='bx bx-time'></i>
                        <span id="time-remaining">20</span>s
                    </div>
                    <div class="score-display">
                        Score: <span id="current-score">0</span>
                    </div>
                </div>
                
                <div class="word-display">
                    <h2 id="word-to-translate"></h2>
                </div>
                
                <div class="answer-input">
                    <input type="text" id="user-answer" placeholder="Type English translation..." autocomplete="off">
                    <button id="submit-answer" class="btn">Submit</button>
                </div>
                
                <div class="feedback-display" id="feedback"></div>
            </div>
            
            <div id="game-over" class="game-screen">
                <h2>Game Over!</h2>
                <div class="final-score">
                    <p>Your Score: <span id="final-score">0</span> points</p>
                    <p>High Score: <span id="high-score">0</span> points</p>
                </div>
                
                <div class="stats-summary">
                    <div class="stat">
                        <p>Words Attempted: <span id="words-attempted">0</span></p>
                    </div>
                    <div class="stat">
                        <p>Correct Answers: <span id="correct-answers">0</span></p>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <button id="play-again" class="btn btn-primary">Play Again</button>
                    <a href="games.php" class="btn btn-secondary">Return to Games</a>
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
        const vocabulary = {
            fr: [
                {word: "bonjour", translation: "hello"},
                {word: "au revoir", translation: "goodbye"},
                {word: "merci", translation: "thank you"},
                {word: "s'il vous plaît", translation: "please"},
                {word: "oui", translation: "yes"},
                {word: "non", translation: "no"},
                {word: "chat", translation: "cat"},
                {word: "chien", translation: "dog"},
                {word: "maison", translation: "house"},
                {word: "eau", translation: "water"},
                {word: "pain", translation: "bread"},
                {word: "lait", translation: "milk"},
                {word: "livre", translation: "book"},
                {word: "table", translation: "table"},
                {word: "chaise", translation: "chair"},
                {word: "porte", translation: "door"},
                {word: "fenêtre", translation: "window"},
                {word: "rouge", translation: "red"},
                {word: "bleu", translation: "blue"},
                {word: "vert", translation: "green"}
            ],
            es: [
                {word: "hola", translation: "hello"},
                {word: "adiós", translation: "goodbye"},
                {word: "gracias", translation: "thank you"},
                {word: "por favor", translation: "please"},
                {word: "sí", translation: "yes"},
                {word: "no", translation: "no"},
                {word: "gato", translation: "cat"},
                {word: "perro", translation: "dog"},
                {word: "casa", translation: "house"},
                {word: "agua", translation: "water"},
                {word: "pan", translation: "bread"},
                {word: "leche", translation: "milk"},
                {word: "libro", translation: "book"},
                {word: "mesa", translation: "table"},
                {word: "silla", translation: "chair"},
                {word: "puerta", translation: "door"},
                {word: "ventana", translation: "window"},
                {word: "rojo", translation: "red"},
                {word: "azul", translation: "blue"},
                {word: "verde", translation: "green"}
            ],
            de: [
                {word: "hallo", translation: "hello"},
                {word: "auf wiedersehen", translation: "goodbye"},
                {word: "danke", translation: "thank you"},
                {word: "bitte", translation: "please"},
                {word: "ja", translation: "yes"},
                {word: "nein", translation: "no"},
                {word: "katze", translation: "cat"},
                {word: "hund", translation: "dog"},
                {word: "haus", translation: "house"},
                {word: "wasser", translation: "water"},
                {word: "brot", translation: "bread"},
                {word: "milch", translation: "milk"},
                {word: "buch", translation: "book"},
                {word: "tisch", translation: "table"},
                {word: "stuhl", translation: "chair"},
                {word: "tür", translation: "door"},
                {word: "fenster", translation: "window"},
                {word: "rot", translation: "red"},
                {word: "blau", translation: "blue"},
                {word: "grün", translation: "green"}
            ],
            it: [
                {word: "ciao", translation: "hello"},
                {word: "arrivederci", translation: "goodbye"},
                {word: "grazie", translation: "thank you"},
                {word: "per favore", translation: "please"},
                {word: "sì", translation: "yes"},
                {word: "no", translation: "no"},
                {word: "gatto", translation: "cat"},
                {word: "cane", translation: "dog"},
                {word: "casa", translation: "house"},
                {word: "acqua", translation: "water"},
                {word: "pane", translation: "bread"},
                {word: "latte", translation: "milk"},
                {word: "libro", translation: "book"},
                {word: "tavolo", translation: "table"},
                {word: "sedia", translation: "chair"},
                {word: "porta", translation: "door"},
                {word: "finestra", translation: "window"},
                {word: "rosso", translation: "red"},
                {word: "blu", translation: "blue"},
                {word: "verde", translation: "green"}
            ]
        };
        
        const userId = <?php echo $user_id; ?>;
    </script>
    <script src="speed-translate.js"></script>
</body>
</html>
