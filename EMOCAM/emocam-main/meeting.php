<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EMO Consultation</title>
  <link rel="stylesheet" href="meeting.css" />
</head>
<body>
  <div class="container">
    <header class="logo">
      <img class="emo" src="foto/logo.jpg" alt="EMO Logo" />
      <span class="username">Jennie</span>
    </header>

    <div class="camera-box">
      <video id="camera" width="640" height="480" autoplay muted playsinline></video>

      <!-- Popup end confirmation -->
      <div id="confirmationPopup" class="popup" style="display:none;">
        <div class="popup-box">
          <p><strong>Are You Sure You want to End The Consultation?</strong></p>
          <div style="margin-top: 20px;">
            <button id="yesBtn" class="btn green">Yes</button>
            <button id="noBtn" class="btn red">No</button>
          </div>
        </div>
      </div>
    </div>

    <div id="timeInfo" style="margin-top: 20px; font-size: 16px; text-align: center;"></div>

    <div class="buttons">
      <button id="endConsultationBtn" class="btn red">End The Consultation</button>
    </div>
  </div>

  <script src="meeting.js"></script>
</body>
</html>