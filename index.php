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
    <title>Lango - Learn Languages Effectively</title>
</head>

<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Lango</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if($is_logged_in): ?>
                    <a href="content/main/dashboard.php" class="btn btn-primary">My Dashboard</a>
                <?php else: ?>
                    <a href="content/auth/login.php" class="btn btn-secondary">Log In</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Master Any Language with Lango</h1>
            <p>The fun, effective way to learn a new language.</p>
            <div class="hero-cta">
                <a href="#how-it-works" class="btn btn-outline">Learn More</a>
            </div>
        </div>
        <div class="hero-image">
            <div class="flag-container">
                <div class="flag-rectangle france">
                    <div class="flag">
                        <img src="https://flagcdn.com/w320/fr.png" alt="French Flag">
                    </div>
                </div>
                <div class="flag-rectangle uk">
                    <div class="flag">
                        <img src="https://flagcdn.com/w320/gb.png" alt="UK Flag">
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
        <h2>Why Choose Lango?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class='bx bx-conversation'></i>
                </div>
                <h3>Interactive Learning</h3>
                <p>Learn through conversation, not memorization. Our interactive approach keeps you engaged and motivated.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class='bx bx-brain'></i>
                </div>
                <h3>Smart Algorithm</h3>
                <p>Our AI adapts to your learning style, focusing on what you need to practice most.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class='bx bx-time-five'></i>
                </div>
                <h3>Learn Anywhere</h3>
                <p>Short, effective lessons that fit into your busy schedule. Just 15 minutes a day makes a difference.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class='bx bx-medal'></i>
                </div>
                <h3>Achievement System</h3>
                <p>Earn badges and rewards as you progress, keeping you motivated throughout your journey.</p>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="how-it-works">
        <h2>How Lango Works</h2>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Choose Your Language</h3>
                <p>Select from our variety of languages and set your proficiency level.</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>Daily Practice</h3>
                <p>Complete short, interactive lessons tailored to your learning style.</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>Track Progress</h3>
                <p>Monitor your improvement with detailed statistics and achievement badges.</p>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <h3>Become Fluent</h3>
                <p>Advance through levels and achieve real-world conversation skills.</p>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="cta-content">
            <h2>Ready to Start Your Language Journey?</h2>
            <p>Join thousands of successful language learners today.</p>
            <a href="content/auth/register.php" class="btn btn-primary btn-large">Sign Up Free</a>
        </div>
    </section>
</body>
</html>