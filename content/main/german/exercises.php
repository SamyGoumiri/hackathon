<?php
require_once '../../../database/connect.php';

session_start();

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>German Practice Exercises - Esperanto</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../style.css">
    <style>
        .question-container {
            background-color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(127, 87, 241, 0.1);
        }
        
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .question-counter {
            color: #777;
            font-size: 0.9rem;
        }
        
        .vocab-display {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px 0;
        }
        
        .vocab-image {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
        }
        
        .vocab-word {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        
        .option-btn {
            background-color: white;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 15px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .option-btn:hover {
            background-color: #f8f5ff;
            border-color: #d4c4fc;
        }
        
        .option-btn.correct {
            background-color: #e6f7e6;
            border-color: #2e7d32;
            color: #2e7d32;
        }
        
        .option-btn.incorrect {
            background-color: #fce7e7;
            border-color: #c62828;
            color: #c62828;
        }
        
        .fill-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        
        .fill-input:focus {
            outline: none;
            border-color: #7F57F1;
            box-shadow: 0 0 0 3px rgba(127, 87, 241, 0.2);
        }
        
        .fill-input.correct {
            background-color: #e6f7e6;
            border-color: #2e7d32;
        }
        
        .fill-input.incorrect {
            background-color: #fce7e7;
            border-color: #c62828;
        }
        
        .check-btn {
            background-color: #7F57F1;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .check-btn:hover {
            background-color: #6642d1;
        }
        
        .results-container {
            text-align: center;
        }
        
        .results-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #7F57F1;
        }
        
        .results-score {
            font-size: 1.3rem;
            margin-bottom: 15px;
        }
        
        .results-rating {
            font-size: 1.1rem;
            margin-top: 15px;
            padding: 10px;
            border-radius: 10px;
            background-color: #f8f5ff;
            display: inline-block;
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
        <div class="breadcrumb">
            <a href="german.php">German</a> &gt; 
            <span>Practice Exercises</span>
        </div>
        
        <h1>German Practice Exercises</h1>
        
        <div class="question-container">
            <div class="question-header">
                <h2>Test Your Knowledge</h2>
                <span id="questionCounter" class="question-counter">Question 1 / 10</span>
            </div>
            
            <div id="questionArea"></div>
            
            <div class="navigation-buttons">
                <a href="german.php" class="btn btn-secondary">
                    <i class='bx bx-arrow-back'></i> Back to German
                </a>
                <button id="nextButton" onclick="handleNextQuestion()" class="btn btn-primary">
                    Next <i class='bx bx-right-arrow-alt'></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentQuestion = 1;
        let score = 0;
        const totalQuestions = 10;
        let answered = false;

        let vocabQuestions = [
            { question: "What does this word mean in German?", word: "Train", image: "https://img.icons8.com/dusk/64/train.png", options: ["Auto", "Zug", "Flugzeug", "Fahrrad"], answer: "Zug" },
            { question: "What does this word mean in German?", word: "Passport", image: "https://img.icons8.com/dusk/64/passport.png", options: ["Führerschein", "Reisepass", "Ausweis", "Karte"], answer: "Reisepass" },
            { question: "What does this word mean in German?", word: "Apple", image: "https://img.icons8.com/external-vitaliy-gorbachev-lineal-color-vitaly-gorbachev/60/external-apple-fruit-vitaliy-gorbachev-lineal-color-vitaly-gorbachev.png", options: ["Banane", "Apfel", "Traube", "Orange"], answer: "Apfel" },
<<<<<<< HEAD
            { question: "What does this word mean in German?", word: "Book", image: "https://img.icons8.com/stickers/100/book-1.png", options: ["Buch", "Heft", "Papier", "Stift"], answer: "Buch" },
            { question: "What does this word mean in German?", word: "House", image: "https://img.icons8.com/plasticine/50/cottage.png", options: ["Tür", "Zimmer", "Fenster", "Haus"], answer: "Haus" },
            { question: "What does this word mean in German?", word: "Water", image: "https://img.icons8.com/emoji/48/droplet-emoji.png", options: ["Wasser", "Kaffee", "Tee", "Milch"], answer: "Wasser" },
            { question: "What does this word mean in German?", word: "Car", image: "https://img.icons8.com/color/48/car--v1.png", options: ["Fahrrad", "Auto", "Bus", "Schiff"], answer: "Auto" },
=======
            { question: "What does this word mean in German?", word: "Book", image: "https://img.icons8.com/dusk/100/book.png", options: ["Buch", "Heft", "Papier", "Stift"], answer: "Buch" },
            { question: "What does this word mean in German?", word: "House", image: "https://img.icons8.com/cute-clipart/64/home.png", options: ["Tur", "Zimmer", "Flugzeug", "Haus"], answer: "Haus" },




>>>>>>> c25d3ba194636ba6443d0305859bdca08b558f19
        ];

        let fillQuestions = [
            { question: "Fill in the blank: Ich ___ müde.", answer: "bin" },
            { question: "Fill in the blank: Er ___ nach Hause.", answer: "geht" },
            { question: "Fill in the blank: Ich ___ Fußball.", answer: "spiele" },
            { question: "Fill in the blank: Sie ___ ein Buch.", answer: "liest" },
            { question: "Fill in the blank: Du ___ sehr schnell.", answer: "läufst" },
            { question: "Fill in the blank: Wir ___ in Berlin.", answer: "wohnen" },
            { question: "Fill in the blank: ___ du Deutsch?", answer: "Sprichst" },
            { question: "Fill in the blank: Das Kind ___ einen Apfel.", answer: "isst" }
        ];

        function loadQuestion() {
            const questionArea = document.getElementById("questionArea");
            const counter = document.getElementById("questionCounter");
            counter.textContent = `Question ${currentQuestion} / ${totalQuestions}`;
            answered = false;

            let q;
            if (currentQuestion <= 6) {
                if (vocabQuestions.length > 0) {
                    const index = Math.floor(Math.random() * vocabQuestions.length);
                    q = vocabQuestions[index];
                    vocabQuestions.splice(index, 1);
                    renderVocabQuestion(q);
                }
            } else {
                if (fillQuestions.length > 0) {
                    const index = Math.floor(Math.random() * fillQuestions.length);
                    q = fillQuestions[index];
                    fillQuestions.splice(index, 1);
                    renderFillQuestion(q);
                }
            }
            
            if (!q) {
                questionArea.innerHTML = "<p class='text-center'>No more questions available.</p>";
                document.getElementById("nextButton").style.display = "none";
            }
        }

        function renderVocabQuestion(q) {
            document.getElementById("questionArea").innerHTML = `
                <p class="question-text">${q.question}</p>
                <div class="vocab-display">
                    <img src="${q.image}" alt="${q.word}" class="vocab-image">
                    <h3 class="vocab-word">${q.word}</h3>
                </div>
                <div class="options-grid">
                    ${q.options.map(option => `
                        <button class="option-btn" onclick="checkAnswerVocab('${option}', '${q.answer}')">${option}</button>
                    `).join('')}
                </div>
            `;
        }

        function renderFillQuestion(q) {
            document.getElementById("questionArea").innerHTML = `
                <p class="question-text">${q.question}</p>
                <input type="text" id="fillInput" class="fill-input" placeholder="Type your answer here...">
                <div class="navigation-buttons" style="justify-content: flex-end; margin-top: 15px;">
                    <button onclick="checkAnswerFill('${q.answer}')" class="btn btn-primary">Check</button>
                </div>
            `;

            setTimeout(() => {
                document.getElementById("fillInput").addEventListener("keypress", function(event) {
                    if (event.key === "Enter") {
                        event.preventDefault();
                        checkAnswerFill(q.answer);
                    }
                });
            }, 0);
        }

        function checkAnswerVocab(selected, correct) {
            if (answered) return;
            answered = true;

            document.querySelectorAll('.option-btn').forEach(btn => {
                if (btn.textContent === correct) {
                    btn.classList.add('correct');
                } else if (btn.textContent === selected && selected !== correct) {
                    btn.classList.add('incorrect');
                }
                btn.disabled = true;
            });

            if (selected === correct) score++;
            updateNextButton();
        }

        function checkAnswerFill(correct) {
            if (answered) return;
            answered = true;

            const input = document.getElementById("fillInput");
            const value = input.value.trim();

            if (value.toLowerCase() === correct.toLowerCase()) {
                score++;
                input.classList.add('correct');
            } else {
                input.classList.add('incorrect');
                
                const feedbackDiv = document.createElement('div');
                feedbackDiv.style.marginTop = '10px';
                feedbackDiv.style.color = '#c62828';
                feedbackDiv.innerHTML = `Correct answer: <strong>${correct}</strong>`;
                input.parentNode.insertBefore(feedbackDiv, input.nextSibling);
            }

            input.disabled = true;
            document.querySelector('.navigation-buttons button').disabled = true;
            updateNextButton();
        }

        function updateNextButton() {
            document.getElementById("nextButton").textContent = currentQuestion < totalQuestions ? "Next" : "Finish";
        }

        function handleNextQuestion() {
            if (!answered && document.getElementById("questionArea").innerHTML !== "<p class='text-center'>No more questions available.</p>") {
                alert("Please answer the question first.");
                return;
            }

            if (currentQuestion < totalQuestions) {
                currentQuestion++;
                loadQuestion();
            } else {
                showResults();
            }
        }

        function showResults() {
            const percentage = (score / totalQuestions) * 100;
            let rating = "";
            
            if (percentage >= 90) {
                rating = "Excellent! You have a strong grasp of German.";
            } else if (percentage >= 70) {
                rating = "Good job! You're making great progress in German.";
            } else if (percentage >= 50) {
                rating = "Not bad. Keep practicing to improve your German skills.";
            } else {
                rating = "You might need more practice with German fundamentals.";
            }
            
            document.getElementById("questionArea").innerHTML = `
                <div class="results-container">
                    <h2 class="results-title">Exercise Complete</h2>
                    <p class="results-score">You scored <strong>${score}</strong> out of <strong>${totalQuestions}</strong> (${percentage}%)</p>
                    <p class="results-rating">${rating}</p>
                </div>
            `;
            
            document.getElementById("questionCounter").style.display = "none";
            document.getElementById("nextButton").style.display = "none";
            
            const activity_details = {
                'exercise_type': 'german_practice',
                'score': score,
                'total': totalQuestions,
                'percentage': percentage,
                'date': new Date().toISOString()
            };
            
            fetch('../../../api/log_activity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    'user_id': <?php echo $user_id; ?>,
                    'activity_type': 'exercise_completion',
                    'activity_details': JSON.stringify(activity_details)
                })
            });
        }

        window.onload = function() {
            loadQuestion();
            document.querySelector('.user-info').addEventListener('click', function() {
                document.querySelector('.dropdown-menu').classList.toggle('active');
            });
        };
    </script>
</body>
</html>
