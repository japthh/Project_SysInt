<?php
//load all student endpoint
header("Content-Type: application/json");
require_once "auth.php";
require_authenticated_session();
include "db.php";                   //connects db.php
$sql = "SELECT * FROM students";    //selects all queries from the table in DB
$result = $conn->query($sql);       //get the executed query
$students = [];                     //inday uno ini na variable

if ($result === false) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Unable to load students"]);
    $conn->close();
    exit;
}
while($row = $result->fetch_assoc()){//loops result and stores it in json format
    $students[] = $row;
}
echo json_encode($students);
$conn->close();
?>