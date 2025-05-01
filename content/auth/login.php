<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: ../main/dashboard.php");
    exit;
}

$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : "";
$_SESSION['error'] = "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="login.css">
    <title>Welcome to Lango - Learn Languages</title>
</head>

<body>
    <div class="container">
        <div class="logo">
            <h1>Lango</h1>
        </div>
        
        <div class="wrapper">
            <form action="process_login.php" method="POST">
                <h2>Welcome Back!</h2>
                <p class="subtitle">Continue your language adventure</p>
                
                <?php if(!empty($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <div class="input-box">
                    <input type="text" name="username" placeholder="Email or Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>

                <div class="remember-forget">
                    <label><input type="checkbox" name="remember"> Remember Me</label>
                    <a href="forgot_password.php">Forgot password?</a>
                </div>
                
                <button class="btn" type="submit">Start Learning</button>
                
                <div class="register-link">
                    <p>New to Lango? <a href="register.php">Join the community</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
