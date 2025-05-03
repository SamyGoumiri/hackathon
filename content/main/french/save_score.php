
<?php header("Content-Type: application/json");

//get the JSON data from the request

if(isset($data['score'])&& isset($data['total'])){
    $score = $data['score'];
    $total = $data['total'];

    //connect to the database
    $conn = new mysqli("localhost", "username", "password", "database");

    //check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    //insert the score into the database
    $stmt = $conn->prepare("INSERT INTO scores (score, total) VALUES (?, ?)");
    $stmt->bind_param("ii", $score, $total);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to save score"]);
    }

    //close the connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
}


?>