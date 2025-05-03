document.addEventListener('DOMContentLoaded', function() {
    // Game elements
    const gameSetupScreen = document.getElementById('game-setup');
    const gamePlayScreen = document.getElementById('game-play');
    const gameOverScreen = document.getElementById('game-over');
    
    // Setup options
    const languageOptions = document.querySelectorAll('.language-option');
    const difficultyOptions = document.querySelectorAll('.difficulty-option');
    
    // Game controls
    const startGameBtn = document.getElementById('start-game');
    const submitAnswerBtn = document.getElementById('submit-answer');
    const userAnswerInput = document.getElementById('user-answer');
    const playAgainBtn = document.getElementById('play-again');
    
    // Game display elements
    const wordToTranslate = document.getElementById('word-to-translate');
    const timeRemaining = document.getElementById('time-remaining');
    const currentScoreDisplay = document.getElementById('current-score');
    const finalScoreDisplay = document.getElementById('final-score');
    const highScoreDisplay = document.getElementById('high-score');
    const userHighScoreDisplay = document.getElementById('user-high-score');
    const wordsAttemptedDisplay = document.getElementById('words-attempted');
    const correctAnswersDisplay = document.getElementById('correct-answers');
    const feedbackDisplay = document.getElementById('feedback');
    const languageDisplay = document.querySelector('.language-display');
    
    // Game state variables
    let selectedLanguage = 'fr';
    let selectedLanguageName = 'French';
    let selectedTime = 20;
    let timeLeft = selectedTime;
    let currentScore = 0;
    let currentWord = null;
    let timer = null;
    let usedWords = [];
    let wordsAttempted = 0;
    let correctAnswers = 0;
    let highScore = parseInt(userHighScoreDisplay.textContent) || 0;
    
    // Handle language selection
    languageOptions.forEach(option => {
        option.addEventListener('click', function() {
            languageOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            selectedLanguage = this.dataset.language;
            selectedLanguageName = this.dataset.languageName;
        });
    });
    
    // Handle difficulty selection
    difficultyOptions.forEach(option => {
        option.addEventListener('click', function() {
            difficultyOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            selectedTime = parseInt(this.dataset.time);
        });
    });
    
    // Start game
    startGameBtn.addEventListener('click', startGame);
    
    // Submit answer
    submitAnswerBtn.addEventListener('click', checkAnswer);
    
    // Enter key to submit answer
    userAnswerInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            checkAnswer();
        }
    });
    
    // Play again
    playAgainBtn.addEventListener('click', function() {
        showScreen(gameSetupScreen);
    });

    // User menu dropdown
    document.querySelector('.user-info').addEventListener('click', function() {
        document.querySelector('.dropdown-menu').classList.toggle('active');
    });
    
    function startGame() {
        // Reset game state
        timeLeft = selectedTime;
        currentScore = 0;
        usedWords = [];
        wordsAttempted = 0;
        correctAnswers = 0;
        
        // Update UI
        currentScoreDisplay.textContent = currentScore;
        timeRemaining.textContent = timeLeft;
        languageDisplay.textContent = `${selectedLanguageName} → English`;
        
        // Show game play screen
        showScreen(gamePlayScreen);
        
        // Focus on input
        userAnswerInput.focus();
        
        // Display first word
        showNextWord();
        
        // Start timer
        timer = setInterval(updateTimer, 1000);
    }
    
    function updateTimer() {
        timeLeft--;
        timeRemaining.textContent = timeLeft;
        
        if (timeLeft <= 5) {
            timeRemaining.classList.add('time-low');
        } else {
            timeRemaining.classList.remove('time-low');
        }
        
        if (timeLeft <= 0) {
            endGame();
        }
    }
    
    function showNextWord() {
        // Clear previous word and answer
        userAnswerInput.value = '';
        feedbackDisplay.textContent = '';
        feedbackDisplay.className = 'feedback-display';
        
        // Get a random word that hasn't been used yet
        const availableWords = vocabulary[selectedLanguage].filter(word => !usedWords.includes(word.word));
        
        // If all words have been used, reset the usedWords array
        if (availableWords.length === 0) {
            usedWords = [];
            currentWord = vocabulary[selectedLanguage][Math.floor(Math.random() * vocabulary[selectedLanguage].length)];
        } else {
            currentWord = availableWords[Math.floor(Math.random() * availableWords.length)];
        }
        
        usedWords.push(currentWord.word);
        wordToTranslate.textContent = currentWord.word;
        
        // Focus on input
        userAnswerInput.focus();
    }
    
    function checkAnswer() {
        wordsAttempted++;
        
        const userAnswer = userAnswerInput.value.trim().toLowerCase();
        if (userAnswer === currentWord.translation) {
            currentScore++;
            correctAnswers++;
            currentScoreDisplay.textContent = currentScore;
            feedbackDisplay.textContent = '✓ Correct!';
            feedbackDisplay.classList.add('correct');
        } else {
            feedbackDisplay.textContent = `✗ Incorrect! The correct answer is "${currentWord.translation}"`;
            feedbackDisplay.classList.add('incorrect');
        }
        
        // Show next word after a short delay
        setTimeout(showNextWord, 1000);
    }
    
    function endGame() {
        // Clear timer
        clearInterval(timer);
        
        // Update high score if needed
        if (currentScore > highScore) {
            highScore = currentScore;
            saveHighScore();
        }
        
        // Update game over screen
        finalScoreDisplay.textContent = currentScore;
        highScoreDisplay.textContent = highScore;
        wordsAttemptedDisplay.textContent = wordsAttempted;
        correctAnswersDisplay.textContent = correctAnswers;
        
        // Show game over screen
        showScreen(gameOverScreen);
    }
    
    function saveHighScore() {
        // AJAX request to save high score
        fetch('save-score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `user_id=${userId}&score=${currentScore}&test_id=0`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                userHighScoreDisplay.textContent = highScore;
            }
        })
        .catch(error => console.error('Error saving score:', error));
    }
    
    function showScreen(screen) {
        // Hide all screens
        gameSetupScreen.classList.remove('active');
        gamePlayScreen.classList.remove('active');
        gameOverScreen.classList.remove('active');
        
        // Show the requested screen
        screen.classList.add('active');
    }
});
