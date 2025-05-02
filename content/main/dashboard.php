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
    <h1 id = "welcome">Welcome</h1>
    
    <script>
        const userName = "";//User Name
        document.getElementById("welcome").textContent = `Welcome "${userName}"`;
        document.title = `Welcome "${userName}"`;
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