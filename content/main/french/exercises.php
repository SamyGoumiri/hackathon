<?php
session_start();
require_once "../../../database/connect.php";

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

$log_query = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
             VALUES (?, 'practice_exercise', ?)";
$details = json_encode(['language' => 'french', 'type' => 'mixed']);
$stmt = $conn->prepare($log_query);
$stmt->bind_param("is", $user_id, $details);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../style.css">
    <title>Esperanto - French Practice Exercises</title>
    <style>
        .exercise-container {
            background-color: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(127, 87, 241, 0.1);
            margin-bottom: 30px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .question-counter {
            color: #7F57F1;
            font-weight: 600;
        }
        
        .question-text {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        
        .word-display {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .word-display img {
            width: 100px;
            height: 100px;
            margin-bottom: 10px;
        }
        
        .word-display h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #7F57F1;
        }
        
        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .option-btn {
            background-color: #f8f5ff;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 15px;
            font-size: 1rem;
            font-weight: 500;
            color: #333;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }
        
        .option-btn:hover {
            background-color: #eee6ff;
            border-color: #7F57F1;
        }
        
        .option-btn.correct {
            background-color: #e6f7e6;
            border-color: #2e7d32;
            color: #2e7d32;
        }
        
        .option-btn.incorrect {
            background-color: #fbe9e7;
            border-color: #d84315;
            color: #d84315;
        }
        
        .fill-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            margin-bottom: 15px;
            transition: border 0.3s ease;
        }
        
        .fill-input:focus {
            border-color: #7F57F1;
            outline: none;
        }
        
        .check-button {
            background-color: #7F57F1;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .check-button:hover {
            background-color: #6642d1;
        }
        
        .results {
            text-align: center;
            padding: 20px;
        }
        
        .results h2 {
            color: #7F57F1;
            margin-bottom: 15px;
        }
        
        .results p {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        
        .results .score {
            font-size: 2rem;
            font-weight: 700;
            color: #7F57F1;
            margin: 15px 0;
        }
        
        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .match-pairs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 20px;
            margin-bottom: 25px;
        }
        
        .pair-item {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            background-color: #f8f5ff;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s ease;
        }
        
        .pair-item:hover {
            background-color: #eee6ff;
        }
        
        .pair-item.selected {
            background-color: #7F57F1;
            color: white;
        }
        
        .pair-item.matched {
            background-color: #e6f7e6;
            border-color: #2e7d32;
            color: #2e7d32;
            cursor: default;
        }
        
        .pronunciation-section audio {
            width: 100%;
            margin: 10px 0 20px;
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
        <h1>French Practice Exercises <img src="https://flagcdn.com/w40/fr.png" alt="French Flag" class="flag-icon"></h1>
        
        <div class="exercise-container">
            <div class="question-header">
                <h2>Test Your Knowledge</h2>
                <span id="questionCounter" class="question-counter">Question 1/10</span>
            </div>
            
            <div id="questionArea"></div>
            
            <div class="navigation-buttons">
                <a href="french.php" class="btn btn-secondary">
                    <i class='bx bx-arrow-back'></i> Back to French
                </a>
                <button id="nextButton" class="btn btn-primary">Next Question</button>
            </div>
        </div>
    </div>

    <script>
        let currentQuestion = 1;
        let score = 0;
        const totalQuestions = 10;
        let answered = false;

        let vocabQuestions = [
            { 
                question: "What does 'Bonjour' mean in English?", 
                word: "Bonjour", 
                image: "https://img.icons8.com/fluency/100/waving-hand-light-skin-tone.png", 
                options: ["Hello", "Goodbye", "Thank you", "Please"], 
                answer: "Hello" 
            },
            { 
                question: "What is the French word for 'I'?", 
                word: "Je", 
                image: "https://img.icons8.com/ios-filled/100/i-pronoun.png", 
                options: ["Je", "Tu", "Il", "Nous"], 
                answer: "Je" 
            },
            {
                question: "How do you say 'the boy' in French?",
                word: "The boy", 
                image: "https://img.icons8.com/fluency/100/child.png", 
                options: ["Le garçon", "La fille", "L'homme", "Le chien"], 
                answer: "Le garçon"
            },
            {
                question: "What is the French word for 'apple'?",
                word: "Apple", 
                image: "https://img.icons8.com/fluency/100/apple.png", 
                options: ["Orange", "Pomme", "Banane", "Fraise"], 
                answer: "Pomme"
            },
            {
                question: "What does 'Merci beaucoup' mean?",
                word: "Merci beaucoup", 
                image: "https://img.icons8.com/fluency/100/handshake.png", 
                options: ["Please wait", "Thank you very much", "Excuse me", "You're welcome"], 
                answer: "Thank you very much"
            },
            {
                question: "What is the French word for 'water'?",
                word: "Water", 
                image: "https://img.icons8.com/fluency/100/water.png", 
                options: ["L'eau", "Le pain", "Le lait", "Le vin"], 
                answer: "L'eau"
            },
            {
                question: "How do you say 'good night' in French?",
                word: "Good night", 
                image: "https://img.icons8.com/fluency/100/sleeping.png", 
                options: ["Bonjour", "Bonsoir", "Bonne nuit", "Au revoir"], 
                answer: "Bonne nuit"
            },
            {
                question: "What does 'Comment allez-vous?' mean?",
                word: "Comment allez-vous?", 
                image: "https://img.icons8.com/fluency/100/question-mark.png", 
                options: ["What is your name?", "How are you?", "Where are you going?", "What time is it?"], 
                answer: "How are you?"
            }
        ];

        let fillQuestions = [
            { 
                question: "Fill in the blank: Je ____ français. (I am French)", 
                answer: "suis" 
            },
            { 
                question: "Fill in the blank: Nous ____ à l'école. (We are at school)", 
                answer: "sommes" 
            },
            { 
                question: "Fill in the blank: Tu ____ une pizza. (You are eating a pizza)", 
                answer: "manges" 
            },
            { 
                question: "Fill in the blank: Elle ____ une chanson. (She sings a song)", 
                answer: "chante" 
            },
            { 
                question: "Fill in the blank: Ils ____ en voiture. (They are traveling by car)", 
                answer: "voyagent" 
            },
            { 
                question: "Fill in the blank: J'____ un livre. (I have a book)", 
                answer: "ai" 
            },
            { 
                question: "Fill in the blank: Vous ____ du café? (Do you want some coffee?)", 
                answer: "voulez" 
            },
            { 
                question: "Fill in the blank: Elles ____ à la plage. (They go to the beach)", 
                answer: "vont" 
            }
        ];
        
        let matchingQuestions = [
            {
                question: "Match the French words with their English translations:",
                pairs: [
                    {french: "Chien", english: "Dog"},
                    {french: "Chat", english: "Cat"},
                    {french: "Maison", english: "House"},
                    {french: "Voiture", english: "Car"}
                ]
            },
            {
                question: "Match the French verbs with their meanings:",
                pairs: [
                    {french: "Manger", english: "To eat"},
                    {french: "Dormir", english: "To sleep"},
                    {french: "Parler", english: "To speak"},
                    {french: "Courir", english: "To run"}
                ]
            }
        ];
        
        let listeningQuestions = [
            {
                question: "Choose the correct phrase that this would translate to:",
                audioSrc: null, // Remove non-existent audio reference
                options: [
                    "Bonjour, comment allez-vous?",
                    "Bonsoir, où allez-vous?",
                    "Bonjour, quand allez-vous partir?",
                    "Bonsoir, comment vous appelez-vous?"
                ],
                answer: "Bonjour, comment allez-vous?"
            }
        ];

        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        function loadQuestion() {
            const questionArea = document.getElementById("questionArea");
            const counter = document.getElementById("questionCounter");
            counter.textContent = `Question ${currentQuestion}/${totalQuestions}`;
            answered = false;

            let questionTypes = [];
            if (vocabQuestions.length > 0) questionTypes.push("vocab");
            if (fillQuestions.length > 0) questionTypes.push("fill");
            if (matchingQuestions.length > 0 && currentQuestion > 2) questionTypes.push("matching");
            
            if (questionTypes.length === 0) {
                showResults();
                return;
            }

            const questionType = questionTypes[Math.floor(Math.random() * questionTypes.length)];
            
            switch(questionType) {
                case "vocab":
                    const vocabIndex = Math.floor(Math.random() * vocabQuestions.length);
                    const vocabQ = vocabQuestions.splice(vocabIndex, 1)[0];
                    renderVocabQuestion(vocabQ);
                    break;
                case "fill":
                    const fillIndex = Math.floor(Math.random() * fillQuestions.length);
                    const fillQ = fillQuestions.splice(fillIndex, 1)[0];
                    renderFillQuestion(fillQ);
                    break;
                case "matching":
                    const matchIndex = Math.floor(Math.random() * matchingQuestions.length);
                    const matchQ = matchingQuestions.splice(matchIndex, 1)[0];
                    renderMatchingQuestion(matchQ);
                    break;
            }

            document.getElementById("nextButton").textContent = currentQuestion < totalQuestions ? "Next Question" : "Finish Quiz";
        }

        function renderVocabQuestion(q) {
            document.getElementById("questionArea").innerHTML = `
                <p class="question-text">${q.question}</p>
                <div class="word-display">
                    <img src="${q.image}" alt="${q.word}" onerror="this.src='https://img.icons8.com/ios/100/question-mark.png'">
                    <h3>${q.word}</h3>
                </div>
                <div class="options-grid" id="options-container">
                </div>
            `;
            
            // Create option buttons using DOM manipulation instead of string interpolation
            const optionsContainer = document.getElementById("options-container");
            shuffleArray([...q.options]).forEach(option => {
                const button = document.createElement('button');
                button.className = 'option-btn';
                button.textContent = option;
                button.addEventListener('click', function() {
                    checkAnswerVocab(option, q.answer);
                });
                optionsContainer.appendChild(button);
            });
        }

        function renderFillQuestion(q) {
            document.getElementById("questionArea").innerHTML = `
                <p class="question-text">${q.question}</p>
                <input type="text" id="fillInput" class="fill-input" placeholder="Type your answer here...">
                <button onclick='checkAnswerFill("${q.answer}")' class="check-button">Check Answer</button>
            `;

            setTimeout(() => {
                document.getElementById("fillInput").addEventListener("keypress", function(event) {
                    if (event.key === "Enter") {
                        event.preventDefault();
                        checkAnswerFill(q.answer);
                    }
                });
                document.getElementById("fillInput").focus();
            }, 0);
        }
        
        function renderMatchingQuestion(q) {
            const shuffledFrench = shuffleArray([...q.pairs.map(p => p.french)]);
            const shuffledEnglish = shuffleArray([...q.pairs.map(p => p.english)]);
            
            document.getElementById("questionArea").innerHTML = `
                <p class="question-text">${q.question}</p>
                <div class="match-pairs" id="matching-container">
                    ${shuffledFrench.map(term => `
                        <div class="pair-item french-term" data-term="${term}">${term}</div>
                    `).join('')}
                    ${shuffledEnglish.map(term => `
                        <div class="pair-item english-term" data-term="${term}">${term}</div>
                    `).join('')}
                </div>
                <div id="match-feedback" style="margin-top: 15px; text-align: center;"></div>
            `;
            
            let selectedItem = null;
            let matchedPairs = 0;
            const totalPairs = q.pairs.length;
            
            document.querySelectorAll('.pair-item').forEach(item => {
                item.addEventListener('click', function() {
                    if (this.classList.contains('matched')) return;
                    
                    if (selectedItem) {
                        const isSelectedFrench = selectedItem.classList.contains('french-term');
                        const isThisFrench = this.classList.contains('french-term');
                        
                        // Only allow matching between different types (French to English)
                        if (isSelectedFrench === isThisFrench) {
                            selectedItem.classList.remove('selected');
                            this.classList.add('selected');
                            selectedItem = this;
                            return;
                        }
                        
                        // Get the terms for matching check
                        const frenchTerm = isSelectedFrench ? selectedItem.dataset.term : this.dataset.term;
                        const englishTerm = isSelectedFrench ? this.dataset.term : selectedItem.dataset.term;
                        
                        // Find if there's a match in the pairs
                        let isCorrectMatch = false;
                        for (let i = 0; i < q.pairs.length; i++) {
                            if (q.pairs[i].french === frenchTerm && q.pairs[i].english === englishTerm) {
                                isCorrectMatch = true;
                                break;
                            }
                        }
                        
                        if (isCorrectMatch) {
                            selectedItem.classList.add('matched');
                            this.classList.add('matched');
                            selectedItem.classList.remove('selected');
                            this.classList.remove('selected');
                            
                            matchedPairs++;
                            
                            // Check if all pairs are matched
                            if (matchedPairs === totalPairs) {
                                score++;
                                document.getElementById('match-feedback').innerHTML = 
                                    `<p style="color: #2e7d32; font-weight: bold;">All pairs matched correctly! Well done!</p>`;
                                answered = true;
                            }
                        } else {
                            // Give visual feedback for incorrect match
                            selectedItem.classList.add('incorrect');
                            this.classList.add('incorrect');
                            
                            // Remove incorrect styling after a short delay
                            setTimeout(() => {
                                selectedItem.classList.remove('incorrect', 'selected');
                                this.classList.remove('incorrect', 'selected');
                            }, 800);
                        }
                        
                        selectedItem = null;
                    } else {
                        this.classList.add('selected');
                        selectedItem = this;
                    }
                });
            });
        }

        function checkAnswerVocab(selected, correct) {
            if (answered) return;
            answered = true;

            document.querySelectorAll('.option-btn').forEach(btn => {
                if (btn.textContent === correct) {
                    btn.classList.add('correct');
                } else if (btn.textContent === selected) {
                    btn.classList.add(selected === correct ? 'correct' : 'incorrect');
                }
                btn.disabled = true;
            });

            if (selected === correct) score++;
        }

        function checkAnswerFill(correct) {
            if (answered) return;
            answered = true;

            const input = document.getElementById("fillInput");
            const value = input.value.trim().toLowerCase();
            
            if (value === correct.toLowerCase()) {
                score++;
                input.classList.add('correct');
                input.style.borderColor = "#2e7d32";
            } else {
                input.classList.add('incorrect');
                input.style.borderColor = "#d84315";
                
                const feedbackDiv = document.createElement("div");
                feedbackDiv.innerHTML = `<p style="color: #d84315;">Correct answer: ${correct}</p>`;
                input.parentNode.insertBefore(feedbackDiv, input.nextSibling);
            }

            input.disabled = true;
            document.querySelector('.check-button').disabled = true;
        }

        function handleNextQuestion() {
            // For matching questions, don't require explicit answering,
            // just check if the question has been rendered
            if (!answered && currentQuestion <= totalQuestions && 
                !document.getElementById("questionArea").innerHTML.includes('match-pairs')) {
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
            let feedback;
            
            if (percentage >= 90) {
                feedback = "Excellent! Vous parlez très bien français!";
            } else if (percentage >= 70) {
                feedback = "Très bien! Keep practicing to improve.";
            } else if (percentage >= 50) {
                feedback = "Bien! You're making progress.";
            } else {
                feedback = "Continue practicing to improve your French skills.";
            }
            
            document.getElementById("questionArea").innerHTML = `
                <div class="results">
                    <h2>Quiz Complete!</h2>
                    <div class="score">${score}/${totalQuestions}</div>
                    <p>${feedback}</p>
                </div>
            `;
            
            document.getElementById("questionCounter").style.display = "none";
            document.getElementById("nextButton").style.display = "none";
            
            const payload = {
                user_id: <?php echo $user_id; ?>,
                score: score,
                total: totalQuestions,
                activity_type: 'french_practice'
            };

            fetch("../../../api/log_activity.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    'user_id': <?php echo $user_id; ?>,
                    'activity_type': 'exercise_completion',
                    'activity_details': JSON.stringify({
                        'exercise_type': 'french_practice',
                        'score': score,
                        'total': totalQuestions,
                        'percentage': percentage,
                        'date': new Date().toISOString()
                    })
                })
            })
            .catch(err => {
                console.error("Error saving results:", err);
            });
        }

        document.getElementById("nextButton").addEventListener("click", handleNextQuestion);
        
        document.querySelector('.user-info').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('active');
        });

        window.onload = loadQuestion;
    </script>
</body>
</html>

