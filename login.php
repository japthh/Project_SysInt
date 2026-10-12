<?php
// Tell the browser that the response is JSON.
header("Content-Type: application/json");
include "db.php";

// Only allow POST requests for this login endpoint.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

// Read the JSON request body sent by the browser.
$data = json_decode(file_get_contents("php://input"), true);
$username = is_string($data["username"] ?? null) ? trim($data["username"]) : "";
$password = is_string($data["password"] ?? null) ? $data["password"] : "";

// Check if both fields were submitted.
if ($username === "" || $password === "") {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Username and password are required"]);
    exit;
}

// Query the database for the supplied username.
$stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// If the user exists and the password matches, create a session for them.
if ($user && password_verify($password, $user["password"])) {
    session_start();
    session_regenerate_id(true);
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["username"] = $user["username"];

    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "user_id" => $user["id"],
        "username" => $user["username"]
    ]);
    exit;
}

// If the credentials do not match, return a failed login response.
http_response_code(401);
echo json_encode(["success" => false, "message" => "Invalid username or password"]);
?>