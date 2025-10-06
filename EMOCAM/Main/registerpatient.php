<?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$dbname = "emocam_db";

// Koneksi DB
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('Please login first');
        window.location.href = 'login.html';
    </script>";
    exit();
}

$user_id = $_SESSION['user_id']; // ID user yang login
$name = $_POST['patient_name'];
$birthdate = $_POST['date'];
$email = $_POST['email'];

$result = $conn->query("SELECT patient_id FROM patients ORDER BY patient_id DESC LIMIT 1");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $lastId = $row['patient_id'];
    $num = intval(substr($lastId, 2)) + 1;
} else {
    $num = 1;
}
$patient_id = 'PA' . str_pad($num, 3, '0', STR_PAD_LEFT);

$sql = "INSERT INTO patients (patient_id, user_id, patient_name, patient_birthdate, patient_email) 
        VALUES ('$patient_id', '$user_id', '$name', '$birthdate', '$email')";

if ($conn->query($sql) === TRUE) {
    header("Location: meeting.php");
    exit();
} else {
    echo "<script>
        alert('Failed to save patient data.');
        window.location.href = 'registerpatient.html';
    </script>";
    exit();
}

$conn->close();
?>
