<?php
header('Content-Type: application/json');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();}
else{
    $ID = $_GET['ID'] ?? "";
    $sql = "DELETE FROM students WHERE ID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $ID);
    
    if (mysqli_stmt_execute($stmt)) {
        if(mysqli_stmt_affected_rows($stmt) > 0){
            http_response_code(200); // OK
            echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(['success' => false, 'message' => 'Student not found']);
        }
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Error deleting student: ' . mysqli_error($conn)]);
    }
}





?>