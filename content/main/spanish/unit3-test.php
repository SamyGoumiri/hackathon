<?php
session_start();
require_once "../../../database/connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$unit_id = 11;

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
    if (isset($_POST['q1']) && $_POST['q1'] === 'supermercado') {
        $score++;
    }
    
    // Question 2
    if (isset($_POST['q2']) && $_POST['q2'] === 'la_cuenta') {
        $score++;
    }
    
    // Question 3
    if (isset($_POST['q3']) && $_POST['q3'] === 'camisa') {
        $score++;
    }
    
    // Question 4
    if (isset($_POST['q4']) && $_POST['q4'] === 'euro') {
        $score++;
    }
    
    // Question 5
    if (isset($_POST['q5']) && $_POST['q5'] === 'barato') {
        $score++;
    }
    
    // Question 6
    if (isset($_POST['q6']) && strtolower(trim($_POST['q6'])) === 'cuánto cuesta esto') {
        $score++;
    }
    
    // Question 7
    if (isset($_POST['q7']) && $_POST['q7'] === 'verduras') {
        $score++;
    }
    
    // Question 8
    if (isset($_POST['q8']) && strtolower(trim($_POST['q8'])) === 'tarjeta de crédito') {
        $score++;
    }
    
    // Question 9
    $q9_answer = isset($_POST['q9']) ? $_POST['q9'] : [];
    if (
        in_array('menu', $q9_answer) && 
        in_array('entrada', $q9_answer) && 
        in_array('plato_principal', $q9_answer) && 
        count($q9_answer) === 3
    ) {
        $score++;
    }
    
    // Question 10
    if (isset($_POST['q10']) && $_POST['q10'] === 'quisiera_probarme') {
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

    <title>Esperanto - Unit 3 Test: Las Compras</title>
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
                    <li><a href="../games.php">Games</a></li>
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
        <h1>Unit 3 Test: Las Compras (Shopping)</h1>
        
        <?php if ($test_submitted): ?>
        <div class="result-container">
            <h2>Test Results</h2>
            <div class="score-display"><?php echo $score; ?> / 10 points (<?php echo round($percentage_score); ?>%)</div>
            
            <?php if ($percentage_score >= 80): ?>
                <div class="result-message success">Excellent! You've mastered shopping vocabulary and expressions in Spanish.</div>
            <?php elseif ($percentage_score >= 60): ?>
                <div class="result-message neutral">Good job! You have a solid understanding of shopping in Spanish, but there's still room for improvement.</div>
            <?php else: ?>
                <div class="result-message failure">You might need more practice with shopping vocabulary. Consider reviewing the lessons again.</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="test-container">
            <?php if (!$test_submitted): ?>
            <p>This test will assess your knowledge of shopping vocabulary, restaurant interactions, clothing terms, and handling money in Spanish.</p>
            <p>Answer all questions to the best of your ability.</p>
            
            <form method="post" action="">
                <!-- Question 1 -->
                <div class="question-container">
                    <div class="question-text">1. What is the Spanish word for "supermarket"?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q1_a" name="q1" value="mercado">
                            <label for="q1_a">mercado</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_b" name="q1" value="supermercado">
                            <label for="q1_b">supermercado</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_c" name="q1" value="tienda">
                            <label for="q1_c">tienda</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_d" name="q1" value="tienda_de_comestibles">
                            <label for="q1_d">tienda de comestibles</label>
                        </div>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="question-container">
                    <div class="question-text">2. What do you ask for at a restaurant when you want to pay?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q2_a" name="q2" value="la_cuenta">
                            <label for="q2_a">la cuenta</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_b" name="q2" value="la_factura">
                            <label for="q2_b">la factura</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_c" name="q2" value="el_precio">
                            <label for="q2_c">el precio</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_d" name="q2" value="el_pago">
                            <label for="q2_d">el pago</label>
                        </div>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="question-container">
                    <div class="question-text">3. Which word means "shirt" in Spanish?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q3_a" name="q3" value="pantalon">
                            <label for="q3_a">pantalón</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_b" name="q3" value="zapatos">
                            <label for="q3_b">zapatos</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_c" name="q3" value="camisa">
                            <label for="q3_c">camisa</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_d" name="q3" value="sombrero">
                            <label for="q3_d">sombrero</label>
                        </div>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="question-container">
                    <div class="question-text">4. What is the currency used in Spain?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q4_a" name="q4" value="franco">
                            <label for="q4_a">franco</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_b" name="q4" value="euro">
                            <label for="q4_b">euro</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_c" name="q4" value="dolar">
                            <label for="q4_c">dólar</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_d" name="q4" value="libra">
                            <label for="q4_d">libra</label>
                        </div>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="question-container">
                    <div class="question-text">5. How would you say something is "inexpensive" in Spanish?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q5_a" name="q5" value="caro">
                            <label for="q5_a">caro</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_b" name="q5" value="barato">
                            <label for="q5_b">barato</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_c" name="q5" value="costoso">
                            <label for="q5_c">costoso</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_d" name="q5" value="nada_caro">
                            <label for="q5_d">nada caro</label>
                        </div>
                    </div>
                </div>

                <!-- Question 6 -->
                <div class="question-container">
                    <div class="question-text">6. How do you ask "How much does this cost?" in Spanish?</div>
                    <div class="options-container">
                        <input type="text" name="q6" class="text-input" placeholder="Type your answer in Spanish">
                    </div>
                </div>

                <!-- Question 7 -->
                <div class="question-container">
                    <div class="question-text">7. What is the Spanish word for "vegetables"?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q7_a" name="q7" value="frutas">
                            <label for="q7_a">frutas</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_b" name="q7" value="carnes">
                            <label for="q7_b">carnes</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_c" name="q7" value="verduras">
                            <label for="q7_c">verduras</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_d" name="q7" value="pescados">
                            <label for="q7_d">pescados</label>
                        </div>
                    </div>
                </div>

                <!-- Question 8 -->
                <div class="question-container">
                    <div class="question-text">8. What is the Spanish term for "credit card"?</div>
                    <div class="options-container">
                        <input type="text" name="q8" class="text-input" placeholder="Type your answer in Spanish">
                    </div>
                </div>

                <!-- Question 9 -->
                <div class="question-container">
                    <div class="question-text">9. Which of these are typical parts of a Spanish restaurant meal? (Select all that apply)</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="checkbox" id="q9_a" name="q9[]" value="menu">
                            <label for="q9_a">menú</label>
                        </div>
                        <div class="option">
                            <input type="checkbox" id="q9_b" name="q9[]" value="entrada">
                            <label for="q9_b">entrada</label>
                        </div>
                        <div class="option">
                            <input type="checkbox" id="q9_c" name="q9[]" value="plato_principal">
                            <label for="q9_c">plato principal</label>
                        </div>
                        <div class="option">
                            <input type="checkbox" id="q9_d" name="q9[]" value="merienda">
                            <label for="q9_d">merienda</label>
                        </div>
                    </div>
                </div>

                <!-- Question 10 -->
                <div class="question-container">
                    <div class="question-text">10. What phrase would you use to ask to try on clothes?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q10_a" name="q10" value="quisiera_comprar">
                            <label for="q10_a">Quisiera comprar...</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_b" name="q10" value="quisiera_probarme">
                            <label for="q10_b">Quisiera probarme...</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_c" name="q10" value="estoy_buscando">
                            <label for="q10_c">Estoy buscando...</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_d" name="q10" value="necesito">
                            <label for="q10_d">Necesito...</label>
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
