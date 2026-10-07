<?php
function require_authenticated_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION["user_id"])) {
        http_response_code(401);
        header("Content-Type: application/json");
        echo json_encode(["success" => false, "message" => "Authentication required"]);
        exit;
    }
}

if (realpath($_SERVER["SCRIPT_FILENAME"] ?? "") === __FILE__) {
    header("Content-Type: application/json");
    header("Cache-Control: no-store");
    session_start();

    if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_GET["action"] ?? "") === "logout") {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $cookie = session_get_cookie_params();
            setcookie(
                session_name(),
                "",
                time() - 42000,
                $cookie["path"],
                $cookie["domain"],
                $cookie["secure"],
                $cookie["httponly"]
            );
        }

        session_destroy();
        echo json_encode(["success" => true]);
        exit;
    }

    echo json_encode([
        "authenticated" => !empty($_SESSION["user_id"])
    ]);
}
?>
