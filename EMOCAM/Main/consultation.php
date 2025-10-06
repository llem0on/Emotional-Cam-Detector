<?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$dbname = "emocam_db";

// Connect to DB
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('Please login first');
        window.location.href = 'login.html';
    </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Get patients for the user
$patients = [];
$sql = "SELECT * FROM patients WHERE user_id = '$user_id' ORDER BY patient_id DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Consultation</title>
    <link rel="stylesheet" href="consultation.css">
</head>
<body>
    <header class="head">
        <a class="logo" href="Home.php"><img src="foto/logo.jpg" alt="Logo"></a>
    </header>

    <section class="back">
        <a href="Home.php"><img src="foto/arrow (1).png" alt="arrow"></a>
    </section>

    <section class="selection-text">
        <h1>Select Your Patient</h1>
        <h3>Who do you want to have consultation with?</h3>
    </section>

    <div class="custom-dropdown">
        <form action="meeting.php" method="post">
            <div class="selected">Select Patient</div>
            <ul class="options">
                <li onclick="window.location.href='registerpatient.html'" style="cursor: pointer;">➕ Add New Patient</li>
                <?php foreach ($patients as $patient): ?>
                    <li data-value="<?php echo $patient['patient_id']; ?>">
                        <?php echo htmlspecialchars($patient['patient_id'] . ' - ' . $patient['patient_name']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <input type="hidden" name="selected_patient_id" id="selected_patient_id">
            <button type="submit" class="start" name="start_meeting">Start Meeting</button>
        </form>
    </div>

    <script>
        const dropdown = document.querySelector('.custom-dropdown');
        const selected = dropdown.querySelector('.selected');
        const options = dropdown.querySelector('.options');
        const optionItems = options.querySelectorAll('li');
        const hiddenInput = document.getElementById('selected_patient_id');

        // Toggle dropdown
        selected.addEventListener('click', () => {
            options.style.display = options.style.display === 'block' ? 'none' : 'block';
        });

        // Set selected text & hidden input
        optionItems.forEach(item => {
            item.addEventListener('click', () => {
                const patientName = item.textContent;
                const patientId = item.getAttribute('data-value');

                if (patientId) {
                    selected.textContent = patientName;
                    selected.setAttribute('data-value', patientId);
                    hiddenInput.value = patientId;

                    // Store selected patient ID in localStorage for use in result.js
                    localStorage.setItem('selectedPatientId', patientId);

                    options.style.display = 'none';
                }
            });
        });

        // Close dropdown if clicked outside
        window.addEventListener('click', e => {
            if (!dropdown.contains(e.target)) {
                options.style.display = 'none';
            }
        });
    </script>
</body>
</html>
