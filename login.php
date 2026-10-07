<?php
header("Content-Type: application/json");
include "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success"=>false, "message"=>"Unknown Request"]);
    exit;
}

// Get the JSON input data
$data = json_decode(file_get_contents("php://input"), true); //get the JSON input data and decode it into an associative array
$username = is_array($data) && is_string($data["username"] ?? null) ? trim($data["username"]) : ""; //get the username from the input data and trim any whitespace
$password = is_array($data) && is_string($data["password"] ?? null) ? $data["password"] : ""; //get the password from the input data

//validate input
if ($username === "" || $password === "") {
    http_response_code(400);
    echo json_encode(["success"=>false, "message"=>"Username and password are required"]);
    exit;
}

//find user in database
$sql = "SELECT id, username, password FROM users WHERE username = ?"; 
$stmt = mysqli_prepare($conn, $sql);            //prepare the SQL statement
mysqli_stmt_bind_param($stmt, "s", $username);  //bind the username parameter to the prepared statement
mysqli_stmt_execute($stmt);                     //execute the prepared statement
$result = mysqli_stmt_get_result($stmt);        //get the result set from the prepared statement
$user = mysqli_fetch_assoc($result);            //convert result to associative array

//check if user exists and password is correct
if ($user && password_verify($password, $user["password"])) {
    session_start();
    session_regenerate_id(true);
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["username"] = $user["username"];

    http_response_code(200);
    echo json_encode([
        "success"=>true,
        "message"=>"Login successful bai",
        "user_id"=>$user["id"],
        "username"=>$user["username"]
    ]);
    exit;
}

http_response_code(401);
echo json_encode(["success"=>false, "message"=>"Invalid username or password"]);
?>