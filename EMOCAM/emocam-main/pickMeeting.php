<?php
session_start();

if (!isset($_SESSION['user_id'])) {
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
$patient_id = $_POST['patient_id'] ?? null;

if (!$patient_id) {
    echo "No patient selected.";
    exit();
}

// Fetch patient name
$stmt = $conn->prepare("SELECT patient_name FROM patients WHERE patient_id = ? AND user_id = ?");
$stmt->bind_param("si", $patient_id, $user_id);
$stmt->execute();
$stmt->bind_result($patient_name);
$stmt->fetch();
$stmt->close();

// Fetch meetings for the selected patient
$sql = "SELECT meeting_id, meeting_date FROM meetings WHERE patient_id = ? ORDER BY meeting_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$meetings = [];
while ($row = $result->fetch_assoc()) {
    $meetings[] = $row;
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
    <link rel="stylesheet" href="pickMeeting.css" />
</head>
<body>

<header class="head">
    <h2 class="logo"><img src="foto/logo.jpg" alt="Logo"></h2>
</header>

<section class="back">
    <a href="Home.php"><img src="foto/arrow (1).png" alt="arrow"></a>
</section>

<section class="selection-text">
    <h1>Select Meeting</h1>
    <h3>Which <?php echo htmlspecialchars($patient_name); ?>'s Meeting Data Do You Want To See?</h3>
</section>

<div class="custom-dropdown">
    <div class="selected">Select Meeting</div>
    <ul class="options">
        <?php foreach ($meetings as $meeting): ?>
            <?php
                $datetime = new DateTime($meeting['meeting_date']);
                $dateFormatted = $datetime->format('Y-M-d');
                $monthFormatted = $datetime->format('M');
                $yearFormatted = $datetime->format('Y');
                $dayFormatted = $datetime->format('d');
                $timeFormatted = $datetime->format('H:i:s');
            ?>
            <li data-value="<?php echo htmlspecialchars($meeting['meeting_id']); ?>">
                <?php echo htmlspecialchars("{$meeting['meeting_id']} - {$dayFormatted}-{$monthFormatted}-{$yearFormatted} - End Time at {$timeFormatted}"); ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
    const dropdown = document.querySelector('.custom-dropdown');
    const selected = dropdown.querySelector('.selected');
    const options = dropdown.querySelector('.options');

    selected.addEventListener('click', () => {
        options.style.display = options.style.display === 'block' ? 'none' : 'block';
    });

    options.addEventListener('click', (event) => {
        if(event.target.tagName.toLowerCase() === 'li') {
            selected.textContent = event.target.textContent;
            selected.setAttribute('data-value', event.target.getAttribute('data-value'));
            options.style.display = 'none';
            localStorage.setItem('selectedMeetingId', event.target.getAttribute('data-value'));
        }
    });

    window.addEventListener('click', e => {
        if (!dropdown.contains(e.target)) {
            options.style.display = 'none';
        }
    });
</script>

<section class="submit">
    <form id="viewResultForm" method="POST" action="viewResult.php">
        <input type="hidden" name="meeting_id" id="meeting_id_input">
        <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($patient_id); ?>">
        <button type="submit" class="start">View Meeting Data</button>
    </form>
</section>

<script>
    const form = document.getElementById('viewResultForm');
    form.addEventListener('submit', (e) => {
        const selectedMeetingId = document.querySelector('.selected').getAttribute('data-value');
        if (!selectedMeetingId) {
            e.preventDefault();
            alert('Please select a meeting first.');
        } else {
            document.getElementById('meeting_id_input').value = selectedMeetingId;
        }
    });
</script>

</body>
</html>