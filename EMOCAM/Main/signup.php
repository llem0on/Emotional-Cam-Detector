<?php

// signup.php

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

$result = $conn->query("SELECT id FROM users ORDER BY id DESC LIMIT 1");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $lastId = $row['id'];
    $num = intval(substr($lastId, 2)) + 1; // ambil angka setelah "PS"
} else {
    $num = 1; // kalau belum ada data
}
$id = 'PS' . str_pad($num, 3, '0', STR_PAD_LEFT);

// Ambil data dari form
$name = $_POST['username'];
$birthdate = $_POST['date'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

if ($password !== $confirm_password) {
    die("Your password does not match.");
}

// Simpan ke database
$sql = "INSERT INTO users (id, username, birthdate, email, password)
        VALUES ('$id', '$name', '$birthdate', '$email', '$password')";

if ($conn->query($sql) === TRUE) {
    header("Location: login.html");
    exit();
} else {
    header("Location: signup.html");
    exit();
}

$conn->close();
?>
