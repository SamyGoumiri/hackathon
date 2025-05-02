
<?php
    // Assuming you have a database connection established
    
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the JSON data sent from JavaScript
    $json_data = file_get_contents("php://input");
    $data = json_decode($json_data, true);

    if (isset($data['score']) && isset($data['total'])) {
        $score = intval($data['score']);
        $total = intval($data['total']);

        // Calculate a rating (you can customize this logic)
        $percentage = ($score / $total) * 100;
        $rating = "";
        if ($percentage >= 90) {
            $rating = "Excellent";
        } else if ($percentage >= 75) {
            $rating = "Good";
        } else if ($percentage >= 60) {
            $rating = "Average";
        } else {
            $rating = "Needs Improvement";
        }

        // Assuming you have a user ID in your session or passed in the request
        // Replace 'user_id' with the actual way you identify the user
        $user_id = 123; // Example user ID

        $sql = "UPDATE users SET score = $score WHERE id = $user_id";

        if ($conn->query($sql) === TRUE) {
            // Send back a JSON response including the rating
            $response = array('status' => 'success', 'rating' => $rating);
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            $response = array('status' => 'error', 'message' => "Error updating record: " . $conn->error);
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    } else {
        $response = array('status' => 'error', 'message' => "Invalid data received.");
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    $conn->close();
?>