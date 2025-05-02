<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Welcome</title>
  <title>Choisir la langue</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>

  <div class="container">

    <h1 id="welcome">Welcome</h1>

  <script>
    // Simulate a logged-in user (in real life this would come from a backend or auth system)
    const userName = "Alice"; // ← this would be dynamic
    document.getElementById("welcome").textContent = `Welcome "${userName}"`;
    document.title = `Welcome "${userName}"`;
  </script>

    <div class="globe"></div>
    <h1>Choose your language</h1>
    <div class="languages">
      <button data-lang="de">Deutsch</button>
      <button data-lang="it">Italian</button>
      <button data-lang="es">Spanish</button>
      <button data-lang="en">English</button>
      <button data-lang="fr">French</button>
    </div>  
  </div>

  <script src="script.js"></script>
</body>
</html>
