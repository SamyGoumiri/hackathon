<?php
session_start();
require_once "../../../database/connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$unit_id = 2;

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


$score = 0;

// Question 1
if (isset($_POST['q1']) && $_POST['q1'] === 'aufwachen') {
    $score++;
}

// Question 2
if (isset($_POST['q2']) && $_POST['q2'] === 'nehmen') {
    $score++;
}

// Question 3
if (isset($_POST['q3']) && $_POST['q3'] === 'Mittag') {
    $score++;
}

// Question 4
if (isset($_POST['q4']) && $_POST['q4'] === 'acht_uhr_viertel_nach') {
    $score++;
}

// Question 5
if (isset($_POST['q5']) && $_POST['q5'] === 'Mittwoch') {
    $score++;
}

// Question 6
if (isset($_POST['q6']) && strtolower(trim($_POST['q6'])) === 'januar') {
    $score++;
}

// Question 7
if (isset($_POST['q7']) && $_POST['q7'] === 'es_ist_heiss') {
    $score++;
}

// Question 8
if (isset($_POST['q8']) && strtolower(trim($_POST['q8'])) === 'es_regnet') {
    $score++;
}

// Question 9
$q9_answer = isset($_POST['q9']) ? $_POST['q9'] : '';
if ($q9_answer === 'der_sommer') {
    $score++;
}

// Question 10
if (isset($_POST['q10']) && $_POST['q10'] === 'ich_bin') {
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../style.css">
    <title>Esperanto - Unit 2 Test: Der Alltag/title>

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
                    <li><a href="../games/games.php">Games</a></li>
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
        <h1>Unit 2 Test: Der Alltag (Daily Life)</h1>
        
        <?php if ($test_submitted): ?>
        <div class="result-container">
            <h2>Test Results</h2>
            <div class="score-display"><?php echo $score; ?> / 10 points (<?php echo round($percentage_score); ?>%)</div>
            
            <?php if ($percentage_score >= 80): ?>
                <div class="result-message success">Excellent! You have a strong grasp of daily life vocabulary in German.</div>
            <?php elseif ($percentage_score >= 60): ?>
                <div class="result-message neutral">Good job! You understand many aspects of daily life in German, but there's room for improvement.</div>
            <?php else: ?>
                <div class="result-message failure">You might need more practice with daily routines and time expressions. Consider reviewing the lessons again.</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="test-container">
            <?php if (!$test_submitted): ?>
            <p>This test will assess your knowledge of daily routines, telling time, days and months, and weather expressions in German.</p>
            <p>Answer all questions to the best of your ability.</p>
            
            <form method="post" action="">
    <!-- Question 1 -->
    <div class="question-container">
        <div class="question-text">1. Which word means "to wake up" in German?</div>
        <div class="options-container">
            <div class="option">
                <input type="radio" id="q1_a" name="q1" value="aufstehen">
                <label for="q1_a">aufstehen</label>
            </div>
            <div class="option">
                <input type="radio" id="q1_b" name="q1" value="aufwachen">
                <label for="q1_b">aufwachen</label>
            </div>
            <div class="option">
                <input type="radio" id="q1_c" name="q1" value="duschen">
                <label for="q1_c">duschen</label>
            </div>
            <div class="option">
                <input type="radio" id="q1_d" name="q1" value="anziehen">
                <label for="q1_d">anziehen</label>
            </div>
        </div>
    </div>
    
    <!-- Question 2 -->
    <div class="question-container">
        <div class="question-text">2. Which verb means "to take" or "to have" (e.g., to have breakfast) in German?</div>
        <div class="options-container">
            <div class="option">
                <input type="radio" id="q2_a" name="q2" value="nehmen">
                <label for="q2_a">nehmen</label>
            </div>
            <div class="option">
                <input type="radio" id="q2_b" name="q2" value="machen">
                <label for="q2_b">machen</label>
            </div>
            <div class="option">
                <input type="radio" id="q2_c" name="q2" value="gehen">
                <label for="q2_c">gehen</label>
            </div>
            <div class="option">
                <input type="radio" id="q2_d" name="q2" value="haben">
                <label for="q2_d">haben</label>
            </div>
        </div>
    </div>
    
    <!-- Question 3 -->
    <div class="question-container">
        <div class="question-text">3. How do you say "noon" in German?</div>
        <div class="options-container">
            <div class="option">
                <input type="radio" id="q3_a" name="q3" value="Mitternacht">
                <label for="q3_a">Mitternacht</label>
            </div>
            <div class="option">
                <input type="radio" id="q3_b" name="q3" value="Morgen">
                <label for="q3_b">Morgen</label>
            </div>
            <div class="option">
                <input type="radio" id="q3_c" name="q3" value="Mittag">
                <label for="q3_c">Mittag</label>
            </div>
            <div class="option">
                <input type="radio" id="q3_d" name="q3" value="Abend">
                <label for="q3_d">Abend</label>
            </div>
        </div>
    </div>
    
    <!-- Question 4 -->
    <div class="question-container">
        <div class="question-text">4. How do you say "8:15" (quarter past eight) in German?</div>
        <div class="options-container">
            <div class="option">
                <input type="radio" id="q4_a" name="q4" value="acht_uhr_viertel_vor">
                <label for="q4_a">acht Uhr viertel vor</label>
            </div>
            <div class="option">
                <input type="radio" id="q4_b" name="q4" value="acht_uhr_viertel_nach">
                <label for="q4_b">acht Uhr viertel nach</label>
            </div>
            <div class="option">
                <input type="radio" id="q4_c" name="q4" value="acht_uhr_fuenfzehn">
                <label for="q4_c">acht Uhr fünfzehn</label>
            </div>
            <div class="option">
                <input type="radio" id="q4_d" name="q4" value="acht_quart">
                <label for="q4_d">acht quart</label>
            </div>
        </div>
    </div>
    
    <!-- Question 5 -->
    <div class="question-container">
        <div class="question-text">5. Which day comes between Tuesday and Thursday?</div>
        <div class="options-container">
            <div class="option">
                <input type="radio" id="q5_a" name="q5" value="Montag">
                <label for="q5_a">Montag</label>
            </div>
            <div class="option">
                <input type="radio" id="q5_b" name="q5" value="Dienstag">
                <label for="q5_b">Dienstag</label>
            </div>
            <div class="option">
                <input type="radio" id="q5_c" name="q5" value="Mittwoch">
                <label for="q5_c">Mittwoch</label>
            </div>
            <div class="option">
                <input type="radio" id="q5_d" name="q5" value="Freitag">
                <label for="q5_d">Freitag</label>
            </div>
        </div>
    </div>
    
    <!-- Question 6 -->
    <div class="question-container">
        <div class="question-text">6. What is the first month of the year in German?</div>
        <div class="options-container">
            <input type="text" name="q6"

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
