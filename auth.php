<?php
// This file checks whether a user is logged in and also handles logout.
function require_authenticated_session(): void
{
    // Start the session if it has not started yet.
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // If the user is not logged in, block access and return JSON for the front end.
    if (empty($_SESSION["user_id"])) {
        http_response_code(401);
        header("Content-Type: application/json");
        echo json_encode(["success" => false, "message" => "Authentication required"]);
        exit;
    }
}

// When this file is called directly, it acts as a simple auth status endpoint.
if (realpath($_SERVER["SCRIPT_FILENAME"] ?? "") === __FILE__) {
    header("Content-Type: application/json");
    header("Cache-Control: no-store");
    session_start();

    // If the request is a logout action, clear the session and destroy it.
    if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_GET["action"] ?? "") === "logout") {
        $_SESSION = [];

        // Clear the session cookie to make sure the client is signed out.
        if (ini_get("session.use_cookies")) {
            $cookie = session_get_cookie_params();
            setcookie(session_name(), "", time() - 42000, $cookie["path"], $cookie["domain"], $cookie["secure"], $cookie["httponly"]);
        }

        session_destroy();
        echo json_encode(["success" => true]);
        exit;
    }

    // Return whether the current session is authenticated.
    echo json_encode(["authenticated" => !empty($_SESSION["user_id"]) ]);
}
?>
