<?php 
$servername = "localhost";
$username = "root"; // Change as needed
$password = ""; // Change as needed
$dbname = "database"; // Change as needed

// Create connection


try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        // Si la connexion échoue
        die("Connection failed: " . $conn->connect_error);
    } else {
        echo "Connection successful!";
    }
}catch(Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

?>