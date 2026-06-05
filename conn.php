<?php
// Database connection details
$servername = getenv('DB_SERVER') ?: "localhost";
$username = getenv('DB_USERNAME') ?: "root";
$password = getenv('DB_PASSWORD') ?: "";
$dbname = getenv('DB_NAME') ?: "wackys";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); 

}

$conn->set_charset("utf8mb4");
?>
