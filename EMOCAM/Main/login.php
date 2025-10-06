<?php
session_start();
// login.php

$host = "localhost";
$username = "root";
$password = "";
$dbname = "emocam_db";

// Buat koneksi
$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil input dari form
$email = $_POST['email'];
$password = $_POST['password'];

// Cek ke database
$sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc(); // Fetch the user row here
    $_SESSION['user_id'] = $row['id']; // Set session variable
    header("Location: home.php");
    exit();
} else {
    echo "<script>
        alert('Account not found. Please sign up first.');
        window.location.href = 'login.html';
    </script>";
    exit();
}
$conn->close();
?>
