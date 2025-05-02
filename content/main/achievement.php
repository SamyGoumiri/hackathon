<?php
require 'user.php';

$stats = getUserStats();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stats['words_learned'] += 1;
    $stats['streak'] += 1;
    updateUserStats($stats);
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Language Achievement System</title>
    <link rel="stylesheet" href="achievement.css">
</head> 
<body>
    <h1>Welcome to the Language Learning App</h1>

    <p>Words Learned: <?= $stats['words_learned'] ?></p>
    <p>Current Streak: <?= $stats['streak'] ?> days</p>

    <form method="post">
        <button type="submit">Learn a New Word</button>
    </form> 

    <h2>Achievements</h2>
    <ul>
        <?php foreach ($stats['achievements'] as $ach): ?>
            <li>✅ <?= $ach ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Check for new achievements</h3>
    <iframe src="check_achievements.php" style="width:100%; height:50px; border:none;"></iframe>
</body>
</html>
