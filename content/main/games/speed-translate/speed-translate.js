document.addEventListener('DOMContentLoaded', function() {

    const gameSetupScreen = document.getElementById('game-setup');
    const gamePlayScreen = document.getElementById('game-play');
    const gameOverScreen = document.getElementById('game-over');
    
    const languageOptions = document.querySelectorAll('.language-option');
    const difficultyOptions = document.querySelectorAll('.difficulty-option');
    
    const startGameBtn = document.getElementById('start-game');
    const submitAnswerBtn = document.getElementById('submit-answer');
    const userAnswerInput = document.getElementById('user-answer');
    const playAgainBtn = document.getElementById('play-again');
    
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
    let consecutiveCorrect = 0;
    let difficultyMultiplier = 1;
    
    languageOptions.forEach(option => {
        option.addEventListener('click', function() {
            languageOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            selectedLanguage = this.dataset.language;
            selectedLanguageName = this.dataset.languageName;
        });
    });
    
    difficultyOptions.forEach(option => {
        option.addEventListener('click', function() {
            difficultyOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            selectedTime = parseInt(this.dataset.time);
            
            if (selectedTime === 10) {
                difficultyMultiplier = 2;
            } else if (selectedTime === 20) {
                difficultyMultiplier = 1;
            } else {
                difficultyMultiplier = 0.8;
            }
        });
    });
    
    startGameBtn.addEventListener('click', startGame);
    
    submitAnswerBtn.addEventListener('click', checkAnswer);
    
    userAnswerInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            checkAnswer();
        }
    });
    
    playAgainBtn.addEventListener('click', function() {
        showScreen(gameSetupScreen);
    });

    document.querySelector('.user-info').addEventListener('click', function() {
        document.querySelector('.dropdown-menu').classList.toggle('active');
    });
    
    function startGame() {
        timeLeft = selectedTime;
        currentScore = 0;
        usedWords = [];
        wordsAttempted = 0;
        correctAnswers = 0;
        consecutiveCorrect = 0
        
        currentScoreDisplay.textContent = currentScore;
        timeRemaining.textContent = timeLeft;
        languageDisplay.textContent = `${selectedLanguageName} → English`;
        
        showScreen(gamePlayScreen);
        
        userAnswerInput.focus();
        
        showNextWord();
        
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
        userAnswerInput.value = '';
        feedbackDisplay.textContent = '';
        feedbackDisplay.className = 'feedback-display';
        
        const availableWords = vocabulary[selectedLanguage].filter(word => !usedWords.includes(word.word));
        
        if (availableWords.length === 0) {
            usedWords = [];
            currentWord = vocabulary[selectedLanguage][Math.floor(Math.random() * vocabulary[selectedLanguage].length)];
        } else {
            currentWord = availableWords[Math.floor(Math.random() * availableWords.length)];
        }
        
        usedWords.push(currentWord.word);
        wordToTranslate.textContent = currentWord.word;
        
        userAnswerInput.focus();
    }
    
    function checkAnswer() {
        wordsAttempted++;
        
        const userAnswer = userAnswerInput.value.trim().toLowerCase();
        if (userAnswer === currentWord.translation) {
            let basePoints = 1 * difficultyMultiplier;
            
            let timeBonus = Math.ceil(timeLeft / selectedTime * 5) / 10;
            
            consecutiveCorrect++;
            let comboBonus = consecutiveCorrect >= 3 ? Math.min(consecutiveCorrect / 10, 0.5) : 0;
            
            let pointsEarned = Math.ceil(basePoints * (1 + timeBonus + comboBonus));
            
            currentScore += pointsEarned;
            correctAnswers++;
            currentScoreDisplay.textContent = currentScore;
            
            feedbackDisplay.textContent = `✓ Correct! +${pointsEarned} points`;
            if (consecutiveCorrect >= 3) {
                feedbackDisplay.textContent += ` (${consecutiveCorrect}x combo!)`;
            }
            feedbackDisplay.classList.add('correct');
        } else {
            feedbackDisplay.textContent = `✗ Incorrect! The correct answer is "${currentWord.translation}"`;
            feedbackDisplay.classList.add('incorrect');
            consecutiveCorrect = 0; // Reset consecutive counter on wrong answer
        }
        
        setTimeout(showNextWord, 1000);
    }
    
    function endGame() {
        clearInterval(timer);
        
        if (currentScore > highScore) {
            highScore = currentScore;
            saveHighScore();
            finalScoreDisplay.textContent = currentScore + " - NEW HIGH SCORE!";
            finalScoreDisplay.classList.add("new-high-score");
        } else {
            finalScoreDisplay.textContent = currentScore;
            finalScoreDisplay.classList.remove("new-high-score");
            saveHighScore();
        }
        
        highScoreDisplay.textContent = highScore;
        wordsAttemptedDisplay.textContent = wordsAttempted;
        correctAnswersDisplay.textContent = correctAnswers;
        
        showScreen(gameOverScreen);
    }
    
    function saveHighScore() {
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
                if (data.is_high_score) {
                    userHighScoreDisplay.textContent = highScore;
                }
            }
        })
        .catch(error => console.error('Error saving score:', error));
    }
    
    function showScreen(screen) {
        gameSetupScreen.classList.remove('active');
        gamePlayScreen.classList.remove('active');
        gameOverScreen.classList.remove('active');
        
        screen.classList.add('active');
    }
});
