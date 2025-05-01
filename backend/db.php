<?php 
$servername = "localhost";
$username = "root"; // Change as needed
$password = ""; // Change as needed
$dbname = "database_project"; // Change as needed

// Create connection
try {
    $conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
}catch(Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

if($_SERVER['REQUEST_METHOD']==='POST')
    {
        $username=$_POST['username']
        $password=$_POST['']
    }
?>