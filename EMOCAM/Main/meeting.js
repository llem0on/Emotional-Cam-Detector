const endConsultationBtn = document.getElementById("endConsultationBtn");
const confirmationPopup = document.getElementById("confirmationPopup");
const yesBtn = document.getElementById("yesBtn");
const noBtn = document.getElementById("noBtn");
const timeInfo = document.getElementById("timeInfo");
const video = document.getElementById('camera');

let startTime = new Date();
let endTime = null;

let emotionCounts = {
  Angry: 0,
  Disgust: 0,
  Fear: 0,
  Happy: 0,
  Neutral: 0,
  Sad: 0,
  Surprise: 0,
};

navigator.mediaDevices.getUserMedia({ video: true })
  .then(stream => {
    video.srcObject = stream;
    video.onloadedmetadata = () => {
      setInterval(captureAndSendFrame, 2500);
    };
  })
  .catch(err => {
    console.error("Error accessing webcam: ", err);
  });

console.log("Meeting started at:", startTime.toLocaleTimeString());
updateTimeDisplay();

endConsultationBtn.addEventListener("click", () => {
  confirmationPopup.style.display = "flex";
});

noBtn.addEventListener("click", () => {
  confirmationPopup.style.display = "none";
});

yesBtn.addEventListener("click", () => {
  endTime = new Date();
  updateTimeDisplay();

  const duration = Math.floor((endTime - startTime) / 60000);
  alert(`Meeting Duration: ${duration} min\nStart: ${startTime.toLocaleTimeString()}\nEnd: ${endTime.toLocaleTimeString()}`);

  const total = Object.values(emotionCounts).reduce((a, b) => a + b, 0) || 1;
  const emotionSummary = Object.entries(emotionCounts).map(([label, count]) => ({
    label,
    percentage: ((count / total) * 100).toFixed(2)
  }));

  localStorage.setItem("emotionSummary", JSON.stringify(emotionSummary));

  confirmationPopup.style.display = "none";
  window.location.href = "result.html";
});

function updateTimeDisplay() {
  timeInfo.innerHTML = `
    <strong>Start:</strong> ${startTime ? startTime.toLocaleTimeString() : "Not started"}<br>
    <strong>End:</strong> ${endTime ? endTime.toLocaleTimeString() : "Not ended"}
  `;
}

async function captureAndSendFrame() {
  if (video.readyState < 2) {
    console.warn("Video not ready yet");
    return;
  }

  const canvas = document.createElement("canvas");
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  const context = canvas.getContext("2d");
  context.drawImage(video, 0, 0, canvas.width, canvas.height);

  const imageData = canvas.toDataURL("image/jpeg");

  try {
    const response = await fetch("http://127.0.0.1:5000/process_frame", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ image: imageData })
    });

    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

    const result = await response.json();
    console.log("Detected Emotions:", result.emotions);

    for (const [emotion, count] of Object.entries(result.emotions)) {
      emotionCounts[emotion] = (emotionCounts[emotion] || 0) + Number(count);
    }
  } catch (error) {
    console.error("Error sending frame:", error);
  }
}