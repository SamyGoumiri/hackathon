<?php
session_start();
require_once "../../../database/connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$unit_id = 6; // Unit 6 is La Vita Quotidiana for Italian

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
    if (isset($_POST['q1']) && $_POST['q1'] === 'svegliarsi') {
        $score++;
    }
    
    // Question 2
    if (isset($_POST['q2']) && $_POST['q2'] === 'prendere') {
        $score++;
    }
    
    // Question 3
    if (isset($_POST['q3']) && $_POST['q3'] === 'mezzogiorno') {
        $score++;
    }
    
    // Question 4
    if (isset($_POST['q4']) && $_POST['q4'] === 'otto_e_un_quarto') {
        $score++;
    }
    
    // Question 5
    if (isset($_POST['q5']) && $_POST['q5'] === 'mercoledi') {
        $score++;
    }
    
    // Question 6
    if (isset($_POST['q6']) && strtolower(trim($_POST['q6'])) === 'gennaio') {
        $score++;
    }
    
    // Question 7
    if (isset($_POST['q7']) && $_POST['q7'] === 'fa_caldo') {
        $score++;
    }
    
    // Question 8
    if (isset($_POST['q8']) && strtolower(trim($_POST['q8'])) === 'piove') {
        $score++;
    }
    
    // Question 9
    if (isset($_POST['q9']) && $_POST['q9'] === 'estate') {
        $score++;
    }
    
    // Question 10
    if (isset($_POST['q10']) && $_POST['q10'] === 'sono') {
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

    <title>Lango - Unit 6 Test: La Vita Quotidiana</title>
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
        <h1>Unit 6 Test: La Vita Quotidiana (Daily Life)</h1>
        
        <?php if ($test_submitted): ?>
        <div class="result-container">
            <h2>Test Results</h2>
            <div class="score-display"><?php echo $score; ?> / 10 points (<?php echo round($percentage_score); ?>%)</div>
            
            <?php if ($percentage_score >= 80): ?>
                <div class="result-message success">Excellent! You have a strong grasp of daily life vocabulary in Italian.</div>
            <?php elseif ($percentage_score >= 60): ?>
                <div class="result-message neutral">Good job! You understand many aspects of daily life in Italian, but there's room for improvement.</div>
            <?php else: ?>
                <div class="result-message failure">You might need more practice with daily routines and time expressions. Consider reviewing the lessons again.</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="test-container">
            <?php if (!$test_submitted): ?>
            <p>This test will assess your knowledge of daily routines, telling time, days and months, and weather expressions in Italian.</p>
            <p>Answer all questions to the best of your ability.</p>
            
            <form method="post" action="">
                <!-- Question 1 -->
                <div class="question-container">
                    <div class="question-text">1. Which phrase means "to wake up" in Italian?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q1_a" name="q1" value="alzarsi">
                            <label for="q1_a">alzarsi</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_b" name="q1" value="svegliarsi">
                            <label for="q1_b">svegliarsi</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_c" name="q1" value="fare_la_doccia">
                            <label for="q1_c">fare la doccia</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_d" name="q1" value="vestirsi">
                            <label for="q1_d">vestirsi</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 2 -->
                <div class="question-container">
                    <div class="question-text">2. Which verb means "to take" or "to have" (as in having breakfast) in Italian?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q2_a" name="q2" value="prendere">
                            <label for="q2_a">prendere</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_b" name="q2" value="fare">
                            <label for="q2_b">fare</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_c" name="q2" value="andare">
                            <label for="q2_c">andare</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_d" name="q2" value="avere">
                            <label for="q2_d">avere</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 3 -->
                <div class="question-container">
                    <div class="question-text">3. What is the Italian word for "noon"?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q3_a" name="q3" value="mezzanotte">
                            <label for="q3_a">mezzanotte</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_b" name="q3" value="mattina">
                            <label for="q3_b">mattina</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_c" name="q3" value="mezzogiorno">
                            <label for="q3_c">mezzogiorno</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_d" name="q3" value="sera">
                            <label for="q3_d">sera</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 4 -->
                <div class="question-container">
                    <div class="question-text">4. How do you say "8:15" (quarter past eight) in Italian?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q4_a" name="q4" value="otto_meno_un_quarto">
                            <label for="q4_a">otto meno un quarto</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_b" name="q4" value="otto_e_un_quarto">
                            <label for="q4_b">otto e un quarto</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_c" name="q4" value="otto_e_quindici">
                            <label for="q4_c">otto e quindici</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_d" name="q4" value="otto_quarto">
                            <label for="q4_d">otto quarto</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 5 -->
                <div class="question-container">
                    <div class="question-text">5. Which day comes between Tuesday (martedì) and Thursday (giovedì)?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q5_a" name="q5" value="lunedi">
                            <label for="q5_a">lunedì</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_b" name="q5" value="martedi">
                            <label for="q5_b">martedì</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_c" name="q5" value="mercoledi">
                            <label for="q5_c">mercoledì</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_d" name="q5" value="venerdi">
                            <label for="q5_d">venerdì</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 6 -->
                <div class="question-container">
                    <div class="question-text">6. What is the first month of the year in Italian?</div>
                    <div class="options-container">
                        <input type="text" name="q6" class="text-input" placeholder="Type your answer in Italian">
                    </div>
                </div>
                
                <!-- Question 7 -->
                <div class="question-container">
                    <div class="question-text">7. How do you say "It's hot" when referring to the weather in Italian?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q7_a" name="q7" value="piove">
                            <label for="q7_a">Piove</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_b" name="q7" value="ce_sole">
                            <label for="q7_b">C'è sole</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_c" name="q7" value="fa_caldo">
                            <label for="q7_c">Fa caldo</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_d" name="q7" value="fa_freddo">
                            <label for="q7_d">Fa freddo</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 8 -->
                <div class="question-container">
                    <div class="question-text">8. Translate: "It's raining." into Italian</div>
                    <div class="options-container">
                        <input type="text" name="q8" class="text-input" placeholder="Type your answer in Italian">
                    </div>
                </div>
                
                <!-- Question 9 -->
                <div class="question-container">
                    <div class="question-text">9. Which season in Italian corresponds to summer?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q9_a" name="q9" value="primavera">
                            <label for="q9_a">la primavera</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q9_b" name="q9" value="estate">
                            <label for="q9_b">l'estate</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q9_c" name="q9" value="autunno">
                            <label for="q9_c">l'autunno</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q9_d" name="q9" value="inverno">
                            <label for="q9_d">l'inverno</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 10 -->
                <div class="question-container">
                    <div class="question-text">10. What is the conjugation of "essere" (to be) for "I am"?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q10_a" name="q10" value="sono">
                            <label for="q10_a">sono</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_b" name="q10" value="sei">
                            <label for="q10_b">sei</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_c" name="q10" value="e">
                            <label for="q10_c">è</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_d" name="q10" value="siamo">
                            <label for="q10_d">siamo</label>
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
