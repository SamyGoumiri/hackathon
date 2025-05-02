<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>

    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Select Language</title>
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <div class="header">
        <h1 id="welcome">Welcome</h1>
        <a href="../auth/logout.php" class="disconnect-btn">Disconnect</a>
    </div>
    
    <script>
        const userName = "<?php echo isset($_SESSION['first_name']) ? $_SESSION['first_name'] : ''; ?>";//User Name
        document.getElementById("welcome").textContent = userName ? `Welcome "${userName}"` : 'Welcome';
        document.title = userName ? `Welcome "${userName}"` : 'Welcome';
    </script>

    <div class="container">
    <div class="globe"></div>
    <div class="languages">
      <button data-lang="en">🌐 English</button>
      <button data-lang="fr">🇫🇷 Français</button>
      <button data-lang="es">🇪🇸 Español</button>
      <button data-lang="jp">🇯🇵 日本語</button>
    </div>
  </div>
  <script src="script.js"></script>
</body>
</html>