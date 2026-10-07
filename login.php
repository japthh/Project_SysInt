<?php
header("Content-Type: application/json");
include "db.php";  

if($_SERVER["REQUEST_METHOD"] === "POST"){
    http_response_code(405);
    echo json_encode(["success"=>false, "message"=>"Unknown Request"]);
    exit;
}
// Get the JSON input data
$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data["username"] ?? "");
$password = trim($data["password"] ?? "");
//validate input
if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(["success"=>false, "message"=>"Username and password are required"]);
    exit;
}

//find user in database
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

//check if user exists and password is correct
if ($user && password_verify($password, $user["password"])) {
    http_response_code(200);
    echo json_encode(["success"=>true, "message"=>"Login successful", "user"=>$user["id"], "username"=>$user["username"]]);
} else {
    http_response_code(401);
    echo json_encode(["success"=>false, "message"=>"Invalid username or password"]);
}

//create a new session for the user
session_regenerate_id(true);
$_SESSION["user_id"] = $user["id"];
$_SESSION["username"] = $user["username"];
$_SESSION["full_name"] = $user["full_name"];

echo json_encode(["success"=>true, 
                    "message"=>"Login successful", 
                    "user_id"=>$user["id"], 
                    "username"=>$user["username"], 
                    "full_name"=>$user["full_name"]]);


?>