<?php
// Return a single student record based on the ID in the URL.
header("Content-Type: application/json");
require_once "auth.php";
require_authenticated_session();
include "db.php";

// Get the student ID from the query string and validate it.
$id = filter_input(INPUT_GET, "ID", FILTER_VALIDATE_INT);

if ($id === false || $id === null) {
    http_response_code(400);
    echo json_encode(["message" => "Student ID is required"]);
    exit;
}

// Search the database for the matching student row.
$stmt = $conn->prepare("SELECT * FROM students WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// If the student exists, return that row; otherwise, return a not-found response.
if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["message" => "Student not found"]);
}

$conn->close();
?>