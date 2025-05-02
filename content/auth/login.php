<?php
session_start();
require_once "../../database/connect.php";

if(isset($_SESSION['user_id'])) {
    header("Location: ../main/dashboard.php");
    exit;
}

$error_message = "";
$success_message = "";
$prefill_username = "";

// Check if user just registered successfully
if(isset($_GET['registered']) && $_GET['registered'] === 'success') {
    $success_message = "Registration successful! Please login with your new account.";
    if(isset($_GET['username'])) {
        $prefill_username = htmlspecialchars($_GET['username']);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($conn, $_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['first_name'] = $user['first_name'];
            
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expires = time() + (30 * 24 * 60 * 60); // 30 days
                
                $sql = "UPDATE users SET remember_token = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $token, $user['user_id']);
                $stmt->execute();
                
                setcookie('remember_token', $token, $expires, '/');
                setcookie('user_id', $user['user_id'], $expires, '/');
            }
            
            $sql = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
            $stmt->prepare($sql);
            $stmt->bind_param("i", $user['user_id']);
            $stmt->execute();
            
            header("Location: ../main/dashboard.php");
            exit;
        } else {
            $error_message = "Invalid password. Please try again.";
        }
    } else {
        $error_message = "User not found. Please check your username or email.";
    }
    
    $stmt->close();
}

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token']) && isset($_COOKIE['user_id'])) {
    $token = $_COOKIE['remember_token'];
    $user_id = $_COOKIE['user_id'];
    
    $sql = "SELECT * FROM users WHERE user_id = ? AND remember_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['first_name'] = $user['first_name'];
        
        header("Location: ../main/dashboard.php");
        exit;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="login.css">
    <title>Welcome to Esperanto - Learn Languages</title>
</head>

<body>
    <div class="container">
        <div class="logo">
            <a href="../../index.php">
                <h1>Esperanto</h1>
            </a>
        </div>
        
        <div class="wrapper">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <h2>Welcome Back!</h2>
                <p class="subtitle">Continue your language adventure</p>
                
                <?php if(!empty($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <?php if(!empty($success_message)): ?>
                    <div class="success-message"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <div class="input-box">
                    <input type="text" name="username" placeholder="Email or Username" value="<?php echo $prefill_username; ?>" required>
                    <i class='bx bxs-user'></i>
                </div>
                
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                
                <button class="btn" type="submit">Start Learning</button>
                
                <div class="register-link">
                    <p>New to Esperanto? <a href="register.php">Join the community</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
