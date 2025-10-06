<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    exit('Not logged in');
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "emocam_db";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    exit("DB connection failed: " . $conn->connect_error);
}

$meeting_id = $_POST['meeting_id'] ?? null;

if (!$meeting_id) {
    http_response_code(400);
    exit("Missing meeting ID");
}

// Delete from child table first to avoid foreign key conflict
$conn->begin_transaction();

try {
    $stmt1 = $conn->prepare("DELETE FROM meeting_emotions WHERE meeting_id = ?");
    $stmt1->bind_param("s", $meeting_id);
    $stmt1->execute();

    $stmt2 = $conn->prepare("DELETE FROM meetings WHERE meeting_id = ?");
    $stmt2->bind_param("s", $meeting_id);
    $stmt2->execute();

    $conn->commit();
    echo "Session deleted successfully";
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo "Error deleting session: " . $e->getMessage();
}
$conn->close();
