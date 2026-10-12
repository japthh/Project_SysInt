<?php
// Return the full list of students as JSON for the JavaScript front end.
header("Content-Type: application/json");
require_once "auth.php";
require_authenticated_session();
include "db.php";

// Fetch all rows from the students table.
$result = $conn->query("SELECT * FROM students");
$students = [];

// Loop through each record and store it in an array.
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Send the student records to the browser as JSON.
echo json_encode($students);
$conn->close();
?>