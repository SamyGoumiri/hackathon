<?php
session_start();
require_once "../../../database/connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$unit_id = 12; // Fourth Spanish unit ID

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
    if (isset($_POST['q1']) && $_POST['q1'] === 'tren') {
        $score++;
    }
    
    // Question 2
    if (isset($_POST['q2']) && $_POST['q2'] === 'a_la_derecha') {
        $score++;
    }
    
    // Question 3
    if (isset($_POST['q3']) && $_POST['q3'] === 'habitacion_doble') {
        $score++;
    }
    
    // Question 4
    if (isset($_POST['q4']) && $_POST['q4'] === 'museo') {
        $score++;
    }
    
    // Question 5
    if (isset($_POST['q5']) && $_POST['q5'] === 'pasaporte') {
        $score++;
    }
    
    // Question 6
    if (isset($_POST['q6']) && strtolower(trim($_POST['q6'])) === "estoy perdido") {
        $score++;
    }
    
    // Question 7
    if (isset($_POST['q7']) && $_POST['q7'] === 'avion') {
        $score++;
    }
    
    // Question 8
    if (isset($_POST['q8']) && strtolower(trim($_POST['q8'])) === "cuál es el precio") {
        $score++;
    }
    
    // Question 9
    $q9_answers = isset($_POST['q9']) ? $_POST['q9'] : [];
    if (
        in_array('mapa', $q9_answers) && 
        in_array('plano', $q9_answers) && 
        count($q9_answers) === 2
    ) {
        $score++;
    }
    
    // Question 10
    if (isset($_POST['q10']) && $_POST['q10'] === 'retraso') {
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

    <title>Lango - Unit 4 Test: Los Viajes</title>
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
        <h1>Unit 4 Test: Los Viajes (Traveling)</h1>
        
        <?php if ($test_submitted): ?>
        <div class="result-container">
            <h2>Test Results</h2>
            <div class="score-display"><?php echo $score; ?> / 10 points (<?php echo round($percentage_score); ?>%)</div>
            
            <?php if ($percentage_score >= 80): ?>
                <div class="result-message success">Excellent! You're ready to travel in Spanish-speaking countries.</div>
            <?php elseif ($percentage_score >= 60): ?>
                <div class="result-message neutral">Good job! You know the basics of traveling in Spanish, but there's room for improvement.</div>
            <?php else: ?>
                <div class="result-message failure">You might need more practice with travel vocabulary. Consider reviewing the lessons again.</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="test-container">
            <?php if (!$test_submitted): ?>
            <p>This test will assess your knowledge of transportation vocabulary, directions, hotel reservations, tourist attractions, and dealing with travel problems in Spanish.</p>
            <p>Answer all questions to the best of your ability.</p>
            
            <form method="post" action="">
                <!-- Question 1 -->
                <div class="question-container">
                    <div class="question-text">1. What is the Spanish word for "train"?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q1_a" name="q1" value="autobus">
                            <label for="q1_a">autobús</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_b" name="q1" value="coche">
                            <label for="q1_b">coche</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_c" name="q1" value="tren">
                            <label for="q1_c">tren</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_d" name="q1" value="metro">
                            <label for="q1_d">metro</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 2 -->
                <div class="question-container">
                    <div class="question-text">2. How do you say "to the right" in Spanish when giving directions?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q2_a" name="q2" value="a_la_izquierda">
                            <label for="q2_a">a la izquierda</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_b" name="q2" value="a_la_derecha">
                            <label for="q2_b">a la derecha</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_c" name="q2" value="todo_recto">
                            <label for="q2_c">todo recto</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_d" name="q2" value="enfrente">
                            <label for="q2_d">enfrente</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 3 -->
                <div class="question-container">
                    <div class="question-text">3. What would you ask for when you need a room for two people at a hotel?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q3_a" name="q3" value="habitacion_individual">
                            <label for="q3_a">una habitación individual</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_b" name="q3" value="habitacion_doble">
                            <label for="q3_b">una habitación doble</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_c" name="q3" value="suite">
                            <label for="q3_c">una suite</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_d" name="q3" value="apartamento">
                            <label for="q3_d">un apartamento</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 4 -->
                <div class="question-container">
                    <div class="question-text">4. What is the Spanish word for "museum"?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q4_a" name="q4" value="galeria">
                            <label for="q4_a">galería</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_b" name="q4" value="castillo">
                            <label for="q4_b">castillo</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_c" name="q4" value="museo">
                            <label for="q4_c">museo</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_d" name="q4" value="teatro">
                            <label for="q4_d">teatro</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 5 -->
                <div class="question-container">
                    <div class="question-text">5. Which document do you need to travel internationally?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q5_a" name="q5" value="documento_identidad">
                            <label for="q5_a">documento de identidad</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_b" name="q5" value="permiso_conducir">
                            <label for="q5_b">permiso de conducir</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_c" name="q5" value="pasaporte">
                            <label for="q5_c">pasaporte</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_d" name="q5" value="tarjeta_credito">
                            <label for="q5_d">tarjeta de crédito</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 6 -->
                <div class="question-container">
                    <div class="question-text">6. How do you say "I am lost" in Spanish?</div>
                    <div class="options-container">
                        <input type="text" name="q6" class="text-input" placeholder="Type your answer in Spanish">
                    </div>
                </div>
                
                <!-- Question 7 -->
                <div class="question-container">
                    <div class="question-text">7. What is the Spanish word for "airplane"?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q7_a" name="q7" value="avion">
                            <label for="q7_a">avión</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_b" name="q7" value="tren">
                            <label for="q7_b">tren</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_c" name="q7" value="barco">
                            <label for="q7_c">barco</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_d" name="q7" value="coche">
                            <label for="q7_d">coche</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 8 -->
                <div class="question-container">
                    <div class="question-text">8. How do you ask "What is the price?" in Spanish?</div>
                    <div class="options-container">
                        <input type="text" name="q8" class="text-input" placeholder="Type your answer in Spanish">
                    </div>
                </div>
                
                <!-- Question 9 -->
                <div class="question-container">
                    <div class="question-text">9. Which of these words can be used for "map" in Spanish? (Select all that apply)</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="checkbox" id="q9_a" name="q9[]" value="mapa">
                            <label for="q9_a">mapa</label>
                        </div>
                        <div class="option">
                            <input type="checkbox" id="q9_b" name="q9[]" value="plano">
                            <label for="q9_b">plano</label>
                        </div>
                        <div class="option">
                            <input type="checkbox" id="q9_c" name="q9[]" value="imagen">
                            <label for="q9_c">imagen</label>
                        </div>
                        <div class="option">
                            <input type="checkbox" id="q9_d" name="q9[]" value="foto">
                            <label for="q9_d">foto</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 10 -->
                <div class="question-container">
                    <div class="question-text">10. What is the Spanish word for "delay" (as in a train or flight delay)?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q10_a" name="q10" value="retraso">
                            <label for="q10_a">retraso</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_b" name="q10" value="espera">
                            <label for="q10_b">espera</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_c" name="q10" value="cancelacion">
                            <label for="q10_c">cancelación</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_d" name="q10" value="problema">
                            <label for="q10_d">problema</label>
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
                    Continue Learning <i class='bx bx-right-arrow-alt'></i>
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
