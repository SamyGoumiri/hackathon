<?php
session_start();
require_once "../../../database/connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$unit_id = 4;

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
    if (isset($_POST['q1']) && $_POST['q1'] === 'train') {
        $score++;
    }
    
    // Question 2
    if (isset($_POST['q2']) && $_POST['q2'] === 'a_droite') {
        $score++;
    }
    
    // Question 3
    if (isset($_POST['q3']) && $_POST['q3'] === 'chambre_double') {
        $score++;
    }
    
    // Question 4
    if (isset($_POST['q4']) && $_POST['q4'] === 'musee') {
        $score++;
    }
    
    // Question 5
    if (isset($_POST['q5']) && $_POST['q5'] === 'passeport') {
        $score++;
    }
    
    // Question 6
    if (isset($_POST['q6']) && strtolower(trim($_POST['q6'])) === "je suis perdu") {
        $score++;
    }
    
    // Question 7
    if (isset($_POST['q7']) && $_POST['q7'] === 'avion') {
        $score++;
    }
    
    // Question 8
    if (isset($_POST['q8']) && strtolower(trim($_POST['q8'])) === "quel est le prix") {
        $score++;
    }
    
    // Question 9
    $q9_answers = isset($_POST['q9']) ? $_POST['q9'] : [];
    if (
        in_array('carte', $q9_answers) && 
        in_array('plan', $q9_answers) && 
        count($q9_answers) === 2
    ) {
        $score++;
    }
    
    // Question 10
    if (isset($_POST['q10']) && $_POST['q10'] === 'retard') {
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
    <title>Lango - Unit 4 Test: Les Voyages</title>
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
        <h1>Unit 4 Test: Les Voyages (Traveling)</h1>
        
        <?php if ($test_submitted): ?>
        <div class="result-container">
            <h2>Test Results</h2>
            <div class="score-display"><?php echo $score; ?> / 10 points (<?php echo round($percentage_score); ?>%)</div>
            
            <?php if ($percentage_score >= 80): ?>
                <div class="result-message success">Excellent! You're ready to travel in French-speaking countries.</div>
            <?php elseif ($percentage_score >= 60): ?>
                <div class="result-message neutral">Good job! You know the basics of traveling in French, but there's room for improvement.</div>
            <?php else: ?>
                <div class="result-message failure">You might need more practice with travel vocabulary. Consider reviewing the lessons again.</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="test-container">
            <?php if (!$test_submitted): ?>
            <p>This test will assess your knowledge of transportation vocabulary, directions, hotel reservations, tourist attractions, and dealing with travel problems in French.</p>
            <p>Answer all questions to the best of your ability.</p>
            
            <form method="post" action="">
                <!-- Question 1 -->
                <div class="question-container">
                    <div class="question-text">1. What is the French word for "train"?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q1_a" name="q1" value="bus">
                            <label for="q1_a">bus</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_b" name="q1" value="voiture">
                            <label for="q1_b">voiture</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_c" name="q1" value="train">
                            <label for="q1_c">train</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q1_d" name="q1" value="metro">
                            <label for="q1_d">métro</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 2 -->
                <div class="question-container">
                    <div class="question-text">2. How do you say "to the right" in French when giving directions?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q2_a" name="q2" value="a_gauche">
                            <label for="q2_a">à gauche</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_b" name="q2" value="a_droite">
                            <label for="q2_b">à droite</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_c" name="q2" value="tout_droit">
                            <label for="q2_c">tout droit</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q2_d" name="q2" value="en_face">
                            <label for="q2_d">en face</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 3 -->
                <div class="question-container">
                    <div class="question-text">3. What would you ask for when you need a room for two people at a hotel?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q3_a" name="q3" value="chambre_simple">
                            <label for="q3_a">une chambre simple</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_b" name="q3" value="chambre_double">
                            <label for="q3_b">une chambre double</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_c" name="q3" value="suite">
                            <label for="q3_c">une suite</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q3_d" name="q3" value="appartement">
                            <label for="q3_d">un appartement</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 4 -->
                <div class="question-container">
                    <div class="question-text">4. What is the French word for "museum"?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q4_a" name="q4" value="galerie">
                            <label for="q4_a">galerie</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_b" name="q4" value="chateau">
                            <label for="q4_b">château</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_c" name="q4" value="musee">
                            <label for="q4_c">musée</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q4_d" name="q4" value="theatre">
                            <label for="q4_d">théâtre</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 5 -->
                <div class="question-container">
                    <div class="question-text">5. Which document do you need to travel internationally?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q5_a" name="q5" value="carte_identite">
                            <label for="q5_a">carte d'identité</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_b" name="q5" value="permis_conduire">
                            <label for="q5_b">permis de conduire</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_c" name="q5" value="passeport">
                            <label for="q5_c">passeport</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q5_d" name="q5" value="carte_bancaire">
                            <label for="q5_d">carte bancaire</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 6 -->
                <div class="question-container">
                    <div class="question-text">6. How do you say "I am lost" in French?</div>
                    <div class="options-container">
                        <input type="text" name="q6" class="text-input" placeholder="Type your answer in French">
                    </div>
                </div>
                
                <!-- Question 7 -->
                <div class="question-container">
                    <div class="question-text">7. What is the French word for "airplane"?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q7_a" name="q7" value="avion">
                            <label for="q7_a">avion</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_b" name="q7" value="train">
                            <label for="q7_b">train</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_c" name="q7" value="bateau">
                            <label for="q7_c">bateau</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q7_d" name="q7" value="voiture">
                            <label for="q7_d">voiture</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 8 -->
                <div class="question-container">
                    <div class="question-text">8. How do you ask "What is the price?" in French?</div>
                    <div class="options-container">
                        <input type="text" name="q8" class="text-input" placeholder="Type your answer in French">
                    </div>
                </div>
                
                <!-- Question 9 -->
                <div class="question-container">
                    <div class="question-text">9. Which of these words can be used for "map" in French? (Select all that apply)</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="checkbox" id="q9_a" name="q9[]" value="carte">
                            <label for="q9_a">carte</label>
                        </div>
                        <div class="option">
                            <input type="checkbox" id="q9_b" name="q9[]" value="plan">
                            <label for="q9_b">plan</label>
                        </div>
                        <div class="option">
                            <input type="checkbox" id="q9_c" name="q9[]" value="image">
                            <label for="q9_c">image</label>
                        </div>
                        <div class="option">
                            <input type="checkbox" id="q9_d" name="q9[]" value="photo">
                            <label for="q9_d">photo</label>
                        </div>
                    </div>
                </div>
                
                <!-- Question 10 -->
                <div class="question-container">
                    <div class="question-text">10. What is the French word for "delay" (as in a train or flight delay)?</div>
                    <div class="options-container">
                        <div class="option">
                            <input type="radio" id="q10_a" name="q10" value="retard">
                            <label for="q10_a">retard</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_b" name="q10" value="attente">
                            <label for="q10_b">attente</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_c" name="q10" value="annulation">
                            <label for="q10_c">annulation</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="q10_d" name="q10" value="probleme">
                            <label for="q10_d">problème</label>
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
