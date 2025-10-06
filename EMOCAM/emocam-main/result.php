<?php
file_put_contents('debug.log', "result.php accessed\n", FILE_APPEND);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header("Content-Type: application/json");

try {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "emocam_db";

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["error" => "DB connection failed"]);
        exit();
    }

    // Get JSON input
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data || !isset($data['patient_id']) || !isset($data['emotions'])) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid input"]);
        exit();
    }

    $patient_id = $data['patient_id'];
    $emotions = $data['emotions'];

    // Verify user is logged in
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(["error" => "User not logged in"]);
        exit();
    }
    $user_id = $_SESSION['user_id'];

    // Verify the patient belongs to the logged-in user
    $check = $conn->prepare("SELECT patient_id FROM patients WHERE patient_id = ? AND user_id = ?");
    $check->bind_param("si", $patient_id, $user_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows === 0) {
        http_response_code(403);
        echo json_encode(["error" => "Unauthorized patient"]);
        exit();
    }
    $check->close();

    // Generate new unique meeting_id
    $result = $conn->query("SELECT meeting_id FROM meetings ORDER BY meeting_id DESC LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_id = $row['meeting_id']; // e.g. 'ME012'
        $num = (int)substr($last_id, 2);
        $num++;
    } else {
        $num = 1;
    }
    $meeting_id = 'ME' . str_pad($num, 3, '0', STR_PAD_LEFT);

    // Insert into meetings table (without emotions, but with meeting_date)
    $stmt = $conn->prepare("INSERT INTO meetings (meeting_id, patient_id, meeting_date) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $meeting_id, $patient_id);
    $stmt->execute();
    $stmt->close();

    // Insert emotions into meeting_emotions table
    $stmt = $conn->prepare("INSERT INTO meeting_emotions (meeting_id, emotion, value) VALUES (?, ?, ?)");
    foreach ($emotions as $emotion => $value) {
        $stmt->bind_param("ssd", $meeting_id, $emotion, $value);
        $stmt->execute();
    }
    $stmt->close();

    $conn->close();

    echo json_encode(["status" => "success"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
