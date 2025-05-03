<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="index.css">
    <title>Esperanto - Language Learning Made Easy</title>
</head>

<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Esperanto</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                    <li><a href="#languages">Languages</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if($is_logged_in): ?>
                    <a href="content/main/dashboard.php" class="btn btn-primary">My Dashboard</a>
                    <a href="content/auth/logout.php" class="btn btn-secondary">Disconnect</a>
                <?php else: ?>
                    <a href="content/auth/login.php" class="btn btn-secondary">Log In</a>
                    <a href="content/auth/register.php" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Master New Languages with Esperanto</h1>
            <p>Learn Spanish, Italian, French, and German through interactive lessons, AI-powered conversation practice, and fun language games.</p>
            <div class="hero-cta">
                <a href="content/auth/register.php" class="btn btn-primary btn-large">Start Learning for Free</a>
            </div>
            <div class="flag-container">
                <div class="flag-rectangle france">
                    <div class="flag">
                        <img src="https://flagcdn.com/w320/fr.png" alt="French Flag">
                    </div>
                </div>
                <div class="flag-rectangle spain">
                    <div class="flag">
                        <img src="https://flagcdn.com/w320/es.png" alt="Spanish Flag">
                    </div>
                </div>
                <div class="flag-rectangle italy">
                    <div class="flag">
                        <img src="https://flagcdn.com/w320/it.png" alt="Italian Flag">
                    </div>
                </div>
                <div class="flag-rectangle germany">
                    <div class="flag">
                        <img src="https://flagcdn.com/w320/de.png" alt="German Flag">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="features">
        <h2>Why Choose Esperanto?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class='bx bx-book-open'></i>
                </div>
                <h3>Structured Lessons</h3>
                <p>Progress through carefully designed units and lessons that build your vocabulary and grammar skills step by step.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class='bx bx-conversation'></i>
                </div>
                <h3>AI Language Assistant</h3>
                <p>Practice conversations, get grammar corrections, and translate phrases with our AI-powered language assistant.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class='bx bx-game'></i>
                </div>
                <h3>Interactive Games</h3>
                <p>Reinforce your learning with fun games like Speed Translate that make practicing vocabulary enjoyable.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class='bx bx-trophy'></i>
                </div>
                <h3>Achievement System</h3>
                <p>Earn badges and rewards as you make progress, keeping you motivated throughout your language learning journey.</p>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="how-it-works">
        <h2>How Esperanto Works</h2>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Choose Your Language</h3>
                <p>Select from Spanish, Italian, French, or German to begin your language journey.</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>Complete Lessons</h3>
                <p>Progress through engaging lessons organized into units focusing on different language skills.</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>Test Your Knowledge</h3>
                <p>Take unit tests to validate your understanding and reinforce what you've learned.</p>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <h3>Practice with AI</h3>
                <p>Sharpen your skills through conversation practice with our AI language assistants.</p>
            </div>
        </div>
    </section>

    <section id="languages" class="languages-section">
        <h2>Available Languages</h2>
        <div class="languages-grid">
            <div class="language-item">
                <img src="https://flagcdn.com/w160/es.png" alt="Spanish Flag">
                <h3>Spanish</h3>
                <p>Learn one of the world's most spoken languages with our comprehensive Spanish course.</p>
            </div>
            <div class="language-item">
                <img src="https://flagcdn.com/w160/fr.png" alt="French Flag">
                <h3>French</h3>
                <p>Master the language of love and culture with engaging French lessons.</p>
            </div>
            <div class="language-item">
                <img src="https://flagcdn.com/w160/it.png" alt="Italian Flag">
                <h3>Italian</h3>
                <p>Dive into the beautiful language of art, music, and cuisine.</p>
            </div>
            <div class="language-item">
                <img src="https://flagcdn.com/w160/de.png" alt="German Flag">
                <h3>German</h3>
                <p>Build your skills in this important European language of business and culture.</p>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="cta-content">
            <h2>Ready to Start Your Language Journey?</h2>
            <p>Join thousands of successful language learners today and unlock your potential.</p>
            <a href="content/auth/register.php" class="btn btn-primary btn-large">Sign Up Free</a>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-logo">
                <h2>Esperanto</h2>
                <p>Making language learning accessible for everyone</p>
            </div>
            <div class="footer-links">
                <div class="footer-links-column">
                    <h3>Platform</h3>
                    <a href="#features">Features</a>
                    <a href="#how-it-works">How It Works</a>
                    <a href="#languages">Languages</a>
                </div>
                <div class="footer-links-column">
                    <h3>Account</h3>
                    <a href="content/auth/register.php">Sign Up</a>
                    <a href="content/auth/login.php">Log In</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Esperanto. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>