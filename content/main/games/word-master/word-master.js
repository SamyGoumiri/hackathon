document.addEventListener('DOMContentLoaded', function() {
    // Game elements
    const setupScreen = document.getElementById('setupScreen');
    const gameScreen = document.getElementById('gameScreen');
    const resultsScreen = document.getElementById('resultsScreen');
    
    // Setup elements
    const languageOptions = document.querySelectorAll('.language-option');
    const timeOptions = document.querySelectorAll('.time-option');
    const selectedLanguageDisplay = document.getElementById('selectedLanguage');
    const selectedTimeDisplay = document.getElementById('selectedTime');
    const startGameBtn = document.getElementById('startGameBtn');
    
    // Game elements
    const timerDisplay = document.getElementById('timer');
    const currentScoreDisplay = document.getElementById('currentScore');
    const wordDisplay = document.getElementById('wordToTranslate');
    const translationInput = document.getElementById('translationInput');
    const submitBtn = document.getElementById('submitTranslation');
    const skipBtn = document.getElementById('skipWord');
    const feedbackDisplay = document.getElementById('translationFeedback');
    
    // Results elements
    const finalScoreDisplay = document.getElementById('finalScore');
    const wordsTranslatedDisplay = document.getElementById('wordsTranslated');
    const xpEarnedDisplay = document.getElementById('xpEarned');
    const newHighScoreDisplay = document.getElementById('newHighScore');
    const playAgainBtn = document.getElementById('playAgainBtn');
    
    // User ID
    const userId = document.getElementById('userId').value;

    // Game state variables
    let selectedLanguage = null;
    let selectedTime = null;
    let currentWord = null;
    let currentTranslation = null;
    let score = 0;
    let wordsTranslated = 0;
    let wordsIncorrect = 0;
    let timer = null;
    let timeLeft = 0;
    let wordsList = [];
    let currentWordIndex = 0;

    // Setup event listeners
    languageOptions.forEach(option => {
        option.addEventListener('click', function() {
            languageOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            selectedLanguage = this.getAttribute('data-lang');
            selectedLanguageDisplay.textContent = this.textContent;
            checkStartButtonStatus();
        });
    });

    timeOptions.forEach(option => {
        option.addEventListener('click', function() {
            timeOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            selectedTime = parseInt(this.getAttribute('data-time'));
            selectedTimeDisplay.textContent = selectedTime;
            checkStartButtonStatus();
        });
    });

    startGameBtn.addEventListener('click', startGame);
    submitBtn.addEventListener('click', checkAnswer);
    skipBtn.addEventListener('click', skipWord);
    playAgainBtn.addEventListener('click', resetGame);

    translationInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            checkAnswer();
        }
    });

    function checkStartButtonStatus() {
        if (selectedLanguage && selectedTime) {
            startGameBtn.disabled = false;
        } else {
            startGameBtn.disabled = true;
        }
    }

    function startGame() {
        // Hide setup screen, show game screen
        setupScreen.classList.add('hidden');
        gameScreen.classList.remove('hidden');
        
        // Reset game state
        score = 0;
        wordsTranslated = 0;
        wordsIncorrect = 0;
        currentScoreDisplay.textContent = score;
        timeLeft = selectedTime;
        timerDisplay.textContent = timeLeft;
        
        // Fetch words
        fetchWords(selectedLanguage);
    }

    function fetchWords(language) {
        // Show loading state
        wordDisplay.textContent = "Loading...";
        translationInput.disabled = true;
        submitBtn.disabled = true;
        skipBtn.disabled = true;
        
        // Fetch words from API
        fetch(`api.php?action=getWords&language=${language}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    wordsList = data.words;
                    currentWordIndex = 0;
                    
                    // Start the game with the first word
                    displayNextWord();
                    startTimer();
                    
                    // Enable input
                    translationInput.disabled = false;
                    submitBtn.disabled = false;
                    skipBtn.disabled = false;
                    translationInput.focus();
                } else {
                    wordDisplay.textContent = "Error loading words. Please try again.";
                }
            })
            .catch(error => {
                console.error('Error fetching words:', error);
                wordDisplay.textContent = "Error loading words. Please try again.";
            });
    }

    function displayNextWord() {
        // Check if we have more words
        if (currentWordIndex < wordsList.length) {
            currentWord = wordsList[currentWordIndex].word;
            currentTranslation = wordsList[currentWordIndex].translation;
            wordDisplay.textContent = currentWord;
            translationInput.value = '';
            feedbackDisplay.textContent = '';
            currentWordIndex++;
        } else {
            // If we run out of words, fetch more
            fetchWords(selectedLanguage);
        }
    }

    function startTimer() {
        clearInterval(timer); // Clear any existing timer
        
        timer = setInterval(() => {
            timeLeft--;
            timerDisplay.textContent = timeLeft;
            
            // Add warning classes for time running out
            if (timeLeft <= 5) {
                timerDisplay.classList.add('danger');
            } else if (timeLeft <= 10) {
                timerDisplay.classList.add('warning');
            }
            
            if (timeLeft <= 0) {
                endGame();
            }
        }, 1000);
    }

    function checkAnswer() {
        const userAnswer = translationInput.value.trim().toLowerCase();
        const correctAnswer = currentTranslation.toLowerCase();
        
        if (userAnswer === correctAnswer) {
            // Correct answer
            score += 10;
            wordsTranslated++;
            currentScoreDisplay.textContent = score;
            
            feedbackDisplay.textContent = "Correct!";
            feedbackDisplay.className = "translation-feedback feedback-correct";
            
            // Move to next word
            displayNextWord();
        } else {
            // Incorrect answer
            wordsIncorrect++;
            
            feedbackDisplay.textContent = `Incorrect. The translation is: ${currentTranslation}`;
            feedbackDisplay.className = "translation-feedback feedback-incorrect";
            
            // Wait a moment, then move to next word
            setTimeout(() => {
                displayNextWord();
            }, 1500);
        }
    }

    function skipWord() {
        // Penalize skipping with -2 points
        if (score > 0) {
            score = Math.max(0, score - 2);
            currentScoreDisplay.textContent = score;
        }
        
        feedbackDisplay.textContent = `Skipped. The translation was: ${currentTranslation}`;
        feedbackDisplay.className = "translation-feedback";
        
        // Move to next word
        displayNextWord();
    }

    function endGame() {
        // Stop timer
        clearInterval(timer);
        
        // Calculate XP (based on score and accuracy)
        const totalAttempts = wordsTranslated + wordsIncorrect;
        const accuracy = totalAttempts > 0 ? (wordsTranslated / totalAttempts) : 0;
        const xpEarned = Math.round(score * (0.5 + (accuracy * 0.5)));
        
        // Update result screen
        finalScoreDisplay.textContent = score;
        wordsTranslatedDisplay.textContent = wordsTranslated;
        xpEarnedDisplay.textContent = xpEarned;
        
        // Save score and XP to database
        saveResults(score, xpEarned);
        
        // Hide game screen, show results screen
        gameScreen.classList.add('hidden');
        resultsScreen.classList.remove('hidden');
    }

    function saveResults(finalScore, xpEarned) {
        fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'saveResults',
                userId: userId,
                score: finalScore,
                xp: xpEarned,
                language: selectedLanguage,
                time: selectedTime
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update high score indicator if it's a new high score
                if (data.newHighScore) {
                    newHighScoreDisplay.classList.remove('hidden');
                }
                
                // Update XP display
                document.getElementById('userXP').textContent = data.newXp;
                document.getElementById('userLevel').textContent = data.newLevel;
            }
        })
        .catch(error => {
            console.error('Error saving results:', error);
        });
    }

    function resetGame() {
        // Reset game state and return to setup screen
        resultsScreen.classList.add('hidden');
        newHighScoreDisplay.classList.add('hidden');
        setupScreen.classList.remove('hidden');
        
        // Reset timer display classes
        timerDisplay.classList.remove('warning', 'danger');
        
        // Clear selections
        selectedLanguage = null;
        selectedTime = null;
        languageOptions.forEach(opt => opt.classList.remove('selected'));
        timeOptions.forEach(opt => opt.classList.remove('selected'));
        selectedLanguageDisplay.textContent = 'None';
        selectedTimeDisplay.textContent = 'None';
        startGameBtn.disabled = true;
    }
});
