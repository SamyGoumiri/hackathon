<?php
session_start();
require_once '../../../../database/connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Handle GET requests (fetching words)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getWords') {
    if (!isset($_GET['language'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Language not specified']);
        exit();
    }
    
    $language = $_GET['language'];
    $words = getWords($language);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'words' => $words]);
    exit();
}

// Handle POST requests (saving results)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Parse JSON input
    $jsonInput = file_get_contents('php://input');
    $data = json_decode($jsonInput, true);
    
    if (isset($data['action']) && $data['action'] === 'saveResults') {
        $userId = $data['userId'];
        $score = $data['score'];
        $xp = $data['xp'];
        $language = $data['language'];
        $time = $data['time'];
        
        $result = saveResults($userId, $score, $xp, $language, $time);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }
}

// Function to get words for the selected language
function getWords($language) {
    $words = [];
    
    // Define word sets for different languages
    switch ($language) {
        case 'fr':
            $words = [
                ['word' => 'bonjour', 'translation' => 'hello'],
                ['word' => 'au revoir', 'translation' => 'goodbye'],
                ['word' => 'merci', 'translation' => 'thank you'],
                ['word' => 'oui', 'translation' => 'yes'],
                ['word' => 'non', 'translation' => 'no'],
                ['word' => 'chat', 'translation' => 'cat'],
                ['word' => 'chien', 'translation' => 'dog'],
                ['word' => 'maison', 'translation' => 'house'],
                ['word' => 'livre', 'translation' => 'book'],
                ['word' => 'eau', 'translation' => 'water'],
                ['word' => 'pain', 'translation' => 'bread'],
                ['word' => 'vin', 'translation' => 'wine'],
                ['word' => 'fromage', 'translation' => 'cheese'],
                ['word' => 'pomme', 'translation' => 'apple'],
                ['word' => 'voiture', 'translation' => 'car'],
                ['word' => 'temps', 'translation' => 'time'],
                ['word' => 'argent', 'translation' => 'money'],
                ['word' => 'ami', 'translation' => 'friend'],
                ['word' => 'famille', 'translation' => 'family'],
                ['word' => 'travail', 'translation' => 'work']
            ];
            break;
            
        case 'es':
            $words = [
                ['word' => 'hola', 'translation' => 'hello'],
                ['word' => 'adiós', 'translation' => 'goodbye'],
                ['word' => 'gracias', 'translation' => 'thank you'],
                ['word' => 'sí', 'translation' => 'yes'],
                ['word' => 'no', 'translation' => 'no'],
                ['word' => 'gato', 'translation' => 'cat'],
                ['word' => 'perro', 'translation' => 'dog'],
                ['word' => 'casa', 'translation' => 'house'],
                ['word' => 'libro', 'translation' => 'book'],
                ['word' => 'agua', 'translation' => 'water'],
                ['word' => 'pan', 'translation' => 'bread'],
                ['word' => 'vino', 'translation' => 'wine'],
                ['word' => 'queso', 'translation' => 'cheese'],
                ['word' => 'manzana', 'translation' => 'apple'],
                ['word' => 'coche', 'translation' => 'car'],
                ['word' => 'tiempo', 'translation' => 'time'],
                ['word' => 'dinero', 'translation' => 'money'],
                ['word' => 'amigo', 'translation' => 'friend'],
                ['word' => 'familia', 'translation' => 'family'],
                ['word' => 'trabajo', 'translation' => 'work']
            ];
            break;
            
        case 'de':
            $words = [
                ['word' => 'hallo', 'translation' => 'hello'],
                ['word' => 'auf wiedersehen', 'translation' => 'goodbye'],
                ['word' => 'danke', 'translation' => 'thank you'],
                ['word' => 'ja', 'translation' => 'yes'],
                ['word' => 'nein', 'translation' => 'no'],
                ['word' => 'katze', 'translation' => 'cat'],
                ['word' => 'hund', 'translation' => 'dog'],
                ['word' => 'haus', 'translation' => 'house'],
                ['word' => 'buch', 'translation' => 'book'],
                ['word' => 'wasser', 'translation' => 'water'],
                ['word' => 'brot', 'translation' => 'bread'],
                ['word' => 'wein', 'translation' => 'wine'],
                ['word' => 'käse', 'translation' => 'cheese'],
                ['word' => 'apfel', 'translation' => 'apple'],
                ['word' => 'auto', 'translation' => 'car'],
                ['word' => 'zeit', 'translation' => 'time'],
                ['word' => 'geld', 'translation' => 'money'],
                ['word' => 'freund', 'translation' => 'friend'],
                ['word' => 'familie', 'translation' => 'family'],
                ['word' => 'arbeit', 'translation' => 'work']
            ];
            break;
            
        case 'it':
            $words = [
                ['word' => 'ciao', 'translation' => 'hello'],
                ['word' => 'arrivederci', 'translation' => 'goodbye'],
                ['word' => 'grazie', 'translation' => 'thank you'],
                ['word' => 'sì', 'translation' => 'yes'],
                ['word' => 'no', 'translation' => 'no'],
                ['word' => 'gatto', 'translation' => 'cat'],
                ['word' => 'cane', 'translation' => 'dog'],
                ['word' => 'casa', 'translation' => 'house'],
                ['word' => 'libro', 'translation' => 'book'],
                ['word' => 'acqua', 'translation' => 'water'],
                ['word' => 'pane', 'translation' => 'bread'],
                ['word' => 'vino', 'translation' => 'wine'],
                ['word' => 'formaggio', 'translation' => 'cheese'],
                ['word' => 'mela', 'translation' => 'apple'],
                ['word' => 'macchina', 'translation' => 'car'],
                ['word' => 'tempo', 'translation' => 'time'],
                ['word' => 'soldi', 'translation' => 'money'],
                ['word' => 'amico', 'translation' => 'friend'],
                ['word' => 'famiglia', 'translation' => 'family'],
                ['word' => 'lavoro', 'translation' => 'work']
            ];
            break;
            
        default:
            // Default to French if language not found
            return getWords('fr');
    }
    
    // Shuffle words
    shuffle($words);
    
    // Return the words
    return $words;
}

