<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="register.css">
    <title>Register Page</title>
</head>

<body>
    <div class="wrapper">
        <form action="../backend/db.php">
            <h1>Sign up</h1>
            <div class="input-box">
                <input type="text" placeholder="Pseudo" required>
                <i class='bx bxs-user' ></i>
            </div>

            <div class="input-box">
                <input type="text" placeholder="Name" required>
                <i class='bx bxs-user' ></i>
            </div>

            <div class="input-box">
                <input type="text" placeholder="Last name" required>
                <i class='bx bxs-user' ></i>
            </div>

            <div class="input-box">
                <input type="email" placeholder="E-mail" required>
                <i class='bx bxs-user' ></i>
            </div>

            <div class="input-box">
                <input type="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <div class="input-box">
                <input type="password" placeholder="Confirm password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <button class="btn" type="submit">Register</button>

        </form>

    </div>

</body>

</html>
