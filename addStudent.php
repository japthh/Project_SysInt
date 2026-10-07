<?php
header("Content-Type: application/json");
require_once "db.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $data = json_decode(file_get_contents("php://input"),true);
    $fullname = trim($data["fullname"] ?? "");
    $course = trim($data["course"] ?? "");

    if ($fullname === "" || $course === "") {
        http_response_code(400);
        echo json_encode(["success"=>false,"message"=>"Fullname and course are required"]);
        exit;
    }

    $sql = "INSERT INTO students (Fullname, Course) VALUES (?,?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt,"ss", $fullname, $course);//ss is string string lmaoo
        if(mysqli_stmt_execute($stmt)){
            http_response_code(201);
            echo json_encode(["success"=>true,"message"=>"Student Created Success Baii"]);
        }
        else{
            http_response_code(500);
            echo json_encode(["success"=>false,"message"=>"Failed to create Student Baii"]);
        }
}
else{
    http_response_code(405);
    echo json_encode(["success"=>false, "message"=>"Unknown Request"]);
}







?>