<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not logged in
    header("Location: login.html");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "emocam_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch patients for this user
$sql = "SELECT patient_id, patient_name FROM patients WHERE user_id = ? ORDER BY patient_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$patients = [];
while ($row = $result->fetch_assoc()) {
    $patients[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>See Result</title>
    <link rel="stylesheet" href="seeResult.css" />
</head>
<body>

<header class="head">
    <h2 class="logo"><img src="foto/logo.jpg" alt="Logo"></h2>
</header>

<section class="back">
    <a href="Home.php"><img src="foto/arrow (1).png" alt="arrow"></a>
</section>

<section class="selection-text">
    <h1>Select Your Patient</h1>
    <h3>Whose Result Do You Want To See?</h3>
</section>

<div class="custom-dropdown">
    <div class="selected">Select Patient</div>
    <ul class="options">
        <?php foreach ($patients as $patient): ?>
            <li data-value="<?php echo htmlspecialchars($patient['patient_id']); ?>">
                <?php echo htmlspecialchars($patient['patient_id'] . ' - ' . $patient['patient_name']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<section class="submit">
    <form id="resultForm" method="POST" action="pickMeeting.php">
        <input type="hidden" name="patient_id" id="patient_id_input">
        <button type="submit" class="start">See Result</button>
    </form>
</section>

<script>
    window.onload = function () {
        const dropdown = document.querySelector('.custom-dropdown');
        const selected = dropdown.querySelector('.selected');
        const options = dropdown.querySelector('.options');

        // Toggle dropdown
        selected.addEventListener('click', () => {
            options.style.display = options.style.display === 'block' ? 'none' : 'block';
        });

        // Handle selection
        options.addEventListener('click', (event) => {
            if (event.target.tagName.toLowerCase() === 'li') {
                selected.textContent = event.target.textContent;
                selected.setAttribute('data-value', event.target.getAttribute('data-value'));
                options.style.display = 'none';
                localStorage.setItem('selectedPatientId', event.target.getAttribute('data-value'));
            }
        });

        // Close dropdown when clicking outside
        window.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target)) {
                options.style.display = 'none';
            }
        });

        // Submit form with selected patient ID
        const form = document.getElementById('resultForm');
        form.addEventListener('submit', (e) => {
            const selectedPatientId = selected.getAttribute('data-value');
            if (!selectedPatientId) {
                e.preventDefault();
                alert('Please select a patient first.');
            } else {
                document.getElementById('patient_id_input').value = selectedPatientId;
            }
        });
    };
</script>

</body>
</html>
