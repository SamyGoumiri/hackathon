<?php
session_start();
require_once '../../database/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['lang'])) {
    header("Location: dashboard.php");
    exit();
}

$language_code = sanitize_input($conn, $_GET['lang']);

// Map language codes to their directories
$language_paths = [
    'fr' => 'french/french.php',
    'de' => 'german/german.php',
    'es' => 'spanish/spanish.php',
    'it' => 'italian/italian.php'
];

if (isset($language_paths[$language_code])) {
    // Redirect to the specific language page
    header("Location: " . $language_paths[$language_code]);
    exit();
} else {
    // Language not supported yet
    header("Location: dashboard.php");
    exit();
}
?>
