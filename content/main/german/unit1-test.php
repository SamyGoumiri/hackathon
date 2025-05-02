<?php
session_start();
require_once "../../../database/connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$unit_id = 1;

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

//updated the german aswer-checking-logic

$score = 0;
$submitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = true;

    if (!empty($_POST['question1']) && $_POST['question1'] === 'guten_tag') {
        $score++;
    }

    if (!empty($_POST['question2']) && $_POST['question2'] === 'ich_heisse') {
        $score++;
    }

    if (!empty($_POST['question3']) && $_POST['question3'] === 'auf_wiedersehen') {
        $score++;
    }

    if (!empty($_POST['question4']) && $_POST['question4'] === 'angenehm') {
        $score++;
    }

    if (!empty($_POST['question5']) && $_POST['question5'] === 'zwoelf') {
        $score++;
    }

    if (!empty($_POST['question6']) && $_POST['question6'] === 'wie') {
        $score++;
    }

    if (!empty($_POST['question7']) && $_POST['question7'] === 'ich_verstehe_nicht') {
        $score++;
    }

    if (!empty($_POST['question8']) && $_POST['question8'] === 'wo') {
        $score++;
    }

    if (!empty($_POST['question9']) && $_POST['question9'] === 'vielen dank') {
        $score++;
    }

    if (!empty($_POST['question10']) && $_POST['question10'] === 'siebzehn') {
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
    <title>Lango - Unit 1 Test: Die Grundlagen.</title>
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
        <h1>Unit 1 Test: Die Grundlagen (The Basics)</h1>


        
        <?php if ($test_submitted): ?>
        <div class="result-container">
            <h2>Test Results</h2>
            <div class="score-display"><?php echo $score; ?> / 10 points (<?php echo round($percentage_score); ?>%)</div>
            
            <?php if ($percentage_score >= 80): ?>
                <div class="result-message success">Excellent! You've mastered the basics of German.</div>
            <?php elseif ($percentage_score >= 60): ?>
                <div class="result-message neutral">Good job! You have a solid understanding, but could review some concepts.</div>
            <?php else: ?>
                <div class="result-message failure">You might need more practice. Consider reviewing the lessons again.</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="test-container">
            <?php if (!$test_submitted): ?>
            <p>This test will assess your knowledge of basic German greetings, introductions, numbers, and essential phrases.</p>
            <p>Answer all questions to the best of your ability.</p>
    <form method="post" action="">
    <!-- German test all set -->
    <!-- Question 1 -->
    <div class="question-container">
        <div class="question-text">1. Which phrase means "Hello" or "Good day" in German?</div>
        <div class="options-container">
            <div class="option">
                <input type="radio" id="q1_a" name="q1" value="hallo">
                <label for="q1_a">Hallo</label>
            </div>
            <div class="option">
                <input type="radio" id="q1_b" name="q1" value="guten_tag">
                <label for="q1_b">Guten Tag</label>
            </div>
            <div class="option">
                <input type="radio" id="q1_c" name="q1" value="guten_abend">
                <label for="q1_c">Guten Abend</label>
            </div>
            <div class="option">
                <input type="radio" id="q1_d" name="q1" value="auf_wiedersehen">
                <label for="q1_d">Auf Wiedersehen</label>
            </div>
        </div>
    </div>

    <!-- Question 2 -->
    <div class="question-container">
        <div class="question-text">2. Which phrase do you use to introduce yourself in German?</div>
        <div class="options-container">
            <div class="option">
                <input type="radio" id="q2_a" name="q2" value="ich_heisse">
                <label for="q2_a">Ich heiße...</label>
            </div>
            <div class="option">
                <input type="radio" id="q2_b" name="q2" value="wie_heisst_du">
                <label for="q2_b">Wie heißt du?</label>
            </div>
            <div class="option">
                <input type="radio" id="q2_c" name="q2" value="angenehm">
                <label for="q2_c">Angenehm</label>
            </div>
            <div class="option">
                <input type="radio" id="q2_d" name="q2" value="ich_bin_hier">
                <label for="q2_d">Ich bin hier</label>
            </div>
        </div>
    </div>

    <!-- Question 3 -->
    <div class="question-container">
        <div class="question-text">3. How do you say "Goodbye" in German?</div>
        <div class="options-container">
            <div class="option">
                <input type="radio" id="q3_a" name="q3" value="guten_tag">
                <label for="q3_a">Guten Tag</label>
            </div>
            <div class="option">
                <input type="radio" id="q3_b" name="q3" value="danke">
                <label for="q3_b">Danke</label>
            </div>
            <div class="option">
                <input type="radio" id="q3_c" name="q3" value="auf_wiedersehen">
                <label for="q3_c">Auf Wiedersehen</label>
            </div>
            <div class="option">
                <input type="radio" id="q3_d" name="q3" value="hallo">
                <label for="q3_d">Hallo</label>
            </div>
        </div>
    </div>

    <!-- Question 4 -->
    <div class="question-container">
        <div class="question-text">4. What do you say when meeting someone for the first time?</div>
        <div class="options-container">
            <div class="option">
                <input type="radio" id="q4_a" name="q4" value="auf_wiedersehen">
                <label for="q4_a">Auf Wiedersehen</label>
            </div>
            <div class="option">
                <input type="radio" id="q4_b" name="q4" value="angenehm">
                <label for="q4_b">Angenehm</label>
            </div>
            <div class="option">
                <input type="radio" id="q4_c" name="q4" value="bis_bald">
                <label for="q4_c">Bis bald</label>
            </div>
            <div class="option">
                <input type="radio" id="q4_d" name="q4" value="ich_heisse">
                <label for="q4_d">Ich heiße</label>
            </div>
        </div>
    </div>

    <!-- Question 5 -->
    <div class="question-container">
        <div class="question-text">5. What is the German word for the number 12?</div>
        <div class="options-container">
            <div class="option">
                <input type="radio" id="q5_a" name="q5" value="zehn">
                <label for="q5_a">zehn</label>
            </div>
            <div class="option">
                <input type="radio" id="q5_b" name="q5" value="elf">
                <label for="q5_b">elf</label>
            </div>
            <div class="option">
                <input type="radio" id="q5_c" name="q5" value="zwoelf">
                <label for="q5_c">zwölf</label>
            </div>
            <div class="option">
                <input type="radio" id="q5_d" name="q5" value="dreizehn">
                <label for="q5_d">dreizehn</label>
            </div>
        </div>
    </div>

    <!-- Question 6 -->
    <div class="question-container">
        <div class="question-text">6. Which question word means "how" in German?</div>
        <div class="options-container">
            <div class="option">
                <input type="radio" id="q6_a" name="q6" value="wann">
                <label for="q6_a">wann</label>
            </div>
            <div class="option">
                <input type="radio" id="q6_b" name="q6" value="wer">
                <label for="q6_b">wer</label>
            </div>
            <div class="option">
                <input type="radio" id="q6_c" name="q6" value="wie">
                <label for="q6_c">wie</label>
            </div>
            <div class="option">
                <input type="radio" id="q6_d" name="q6" value="warum">
                <label for="q6_d">warum</label>
            </div>
        </div>
    </div>

    <!-- Question 7 -->
    <div class="question-container">
        <div class="question-text">7. Which phrase means "I don't understand" in German?</div>
        <div class="options-container">
            <div class="option">
                <input type="radio" id="q7_a" name="q7" value="ich_verstehe_nicht">
                <label for="q7_a">Ich verstehe nicht</label>
            </div>
            <div class="option">
                <input type="radio" id="q7_b" name="q7" value="sprechen_sie_englisch">
                <label for="q7_b">Sprechen Sie Englisch?</label>
            </div>
            <div class="option">
                <input type="radio" id="q7_c" name="q7" value="koennen_sie_wiederholen">
                <label for="q7_c">Können Sie das wiederholen?</label>
            </div>
            <div class="option">
                <input type="radio" id="q7_d" name="q7" value="entschuldigung">
                <label for="q7_d">Entschuldigung</label>
            </div>
        </div>
    </div>

    <!-- Question 8 -->
    <div class="question-container">
        <div class="question-text">8. Fill in the blank: "_____ ist der Bahnhof?" (Where is the train station?)</div>
        <div class="options-container">
            <input type="text" name="q8" class="text-input" placeholder="Type the missing word">
        </div>
    </div>

    <!-- Question 9 -->
    <div class="question-container">
        <div class="question-text">9. How do you say "Thank you very much" in German?</div>
        <div class="options-container">
            <input type="text" name="q9" class="text-input" placeholder="Type your answer in German">
        </div>
    </div>

    <!-- Question 10 -->
    <div class="question-container">
        <div class="question-text">10. What is the German word for 17?</div>
        <div class="options-container">
            <div class="option">
                <input type="radio" id="q10_a" name="q10" value="sieben">
                <label for="q10_a">sieben</label>
            </div>
            <div class="option">
                <input type="radio" id="q10_b" name="q10" value="siebzehn">
                <label for="q10_b">siebzehn</label>
            </div>
            <div class="option">
                <input type="radio" id="q10_c" name="q10" value="sechzehn">
                <label for="q10_c">sechzehn</label>
            </div>
            <div class="option">
                <input type="radio" id="q10_d" name="q10" value="sieben_zehn">
                <label for="q10_d">sieben zehn</label>
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
