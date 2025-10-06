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

$patient_id = $_POST['patient_id'] ?? null;
$meeting_id = $_POST['meeting_id'] ?? null;

if (!$patient_id || !$meeting_id) {
    echo "Invalid access.";
    exit();
}

// Get patient and meeting data
$sql = "SELECT p.patient_name, m.meeting_date,
    MAX(CASE WHEN me.emotion = 'angry' THEN me.value ELSE NULL END) AS angry,
    MAX(CASE WHEN me.emotion = 'happy' THEN me.value ELSE NULL END) AS happy,
    MAX(CASE WHEN me.emotion = 'disgust' THEN me.value ELSE NULL END) AS disgust,
    MAX(CASE WHEN me.emotion = 'sad' THEN me.value ELSE NULL END) AS sad,
    MAX(CASE WHEN me.emotion = 'fear' THEN me.value ELSE NULL END) AS fear,
    MAX(CASE WHEN me.emotion = 'surprised' THEN me.value ELSE NULL END) AS surprised,
    MAX(CASE WHEN me.emotion = 'neutral' THEN me.value ELSE NULL END) AS neutral
    FROM patients p
    JOIN meetings m ON p.patient_id = m.patient_id
    JOIN meeting_emotions me ON m.meeting_id = me.meeting_id
    WHERE p.patient_id = ? AND m.meeting_id = ?
    GROUP BY p.patient_name, m.meeting_date";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $patient_id, $meeting_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$data) {
    echo "No data found.";
    exit();
}

// Format date
$date = new DateTime($data['meeting_date']);
$formattedDate = $date->format('d/m/Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Meeting Result</title>
  <link rel="stylesheet" href="viewResult.css" />
</head>
<body>
  <header class="top-bar">
    <img src="foto/logo.jpg" alt="EMO Logo" class="logo" />
    <span class="username"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
  </header>

  <main class="clas">
    <div class="back">
      <a href="Home.php"><img src="foto/arrow (1).png" alt="arrow"></a>
    </div>
    <div class="content">
      <div class="user-info">
        <p><strong>ID</strong>: <?php echo htmlspecialchars($patient_id); ?></p>
        <p><strong>Name</strong>: <?php echo htmlspecialchars($data['patient_name']); ?></p>
        <p><strong>Consultation Date</strong>: <?php echo $formattedDate; ?></p>
      </div>

      <?php
      $emotions = ['angry', 'happy', 'disgust', 'sad', 'fear', 'surprised', 'neutral'];
      foreach ($emotions as $emotion):
          $value = number_format((float)$data[$emotion], 2);
      ?>
          <div class="emotion-bar">
            <label><?php echo ucfirst($emotion); ?></label>
            <div class="bar-bg">
              <div class="bar fill-<?php echo $emotion; ?>" style="width: <?php echo $value; ?>%;">
                <?php echo $value; ?>%
              </div>
            </div>
          </div>
      <?php endforeach; ?>

      <button class="btn purple" onclick="showPopup()">Delete Session</button>

      <div class="popup-overlay" id="popup">
        <div class="popup-box">
          <p><strong>Are You Sure You want to Delete This Session?</strong></p>
          <div class="popup-buttons">
            <button class="btn green" onclick="confirmDelete()">Yes</button>
            <button class="btn red" onclick="hidePopup()">No</button>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    function showPopup() {
      document.getElementById('popup').style.display = 'flex';
    }
    function hidePopup() {
      document.getElementById('popup').style.display = 'none';
    }
    function confirmDelete() {
      fetch('deleteSession.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          meeting_id: '<?php echo htmlspecialchars($meeting_id); ?>'
        })
      })
      .then(response => {
        if (!response.ok) throw new Error("Delete failed");
        return response.text();
      })
      .then(data => {
        alert("Session deleted!");
        window.location.href = "Home.php"; // Redirect after delete
      })
      .catch(error => {
        alert("Error deleting session: " + error.message);
      });

      hidePopup();
    }
  </script>
</body>
</html>
