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
    <title>Italian Practice Exercises - Esperanto</title>
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
        
        .category-label {
            display: inline-block;
            background-color: #f8f5ff;
            color: #7F57F1;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .context-hint {
            font-style: italic;
            color: #666;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        .question-text {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .italian-phrase {
            color: #7F57F1;
            font-weight: 600;
            font-style: italic;
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
            <a href="italian.php">Italian</a> &gt; 
            <span>Practice Exercises</span>
        </div>
        
        <h1>Italian Practice Exercises</h1>
        
        <div class="question-container">
            <div class="question-header">
                <h2>Test Your Knowledge</h2>
                <span id="questionCounter" class="question-counter">Question 1 / 10</span>
            </div>
            
            <div id="questionArea"></div>
            
            <div class="navigation-buttons">
                <a href="italian.php" class="btn btn-secondary">
                    <i class='bx bx-arrow-back'></i> Back to Italian
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
<<<<<<< HEAD
            { 
                category: "Food & Dining",
                question: "What does 'caffè' mean in Italian?", 
                word: "Coffee", 
                image: "https://img.icons8.com/dusk/64/coffee.png", 
                options: ["Tea", "Coffee", "Water", "Juice"], 
                answer: "Coffee",
                context: "Common at Italian breakfast or after meals"
            },
            { 
                category: "Travel",
                question: "Which Italian word means 'train'?", 
                word: "Train", 
                image: "https://img.icons8.com/dusk/64/train.png", 
                options: ["Treno", "Autobus", "Aereo", "Nave"], 
                answer: "Treno",
                context: "Important for getting around Italy's cities"
            },
            { 
                category: "Daily Life",
                question: "What does 'casa' mean?", 
                word: "House", 
                image: "https://img.icons8.com/dusk/64/home.png", 
                options: ["Hotel", "Office", "House", "School"], 
                answer: "House",
                context: "Where Italians keep their families and kitchens!"
            },
            { 
                category: "Greetings",
                question: "Which is the correct greeting for morning in Italian?", 
                word: "Good morning", 
                image: "https://img.icons8.com/dusk/64/sun.png", 
                options: ["Buonasera", "Buongiorno", "Ciao", "Arrivederci"], 
                answer: "Buongiorno",
                context: "Used until early afternoon in Italy"
            },
            { 
                category: "Numbers",
                question: "What is the Italian word for 'twenty'?", 
                word: "20", 
                image: "https://img.icons8.com/dusk/64/20.png", 
                options: ["Dieci", "Venti", "Trenta", "Quindici"], 
                answer: "Venti",
                context: "Also the size of a large coffee at Italian-inspired cafés"
            },
            { 
                category: "Shopping",
                question: "How would you say 'How much does it cost?' in Italian?", 
                word: "Price inquiry", 
                image: "https://img.icons8.com/dusk/64/euro-pound-exchange.png", 
                options: ["Dove si trova?", "Quanto costa?", "A che ora?", "Come si dice?"], 
                answer: "Quanto costa?",
                context: "Essential phrase when shopping in Italian markets"
            },
            { 
                category: "Food & Dining",
                question: "Which of these is NOT a traditional Italian pasta?", 
                word: "Pasta types", 
                image: "https://img.icons8.com/dusk/64/spaghetti.png", 
                options: ["Spaghetti", "Rigatoni", "Chow mein", "Penne"], 
                answer: "Chow mein",
                context: "Italy has hundreds of pasta shapes and varieties"
            },
            { 
                category: "Travel",
                question: "What does 'stazione' mean in English?", 
                word: "Station", 
                image: "https://img.icons8.com/dusk/64/railway-station.png", 
                options: ["Hotel", "Restaurant", "Station", "Airport"], 
                answer: "Station",
                context: "Where you catch trains in Italy"
            },
            { 
                category: "Daily Life",
                question: "What does 'dormire' mean?", 
                word: "Sleep", 
                image: "https://img.icons8.com/dusk/64/sleeping.png", 
                options: ["To eat", "To drink", "To sleep", "To run"], 
                answer: "To sleep",
                context: "Essential after a day of Italian sightseeing"
            },
            { 
                category: "Colors",
                question: "What color is 'rosso' in Italian?", 
                word: "Red", 
                image: "https://img.icons8.com/dusk/64/color-palette.png", 
                options: ["Green", "Red", "Blue", "Yellow"], 
                answer: "Red",
                context: "Color of the Italian flag with green and white"
            }
        ];

