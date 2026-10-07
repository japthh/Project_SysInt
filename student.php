<?php
//base endpoint for searching specific data
// add a value in the link to run [?ID=1]
header("Content-Type: application/json");
include "db.php";

if(!isset($_GET['ID'])){
    http_response_code(400);
    echo json_encode(["message" => "Student ID is required baii"]);
    exit;
}
$ID = filter_input(INPUT_GET, 'ID', FILTER_VALIDATE_INT);
if ($ID === false || $ID === null) {
    http_response_code(400);
    echo json_encode(["message" => "Student ID must be an integer"]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM students WHERE ID = ?");
$stmt->bind_param("i", $ID);
$stmt->execute();
$result = $stmt->get_result();//execute query

if($result->num_rows > 0){
    $student = $result->fetch_assoc();
    echo json_encode($student);
}else{
    echo json_encode(["message" => "Student not found baiiii"]);
}
$conn->close();
?>  