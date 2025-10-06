<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('Please login first');
        window.location.href = 'login.html';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmoCam</title>
    <link rel="stylesheet" href="Home.css">
</head>
<body>
    
    <header class="head">
        <h2 class="logo"><img src="foto/logo.jpg" alt="Logo"></h2>
    </header>
    
    <section class="class">
        <nav class="navigation">
            <a class="consul-nav" href="consultation.php">
                <img src="foto/startconsul.png" alt="consul">
                <button class="start">Start Consultation</button>
            </a>
            <a class="seeres-nav" href="seeResult.php">
                <img src="foto/see result.png" alt="result">
                <button class="result">See Result</button>
            </a>
        </nav>
    </section>
</body>
</html>
