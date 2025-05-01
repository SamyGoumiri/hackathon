<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <h1 id = "welcome">Welcome</h1>
    
    <script>
        const userName = "";//User Name
        document.getElementById("welcome").textContent = `Welcome "${userName}"`;
        document.title = `Welcome "${userName}"`;
    </script>
</body>
</html>