=======
        { question: "What does this word mean in Italian?", word: "Dog", image: "https://img.icons8.com/dusk/64/dog.png", options: ["Gatto", "Cane", "Uccello", "Pesce"], answer: "Cane" },
        { question: "What does this word mean in Italian?", word: "Sun", image: "https://img.icons8.com/dusk/64/sun.png", options: ["Luna", "Sole", "Stella", "Cielo"], answer: "Sole" },
        { question: "What does this word mean in Italian?", word: "Chair", image: "https://img.icons8.com/stickers/100/chair.png", options: ["Tavolo", "Letto", "Sedia", "Divano"], answer: "Sedia" },
        { question: "What does this word mean in Italian?", word: "Window", image: "https://img.icons8.com/dusk/100/closed-window.png", options: ["Finestra", "Porta", "Specchio", "Muro"], answer: "Finestra" },
        { question: "What does this word mean in Italian?", word: "Water", image: "https://img.icons8.com/dusk/64/water.png", options: ["Acqua", "Latte", "Succo", "Vino"], answer: "Acqua" },
        ];


        ///done
>>>>>>> c25d3ba194636ba6443d0305859bdca08b558f19
        let fillQuestions = [
            { 
                category: "Present Tense Verbs",
                question: "Complete this sentence: Io ___ italiano. (I speak Italian)", 
                answer: "parlo",
                context: "From the verb 'parlare' (to speak)"
            },
            { 
                category: "Daily Routines",
                question: "Marco ___ la colazione alle otto. (Marco has breakfast at eight)", 
                answer: "fa",
                context: "From the verb 'fare' (to do/make)"
            },
            { 
                category: "Travel",
                question: "Noi ___ in Italia la prossima estate. (We are going to Italy next summer)", 
                answer: "andiamo",
                context: "From the verb 'andare' (to go)"
            },
            { 
                category: "Restaurant Phrases",
                question: "Vorrei ___ un tavolo per due persone. (I would like to reserve a table for two people)", 
                answer: "prenotare",
                context: "Useful when making restaurant reservations"
            },
            { 
                category: "Weather",
                question: "Oggi ___ molto caldo. (Today it is very hot)", 
                answer: "fa",
                context: "Weather expressions often use 'fare'"
            },
            { 
                category: "Shopping",
                question: "Quanto ___ questi pantaloni? (How much do these pants cost?)", 
                answer: "costano",
                context: "From the verb 'costare' (to cost)"
            },
            { 
                category: "Introductions",
                question: "Piacere di ___. (Nice to meet you.)", 
                answer: "conoscerti",
                context: "Common phrase when meeting someone"
            },
            { 
                category: "Time Expressions",
                question: "A che ora ___ il museo? (At what time does the museum open?)", 
                answer: "apre",
                context: "From the verb 'aprire' (to open)"
            },
            { 
                category: "Directions",
                question: "Mi scusi, dove ___ la stazione? (Excuse me, where is the station?)", 
                answer: "è",
                context: "Using 'essere' for locations"
            },
            { 
                category: "Ordering Food",
                question: "Io ___ una pizza margherita, per favore. (I would like a margherita pizza, please.)", 
                answer: "vorrei",
                context: "Polite form of requesting in restaurants"
            }
        ];

        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        vocabQuestions = shuffleArray(vocabQuestions);
        fillQuestions = shuffleArray(fillQuestions);

        function loadQuestion() {
            const questionArea = document.getElementById("questionArea");
            const counter = document.getElementById("questionCounter");
            counter.textContent = `Question ${currentQuestion} / ${totalQuestions}`;
            answered = false;

            let q;
            if (currentQuestion <= 6) {
                if (vocabQuestions.length > 0) {
                    q = vocabQuestions.pop();
                    renderVocabQuestion(q);
                }
            } else {
                if (fillQuestions.length > 0) {
                    q = fillQuestions.pop();
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
                <span class="category-label">${q.category}</span>
                <p class="question-text">${q.question}</p>
                ${q.context ? `<p class="context-hint">${q.context}</p>` : ''}
                <div class="vocab-display">
                    <img src="${q.image}" alt="${q.word}" class="vocab-image">
                    ${q.word ? `<h3 class="vocab-word">${q.word}</h3>` : ''}
                </div>
                <div class="options-grid">
                    ${shuffleArray([...q.options]).map(option => `
                        <button class="option-btn" onclick="checkAnswerVocab('${option}', '${q.answer}')">${option}</button>
                    `).join('')}
                </div>
            `;
        }

        function renderFillQuestion(q) {
            const parts = q.question.split('___');
            
            document.getElementById("questionArea").innerHTML = `
                <span class="category-label">${q.category}</span>
                <p class="question-text">Fill in the blank:</p>
                <p class="italian-phrase">${parts[0]}<span class="blank">_____</span>${parts[1]}</p>
                ${q.context ? `<p class="context-hint">${q.context}</p>` : ''}
                <input type="text" id="fillInput" class="fill-input" placeholder="Type your answer here..." autofocus>
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
                document.getElementById("fillInput").focus();
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

            if (selected === correct) {
                score++;
                const feedbackDiv = document.createElement('div');
                feedbackDiv.style.marginTop = '15px';
                feedbackDiv.style.color = '#2e7d32';
                feedbackDiv.style.fontWeight = 'bold';
                feedbackDiv.textContent = 'Correct! Ottimo lavoro!';
                document.getElementById("questionArea").appendChild(feedbackDiv);
            } else {
                const feedbackDiv = document.createElement('div');
                feedbackDiv.style.marginTop = '15px';
                feedbackDiv.style.color = '#c62828';
                feedbackDiv.innerHTML = `Not quite. The correct answer is: <strong>${correct}</strong>`;
                document.getElementById("questionArea").appendChild(feedbackDiv);
            }
            
            updateNextButton();
        }

        function checkAnswerFill(correct) {
            if (answered) return;
            answered = true;

            const input = document.getElementById("fillInput");
            const value = input.value.trim().toLowerCase();
            const correctAnswer = correct.toLowerCase();

            const acceptableAnswers = [correctAnswer];
            if (correctAnswer === "è") {
                acceptableAnswers.push("e");
            }

            let isCorrect = acceptableAnswers.includes(value);

            if (isCorrect) {
                score++;
                input.classList.add('correct');
                
                const feedbackDiv = document.createElement('div');
                feedbackDiv.style.marginTop = '10px';
                feedbackDiv.style.color = '#2e7d32';
                feedbackDiv.style.fontWeight = 'bold';
                feedbackDiv.textContent = 'Correct! Ottimo lavoro!';
                input.parentNode.insertBefore(feedbackDiv, input.nextSibling);
            } else {
                input.classList.add('incorrect');
                
                const feedbackDiv = document.createElement('div');
                feedbackDiv.style.marginTop = '10px';
                feedbackDiv.style.color = '#c62828';
                feedbackDiv.innerHTML = `The correct answer is: <strong>${correct}</strong>`;
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
            let emoji = "";
            
            if (percentage >= 90) {
                rating = "Eccellente! You have a strong grasp of Italian.";
                emoji = "🏆";
            } else if (percentage >= 70) {
                rating = "Molto bene! You're making great progress in Italian.";
                emoji = "👏";
            } else if (percentage >= 50) {
                rating = "Non male! Keep practicing to improve your Italian skills.";
                emoji = "👍";
            } else {
                rating = "Continua a studiare! You might need more practice with Italian fundamentals.";
                emoji = "📚";
            }
            
            document.getElementById("questionArea").innerHTML = `
                <div class="results-container">
                    <h2 class="results-title">Exercise Complete ${emoji}</h2>
                    <p class="results-score">You scored <strong>${score}</strong> out of <strong>${totalQuestions}</strong> (${Math.round(percentage)}%)</p>
                    <p class="results-rating">${rating}</p>
                    
                    <div style="margin-top: 30px;">
                        <button onclick="location.reload()" class="btn btn-primary">Try Again</button>
                    </div>
                </div>
            `;
            
            document.getElementById("questionCounter").style.display = "none";
            document.getElementById("nextButton").style.display = "none";
            
            const activity_details = {
                'exercise_type': 'italian_practice',
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
