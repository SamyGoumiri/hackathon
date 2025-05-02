<?php
session_start();
require_once "../../../database/connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$unit_id = 3;

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
    header("Location: course-content.php?unit=" . $unit_id);
    exit();
}

$score = 0;
$test_submitted = false;

if (isset($_POST['submit_test'])) {
    $test_submitted = true;
    $total_questions = 10;
    
    // Question 1
    if (isset($_POST['q1']) && $_POST['q1'] === 'supermarché') {
        $score++;
    }
    
    // Question 2
    if (isset($_POST['q2']) && $_POST['q2'] === 'laddition') {
        $score++;
    }
    
    // Question 3
    if (isset($_POST['q3']) && $_POST['q3'] === 'chemise') {
        $score++;
    }
    
    // Question 4
    if (isset($_POST['q4']) && $_POST['q4'] === 'euro') {
        $score++;
    }
    
    // Question 5
    if (isset($_POST['q5']) && $_POST['q5'] === 'bon_marche') {
        $score++;
    }
    
    // Question 6
    if (isset($_POST['q6']) && strtolower(trim($_POST['q6'])) === 'combien ça coûte') {
        $score++;
    }
    
    // Question 7
    if (isset($_POST['q7']) && $_POST['q7'] === 'legumes') {
        $score++;
    }
    
    // Question 8
    if (isset($_POST['q8']) && strtolower(trim($_POST['q8'])) === 'carte bancaire') {
        $score++;
    }
    
    // Question 9
    $q9_answer = isset($_POST['q9']) ? $_POST['q9'] : [];
    if (
        in_array('menu', $q9_answer) && 
        in_array('entrée', $q9_answer) && 
        in_array('plat_principal', $q9_answer) && 
        count($q9_answer) === 3
    ) {
        $score++;
    }
    
    // Question 10
    if (isset($_POST['q10']) && $_POST['q10'] === 'je_voudrais_essayer') {
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
    <link rel="stylesheet" href="french.css">
    <title>Lango - Unit 3 Test: Faire des Courses</title>
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
        <h1>Unit 3 Test: Faire des Courses (Shopping)</h1>
        
        <?php if ($test_submitted): ?>
        <div class="result-container">
            <h2>Test Results</h2>
            <div class="score-display"><?php echo $score; ?> / 10 points (<?php echo round($percentage_score); ?>%)</div>
            
            <?php if ($percentage_score >= 80): ?>
                <div class="result-message success">Excellent! You've mastered shopping vocabulary and expressions in French.</div>
            <?php elseif ($percentage_score >= 60): ?>
                <div class="result-message neutral">Good job! You have a solid understanding of shopping in French, but there's still room for improvement.</div>
            <?php else: ?>
                <div class="result-message failure">You might need more practice with shopping vocabulary. Consider reviewing the lessons again.</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="test-container">
            <?php if (!$test_submitted): ?>
            <p>This test will assess your knowledge of shopping vocabulary, restaurant interactions, clothing terms, and handling money in French.</p>
            <p>Answer all questions to the best of your ability.</p>
            
            <form method="post" action="">
                <!-- Question 1 -->
                <div class="question-container">
                    <div class="question-text">1. What is the French word for "supermarket"?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q1_a" name="q1" value="marché">
                            <label for="q1_a">marché</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_b" name="q1" value="supermarché">
                            <label for="q1_b">supermarché</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_c" name="q1" value="boutique">
                            <label for="q1_c">boutique</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_d" name="q1" value="épicerie">
                            <label for="q1_d">épicerie</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 2 -->
                <div class="question-container">
                    <div class="question-text">2. What do you ask for at a restaurant when you want to pay?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q2_a" name="q2" value="laddition">
                            <label for="q2_a">l'addition</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_b" name="q2" value="la_facture">
                            <label for="q2_b">la facture</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_c" name="q2" value="le_prix">
                            <label for="q2_c">le prix</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_d" name="q2" value="le_paiement">
                            <label for="q2_d">le paiement</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 3 -->
                <div class="question-container">
                    <div class="question-text">3. Which word means "shirt" in French?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q3_a" name="q3" value="pantalon">
                            <label for="q3_a">pantalon</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_b" name="q3" value="chaussures">
                            <label for="q3_b">chaussures</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_c" name="q3" value="chemise">
                            <label for="q3_c">chemise</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_d" name="q3" value="chapeau">
                            <label for="q3_d">chapeau</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 4 -->
                <div class="question-container">
                    <div class="question-text">4. What is the currency used in France?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q4_a" name="q4" value="franc">
                            <label for="q4_a">franc</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_b" name="q4" value="euro">
                            <label for="q4_b">euro</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_c" name="q4" value="dollar">
                            <label for="q4_c">dollar</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_d" name="q4" value="livre">
                            <label for="q4_d">livre</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 5 -->
                <div class="question-container">
                    <div class="question-text">5. How would you say something is "inexpensive" in French?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q5_a" name="q5" value="cher">
                            <label for="q5_a">cher</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_b" name="q5" value="bon_marche">
                            <label for="q5_b">bon marché</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_c" name="q5" value="coûteux">
                            <label for="q5_c">coûteux</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_d" name="q5" value="pas_cher">
                            <label for="q5_d">pas mal</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 6 -->
                <div class="question-container">
                    <div class="question-text">6. How do you ask "How much does this cost?" in French?</div>
                    <div class="options-container">
                        <input type="text" name="q6" class="text-input" placeholder="Type your answer in French">
                    </div>
                </div>
                
                <!-- Question 7 -->
                <div class="question-container">
                    <div class="question-text">7. What is the French word for "vegetables"?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q7_a" name="q7" value="fruits">
                            <label for="q7_a">fruits</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_b" name="q7" value="viandes">
                            <label for="q7_b">viandes</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_c" name="q7" value="legumes">
                            <label for="q7_c">légumes</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_d" name="q7" value="poissons">
                            <label for="q7_d">poissons</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 8 -->
                <div class="question-container">
                    <div class="question-text">8. What is the French term for "debit card" or "credit card"?</div>
                    <div class="options-container">
                        <input type="text" name="q8" class="text-input" placeholder="Type your answer in French">
                    </div>
                </div>
                
                <!-- Question 9 -->
                <div class="question-container">
                    <div class="question-text">9. Which of these are typical parts of a French restaurant meal? (Select all that apply)</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="checkbox" id="q9_a" name="q9[]" value="menu">
                            <label for="q9_a">menu</label>
                        </div>
                        <div class="option">
                            <input type="checkbox" id="q9_b" name="q9[]" value="entrée">
                            <label for="q9_b">entrée (starter)</label>
                        </div>
                        <div class="option">
                            <input type="checkbox" id="q9_c" name="q9[]" value="plat_principal">
                            <label for="q9_c">plat principal (main course)</label>
                        </div>
                        <div class="option">
                            <input type="checkbox" id="q9_d" name="q9[]" value="goûter">
                            <label for="q9_d">goûter (afternoon snack)</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 10 -->
                <div class="question-container">
                    <div class="question-text">10. What phrase would you use to ask to try on clothes?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q10_a" name="q10" value="je_voudrais_acheter">
                            <label for="q10_a">Je voudrais acheter...</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_b" name="q10" value="je_voudrais_essayer">
                            <label for="q10_b">Je voudrais essayer...</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_c" name="q10" value="je_cherche">
                            <label for="q10_c">Je cherche...</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_d" name="q10" value="jai_besoin">
                            <label for="q10_d">J'ai besoin de...</label>
                        </div>
                    </div>
                </div>
                
                <div class="submit-container">
                    <button type="submit" name="submit_test" class="btn btn-primary">Submit Test</button>
                </div>
            </form>
            <?php else: ?>
            <div class="navigation-buttons">
                <a href="course-content.php?unit=<?php echo $unit_id; ?>" class="btn btn-secondary">
                    <i class='bx bx-arrow-back'></i> Back to Unit
                </a>
                <a href="courses.php" class="btn btn-primary">
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