// Function to save game results
function saveResults($userId, $score, $xp, $language, $time) {
    global $conn;
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Check if there's an existing high score
        $highScoreQuery = "SELECT score FROM game_high_scores WHERE user_id = ? AND game_id = 'word_master'";
        $stmt = $conn->prepare($highScoreQuery);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $highScoreData = $result->fetch_assoc();
        $highScore = $highScoreData['score'] ?? 0;
        
        $newHighScore = false;
        
        // If new score is higher, update high score
        if ($score > $highScore) {
            $newHighScore = true;
            
            // Insert or update high score
            $upsertQuery = "INSERT INTO game_high_scores (user_id, game_id, score) 
                          VALUES (?, 'word_master', ?)
                          ON DUPLICATE KEY UPDATE score = ?, updated_at = CURRENT_TIMESTAMP";
            $stmt = $conn->prepare($upsertQuery);
            $stmt->bind_param("iii", $userId, $score, $score);
            $stmt->execute();
        }
        
        // Update user XP
        $xpQuery = "SELECT xp_points, level FROM user_experience WHERE user_id = ?";
        $stmt = $conn->prepare($xpQuery);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $xpResult = $stmt->get_result();
        
        if ($xpResult->num_rows > 0) {
            // User has XP record - update it
            $xpData = $xpResult->fetch_assoc();
            $currentXp = $xpData['xp_points'];
            $currentLevel = $xpData['level'];
            
            $newXp = $currentXp + $xp;
            $newLevel = calculateLevel($newXp);
            
            $updateXpQuery = "UPDATE user_experience 
                             SET xp_points = ?, level = ?, last_updated = CURRENT_TIMESTAMP 
                             WHERE user_id = ?";
            $stmt = $conn->prepare($updateXpQuery);
            $stmt->bind_param("iii", $newXp, $newLevel, $userId);
            $stmt->execute();
            
        } else {
            // User doesn't have XP record - create one
            $newLevel = calculateLevel($xp);
            
            $insertXpQuery = "INSERT INTO user_experience (user_id, xp_points, level) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertXpQuery);
            $stmt->bind_param("iii", $userId, $xp, $newLevel);
            $stmt->execute();
            
            $newXp = $xp;
        }
        
        // Log activity
        $activityDetails = json_encode([
            'game' => 'word_master',
            'language' => $language,
            'time' => $time,
            'score' => $score,
            'xp_earned' => $xp
        ]);
        
        $logQuery = "INSERT INTO user_activity (user_id, activity_type, activity_details) 
                    VALUES (?, 'game_played', ?)";
        $stmt = $conn->prepare($logQuery);
        $stmt->bind_param("is", $userId, $activityDetails);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        return [
            'success' => true, 
            'newHighScore' => $newHighScore, 
            'newXp' => $newXp,
            'newLevel' => $newLevel
        ];
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        
        return [
            'success' => false, 
            'message' => 'Error saving results: ' . $e->getMessage()
        ];
    }
}

// Helper function to calculate level based on XP
function calculateLevel($xp) {
    // Simple level calculation - adjust as needed
    return floor(sqrt($xp / 100)) + 1;
}

// Default response for any other request
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit();
