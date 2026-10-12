<?php
// Database connection settings used by all PHP files in the project.
$host = "localhost";
$user = "root";
$password = "";
$database = "school";

// Create a connection to the MySQL database.
$conn = new mysqli($host, $user, $password, $database);

// Stop the script if the connection fails so the app does not continue with no DB access.
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>