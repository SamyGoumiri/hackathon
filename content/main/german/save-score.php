<?php
<<<<<<< Updated upstream
session_start();
require_once "../../../database/connect.php";

// Redirige si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
=======
    session_start();
    require_once "../../../database/connect.php";
>>>>>>> Stashed changes

// Récupère les données JSON envoyées par le client
$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

// Vérifie que les données sont valides
if (isset($data['score']) && isset($data['total'])) {
    $score = intval($data['score']);
    $total = intval($data['total']);

    if ($total <= 0) {
        $response = ['status' => 'error', 'message' => "Le total doit être supérieur à 0."];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    // Calcul du pourcentage et du niveau
    $percentage = ($score / $total) * 100;
    if ($percentage >= 90) {
        $rating = "Excellent";
    } elseif ($percentage >= 75) {
        $rating = "Good";
    } elseif ($percentage >= 60) {
        $rating = "Average";
    } else {
        $rating = "Needs Improvement";
    }

    // Requête SQL sécurisée avec prepare + bind
    $stmt = $conn->prepare("UPDATE users SET score = ? WHERE id = ?");
    $stmt->bind_param("ii", $score, $user_id);

    if ($stmt->execute()) {
        $response = ['status' => 'success', 'rating' => $rating];
    } else {
        $response = ['status' => 'error', 'message' => "Erreur SQL : " . $stmt->error];
    }

    $stmt->close();
} else {
    $response = ['status' => 'error', 'message' => "Données invalides reçues."];
}

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
