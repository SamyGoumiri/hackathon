<?php
session_start();
require_once "../../../database/connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$unit_id = 7; // Unit 7 is Fare Acquisti (Shopping) for Italian

$user_query = "SELECT username, first_name, last_name FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$check_lessons_query = "SELECT COUNT(*) as total_lessons, 
                        (SELECT COUNT(*) FROM user_progress up 
                         JOIN lessons l ON up.lesson_id = l.lesson_id 
                         WHERE l.unit_id = ? AND up.user_id = ? AND up.status = 'completed') as completed_lessons
                       FROM lessons WHERE unit_id = ?";
$stmt = $conn->prepare($check_lessons_query);
$stmt->bind_param("iii", $unit_id, $user_id, $unit_id);
$stmt->execute();
$lessons_result = $stmt->get_result();
$lessons_data = $lessons_result->fetch_assoc();

if ($lessons_data['completed_lessons'] < $lessons_data['total_lessons']) {
    $_SESSION['error_message'] = "Please complete all lessons in this unit before taking the test.";
    header("Location: units-content.php?unit=" . $unit_id);
    exit();
}

$score = 0;
$test_submitted = false;

if (isset($_POST['submit_test'])) {
    $test_submitted = true;
    $total_questions = 10;
    
    // Question 1
    if (isset($_POST['q1']) && $_POST['q1'] === 'il_supermercato') {
        $score++;
    }
    
    // Question 2
    if (isset($_POST['q2']) && $_POST['q2'] === 'il_cameriere') {
        $score++;
    }
    
    // Question 3
    if (isset($_POST['q3']) && $_POST['q3'] === 'taglia_media') {
        $score++;
    }
    
    // Question 4
    if (isset($_POST['q4']) && $_POST['q4'] === 'il_cambio') {
        $score++;
    }
    
    // Question 5
    if (isset($_POST['q5']) && $_POST['q5'] === 'con_carta') {
        $score++;
    }
    
    // Question 6
    if (isset($_POST['q6']) && strtolower(trim($_POST['q6'])) === 'quanto costa') {
        $score++;
    }
    
    // Question 7
    if (isset($_POST['q7']) && $_POST['q7'] === 'carne') {
        $score++;
    }
    
    // Question 8
    if (isset($_POST['q8']) && strtolower(trim($_POST['q8'])) === 'vorrei il conto') {
        $score++;
    }
    
    // Question 9
    if (isset($_POST['q9']) && $_POST['q9'] === 'la_boutique') {
        $score++;
    }
    
    // Question 10
    if (isset($_POST['q10']) && $_POST['q10'] === 'cinquanta') {
        $score++;
    }
    
    $percentage_score = ($score / $total_questions) * 100;
    
    $record_test_query = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
                         VALUES (?, 'unit_test', ?)";
    $test_details = json_encode([
        'unit_id' => $unit_id,
        'score' => $percentage_score,
        'date' => date('Y-m-d H:i:s')
    ]);
    
    $stmt = $conn->prepare($record_test_query);
    $stmt->bind_param("is", $user_id, $test_details);
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

    <title>Esperanto - Unit 7 Test: Fare Acquisti</title>
    <style>
        .test-container {
            background-color: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(127, 87, 241, 0.1);
            margin-bottom: 30px;
        }
        
        .question-container {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .question-text {
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }
        
        .options-container {
            margin-left: 10px;
        }
        
        .option {
            margin-bottom: 10px;
        }
        
        .option input[type="radio"], .option input[type="checkbox"] {
            margin-right: 10px;
        }
        
        .option label {
            cursor: pointer;
        }
        
        .submit-container {
            margin-top: 30px;
            text-align: center;
        }
        
        .result-container {
            background-color: #f9f4ff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .score-display {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #7F57F1;
        }
        
        .text-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 8px;
        }
        
        .result-message {
            margin-top: 15px;
            font-weight: 600;
        }
        
        .success {
            color: #2e7d32;
        }
        
        .failure {
            color: #c62828;
        }
        
        .neutral {
            color: #f57c00;
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
        <h1>Unit 7 Test: Fare Acquisti (Shopping)</h1>
        
        <?php if ($test_submitted): ?>
        <div class="result-container">
            <h2>Test Results</h2>
            <div class="score-display"><?php echo $score; ?> / 10 points (<?php echo round($percentage_score); ?>%)</div>
            
            <?php if ($percentage_score >= 80): ?>
                <div class="result-message success">Excellent! You have a strong grasp of shopping vocabulary in Italian.</div>
            <?php elseif ($percentage_score >= 60): ?>
                <div class="result-message neutral">Good job! You understand many aspects of shopping in Italian, but there's room for improvement.</div>
            <?php else: ?>
                <div class="result-message failure">You might need more practice with shopping vocabulary. Consider reviewing the lessons again.</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="test-container">
            <?php if (!$test_submitted): ?>
            <p>This test will assess your knowledge of shopping vocabulary, restaurant phrases, clothing terminology, and money expressions in Italian.</p>
            <p>Answer all questions to the best of your ability.</p>
            
            <form method="post" action="">
                <!-- Question 1 -->
                <div class="question-container">
                    <div class="question-text">1. Which word means "supermarket" in Italian?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q1_a" name="q1" value="il_negozio">
                            <label for="q1_a">il negozio</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_b" name="q1" value="il_supermercato">
                            <label for="q1_b">il supermercato</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_c" name="q1" value="il_mercato">
                            <label for="q1_c">il mercato</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_d" name="q1" value="la_farmacia">
                            <label for="q1_d">la farmacia</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 2 -->
                <div class="question-container">
                    <div class="question-text">2. What is the Italian word for "waiter"?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q2_a" name="q2" value="il_cameriere">
                            <label for="q2_a">il cameriere</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_b" name="q2" value="il_cassiere">
                            <label for="q2_b">il cassiere</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_c" name="q2" value="il_commesso">
                            <label for="q2_c">il commesso</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_d" name="q2" value="il_cuoco">
                            <label for="q2_d">il cuoco</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 3 -->
                <div class="question-container">
                    <div class="question-text">3. How would you ask for a medium-sized shirt in Italian?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q3_a" name="q3" value="taglia_piccola">
                            <label for="q3_a">Vorrei una camicia di taglia piccola</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_b" name="q3" value="taglia_media">
                            <label for="q3_b">Vorrei una camicia di taglia media</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_c" name="q3" value="taglia_grande">
                            <label for="q3_c">Vorrei una camicia di taglia grande</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_d" name="q3" value="colore">
                            <label for="q3_d">Vorrei una camicia di colore rosso</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 4 -->
                <div class="question-container">
                    <div class="question-text">4. Which term refers to an "exchange" (of merchandise) in Italian?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q4_a" name="q4" value="il_rimborso">
                            <label for="q4_a">il rimborso</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_b" name="q4" value="il_cambio">
                            <label for="q4_b">il cambio</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_c" name="q4" value="lo_scontrino">
                            <label for="q4_c">lo scontrino</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_d" name="q4" value="la_cassa">
                            <label for="q4_d">la cassa</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 5 -->
                <div class="question-container">
                    <div class="question-text">5. How do you say "by credit card" in Italian?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q5_a" name="q5" value="in_contanti">
                            <label for="q5_a">in contanti</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_b" name="q5" value="con_carta">
                            <label for="q5_b">con carta</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_c" name="q5" value="con_assegno">
                            <label for="q5_c">con assegno</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_d" name="q5" value="con_bancomat">
                            <label for="q5_d">con bancomat</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 6 -->
                <div class="question-container">
                    <div class="question-text">6. How do you ask "How much does it cost?" in Italian?</div>
                    <div class="options-container">
                        <input type="text" name="q6" class="text-input" placeholder="Type your answer in Italian">
                    </div>
                </div>
                
                <!-- Question 7 -->
                <div class="question-container">
                    <div class="question-text">7. Which product would you find in "la macelleria" (butcher section)?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q7_a" name="q7" value="pane">
                            <label for="q7_a">pane (bread)</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_b" name="q7" value="carne">
                            <label for="q7_b">carne (meat)</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_c" name="q7" value="formaggio">
                            <label for="q7_c">formaggio (cheese)</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_d" name="q7" value="pesce">
                            <label for="q7_d">pesce (fish)</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 8 -->
                <div class="question-container">
                    <div class="question-text">8. How do you ask for the bill in a restaurant in Italian?</div>
                    <div class="options-container">
                        <input type="text" name="q8" class="text-input" placeholder="Type your answer in Italian">
                    </div>
                </div>
                
                <!-- Question 9 -->
                <div class="question-container">
                    <div class="question-text">9. What is the Italian word for a clothing shop or boutique?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q9_a" name="q9" value="la_boutique">
                            <label for="q9_a">la boutique</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q9_b" name="q9" value="la_libreria">
                            <label for="q9_b">la libreria</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q9_c" name="q9" value="la_farmacia">
                            <label for="q9_c">la farmacia</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q9_d" name="q9" value="la_gioielleria">
                            <label for="q9_d">la gioielleria</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 10 -->
                <div class="question-container">
                    <div class="question-text">10. What is the Italian word for the number 50?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q10_a" name="q10" value="quaranta">
                            <label for="q10_a">quaranta</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_b" name="q10" value="cinquanta">
                            <label for="q10_b">cinquanta</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_c" name="q10" value="sessanta">
                            <label for="q10_c">sessanta</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_d" name="q10" value="settanta">
                            <label for="q10_d">settanta</label>
                        </div>
                    </div>
                </div>
                
                <div class="submit-container">
                    <button type="submit" name="submit_test" class="btn btn-primary">Submit Test</button>
                </div>
            </form>
            <?php else: ?>
            <div class="navigation-buttons">
                <a href="units-content.php?unit=<?php echo $unit_id; ?>" class="btn btn-secondary">
                    <i class='bx bx-arrow-back'></i> Back to Unit
                </a>
                <a href="units.php" class="btn btn-primary">
                    Continue to Next Unit <i class='bx bx-right-arrow-alt'></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        document.querySelector('.user-info').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('active');
        });
    </script>
</body>
</html>
