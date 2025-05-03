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
    <title>Spanish Practice Exercises - Esperanto</title>
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
        
        .spanish-phrase {
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
            <a href="spanish.php">Spanish</a> &gt; 
            <span>Practice Exercises</span>
        </div>
        
        <h1>Spanish Practice Exercises</h1>
        
        <div class="question-container">
            <div class="question-header">
                <h2>Test Your Knowledge</h2>
                <span id="questionCounter" class="question-counter">Question 1 / 10</span>
            </div>
            
            <div id="questionArea"></div>
            
            <div class="navigation-buttons">
                <a href="spanish.php" class="btn btn-secondary">
                    <i class='bx bx-arrow-back'></i> Back to Spanish
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
            { 
                category: "Greetings",
                question: "What does 'Buenos días' mean?", 
                word: "Buenos días", 
                image: "https://img.icons8.com/dusk/64/sunrise.png", 
                options: ["Good morning", "Good evening", "Good night", "Goodbye"], 
                answer: "Good morning",
                context: "Used during morning hours until around noon"
            },
            { 
                category: "Food & Dining",
                question: "Which Spanish word means 'menu'?", 
                word: "Menu", 
                image: "https://img.icons8.com/dusk/64/restaurant-menu.png", 
                options: ["La carta", "El tenedor", "El plato", "La servilleta"], 
                answer: "La carta",
                context: "What you ask for when dining at a restaurant in Spain"
            },
            { 
                category: "Daily Life",
                question: "What is 'el tiempo' in English?", 
                word: "El tiempo", 
                image: "https://img.icons8.com/dusk/64/clock.png", 
                options: ["Time", "Weather", "Television", "Space"], 
                answer: "Weather",
                context: "Often discussed in casual conversation in Spain"
            },
            { 
                category: "Numbers",
                question: "How do you say 'fifteen' in Spanish?", 
                word: "15", 
                image: "https://img.icons8.com/dusk/64/15.png", 
                options: ["Cinco", "Doce", "Quince", "Diez"], 
                answer: "Quince",
                context: "Essential for shopping and counting"
            },
            { 
                category: "Travel",
                question: "What does 'aeropuerto' mean?", 
                word: "Aeropuerto", 
                image: "https://img.icons8.com/dusk/64/airport.png", 
                options: ["Train station", "Airport", "Bus terminal", "Seaport"], 
                answer: "Airport",
                context: "Where you catch flights in Spanish-speaking countries"
            },
            { 
                category: "Colors",
                question: "Which color is 'morado' in English?", 
                word: "Morado", 
                image: "https://img.icons8.com/dusk/64/color-palette.png", 
                options: ["Red", "Blue", "Green", "Purple"], 
                answer: "Purple",
                context: "A vibrant color often seen in Spanish festivals"
            },
            { 
                category: "Family",
                question: "What is the Spanish word for 'brother'?", 
                word: "Brother", 
                image: "https://img.icons8.com/dusk/64/conference-call.png", 
                options: ["Hermano", "Padre", "Hijo", "Tío"], 
                answer: "Hermano",
                context: "Important family relationship in Hispanic culture"
            },
            { 
                category: "Shopping",
                question: "How would you ask 'How much does it cost?' in Spanish?", 
                word: "Price inquiry", 
                image: "https://img.icons8.com/dusk/64/price-tag.png", 
                options: ["¿Dónde está?", "¿Cuánto cuesta?", "¿Qué hora es?", "¿Cómo te llamas?"], 
                answer: "¿Cuánto cuesta?",
                context: "Essential phrase for shopping in markets or stores"
            },
            { 
                category: "Directions",
                question: "Which phrase means 'turn left' in Spanish?", 
                word: "Turn left", 
                image: "https://img.icons8.com/dusk/64/left-2.png", 
                options: ["Siga derecho", "Gire a la izquierda", "Gire a la derecha", "Dé la vuelta"], 
                answer: "Gire a la izquierda",
                context: "Useful when navigating Spanish cities on foot"
            },
            { 
                category: "Animals",
                question: "What is 'perro' in English?", 
                word: "Perro", 
                image: "https://img.icons8.com/dusk/64/dog.png", 
                options: ["Cat", "Bird", "Dog", "Fish"], 
                answer: "Dog",
                context: "A common pet in Spanish households"
            }
        ];

        let fillQuestions = [
            { 
                category: "Present Tense Verbs",
                question: "Complete this sentence: Yo ___ español. (I speak Spanish)", 
                answer: "hablo",
                context: "From the verb 'hablar' (to speak)"
            },
            { 
                category: "Daily Routines",
                question: "María ___ desayuno a las ocho. (Maria has breakfast at eight)", 
                answer: "desayuna",
                context: "From the verb 'desayunar' (to have breakfast)"
            },
            { 
                category: "Travel",
                question: "Nosotros ___ a España el próximo verano. (We are going to Spain next summer)", 
                answer: "vamos",
                context: "From the verb 'ir' (to go)"
            },
            { 
                category: "Restaurant Phrases",
                question: "¿Puedo ___ la carta, por favor? (May I have the menu, please?)", 
                answer: "ver",
                context: "Useful when dining at restaurants"
            },
            { 
                category: "Weather",
                question: "Hoy ___ mucho sol. (Today it is very sunny)", 
                answer: "hace",
                context: "Weather expressions often use 'hacer'"
            },
            { 
                category: "Shopping",
                question: "¿___ tarjeta de crédito? (Do you accept credit cards?)", 
                answer: "Aceptan",
                context: "From the verb 'aceptar' (to accept)"
            },
            { 
                category: "Introductions",
                question: "Mucho ___ conocerte. (Nice to meet you)", 
                answer: "gusto",
                context: "Common phrase when meeting someone new"
            },
            { 
                category: "Time Expressions",
                question: "¿A qué hora ___ el museo? (At what time does the museum open?)", 
                answer: "abre",
                context: "From the verb 'abrir' (to open)"
            },
            { 
                category: "Directions",
                question: "Disculpe, ¿dónde ___ el baño? (Excuse me, where is the bathroom?)", 
                answer: "está",
                context: "Using 'estar' for locations"
            },
            { 
                category: "Ordering Food",
                question: "Yo ___ una paella, por favor. (I would like a paella, please)", 
                answer: "quiero",
                context: "From the verb 'querer' (to want)"
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
                } else if (fillQuestions.length > 0) {
                    q = fillQuestions.pop();
                    renderFillQuestion(q);
                }
            } else {
                if (fillQuestions.length > 0) {
                    q = fillQuestions.pop();
                    renderFillQuestion(q);
                } else if (vocabQuestions.length > 0) {
                    q = vocabQuestions.pop();
                    renderVocabQuestion(q);
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
                <div class="options-grid" id="options-container">
                </div>
            `;
            
            const optionsContainer = document.getElementById("options-container");
            shuffleArray([...q.options]).forEach((option, index) => {
                const btn = document.createElement('button');
                btn.className = 'option-btn';
                btn.textContent = option;
                btn.dataset.option = option;
                btn.dataset.answer = q.answer;
                btn.addEventListener('click', function() {
                    checkAnswerVocab(this.dataset.option, this.dataset.answer);
                });
                optionsContainer.appendChild(btn);
            });
        }

        function renderFillQuestion(q) {
            const parts = q.question.split('___');
            
            document.getElementById("questionArea").innerHTML = `
                <span class="category-label">${q.category}</span>
                <p class="question-text">Fill in the blank:</p>
                <p class="spanish-phrase">${parts[0]}<span class="blank">_____</span>${parts[1]}</p>
                ${q.context ? `<p class="context-hint">${q.context}</p>` : ''}
                <input type="text" id="fillInput" class="fill-input" placeholder="Type your answer here..." autofocus>
                <div class="navigation-buttons" style="justify-content: flex-end; margin-top: 15px;">
                    <button id="checkAnswerBtn" class="btn btn-primary">Check</button>
                </div>
            `;

            setTimeout(() => {
                document.getElementById("fillInput").addEventListener("keypress", function(event) {
                    if (event.key === "Enter") {
                        event.preventDefault();
                        checkAnswerFill(q.answer);
                    }
                });
                document.getElementById("checkAnswerBtn").addEventListener("click", function() {
                    checkAnswerFill(q.answer);
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
                feedbackDiv.textContent = '¡Correcto! ¡Bien hecho!';
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

            let isCorrect = acceptableAnswers.includes(value);

            if (isCorrect) {
                score++;
                input.classList.add('correct');
                
                const feedbackDiv = document.createElement('div');
                feedbackDiv.style.marginTop = '10px';
                feedbackDiv.style.color = '#2e7d32';
                feedbackDiv.style.fontWeight = 'bold';
                feedbackDiv.textContent = '¡Correcto! ¡Bien hecho!';
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
                rating = "¡Excelente! You have a strong grasp of Spanish.";
                emoji = "🏆";
            } else if (percentage >= 70) {
                rating = "¡Muy bien! You're making great progress in Spanish.";
                emoji = "👏";
            } else if (percentage >= 50) {
                rating = "¡No está mal! Keep practicing to improve your Spanish skills.";
                emoji = "👍";
            } else {
                rating = "¡Sigue estudiando! You might need more practice with Spanish fundamentals.";
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
                'exercise_type': 'spanish_practice',
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

