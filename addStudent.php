<?php
// This endpoint adds a new student into the database.
header("Content-Type: application/json");
require_once "auth.php";
require_authenticated_session();
require_once "db.php";

// Only allow POST requests to create a new student record.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

// Read the JSON body sent from the browser.
$data = json_decode(file_get_contents("php://input"), true);
$fullname = trim($data["fullname"] ?? "");
$course = trim($data["course"] ?? "");

// Validate that both values are present before inserting.
if ($fullname === "" || $course === "") {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Fullname and course are required"]);
    exit;
}

// Insert the data into the students table using a prepared statement.
$stmt = mysqli_prepare($conn, "INSERT INTO students (Fullname, Course) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "ss", $fullname, $course);

// Send a success or failure message back to the browser.
if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["success" => true, "message" => "Student added successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to add student"]);
}
?>