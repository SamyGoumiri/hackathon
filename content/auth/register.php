<?php
session_start();
require_once "../../database/connect.php";

if(isset($_SESSION['user_id'])) {
    header("Location: ../main/dashboard.php");
    exit;
}

$error_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($conn, $_POST['username']);
    $first_name = sanitize_input($conn, $_POST['first_name']);
    $last_name = sanitize_input($conn, $_POST['last_name']);
    $email = sanitize_input($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $valid = true;
    
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $error_message = "Username must be 3-20 characters and contain only letters, numbers, and underscores.";
        $valid = false;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
        $valid = false;
    }
    
    if (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
        $valid = false;
    }
    
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
        $valid = false;
    }
    
    if ($valid) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error_message = "Username already exists. Please choose another one.";
            $valid = false;
        }
        $stmt->close();
    }
    
    if ($valid) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error_message = "Email address already in use. Please use a different email or try logging in.";
            $valid = false;
        }
        
        $stmt->close();
    }
    
    if ($valid) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, first_name, last_name, email, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $first_name, $last_name, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['first_name'] = $first_name;
            
            header("Location: ../main/dashboard.php");
            exit;
        } else {
            $error_message = "Registration failed. Please try again later.";
        }
        
        $stmt->close();
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
    <link rel="stylesheet" href="register.css">
    <title>Join Esperanto - Learn Languages</title>
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
                <h2>Join Esperanto</h2>
                <p class="subtitle">Begin your language learning adventure</p>
                
                <?php if(!empty($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>

                <div class="input-box">
                    <input type="text" name="first_name" placeholder="First Name" required>
                    <i class='bx bxs-user-detail'></i>
                </div>

                <div class="input-box">
                    <input type="text" name="last_name" placeholder="Last Name" required>
                    <i class='bx bxs-user-detail'></i>
                </div>

                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>
                </div>

                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>

                <div class="input-box">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                
                <button class="btn" type="submit">Create Account</button>
                
                <div class="login-link">
                    <p>Already have an account? <a href="login.php">Log in</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
