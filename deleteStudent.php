<?php
// This endpoint deletes a student from the database using the ID from the URL.
header("Content-Type: application/json");
require_once "auth.php";
require_authenticated_session();
require_once "db.php";

// Only DELETE requests are accepted here.
if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

// Validate that the student ID is present and numeric.
$id = filter_input(INPUT_GET, "ID", FILTER_VALIDATE_INT);

if ($id === false || $id === null) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Student ID is required"]);
    exit;
}

// Delete the record whose ID matches.
$stmt = mysqli_prepare($conn, "DELETE FROM students WHERE ID = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

// Return a success message if a row was deleted; otherwise, report not found.
if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo json_encode(["success" => true, "message" => "Student deleted successfully"]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Student not found"]);
    }
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to delete student"]);
}
?>