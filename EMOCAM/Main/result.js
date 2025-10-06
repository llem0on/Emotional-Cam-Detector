const emotionBarsContainer = document.getElementById("emotionBars");
const emotionSummary = JSON.parse(localStorage.getItem("emotionSummary")) || [];

console.log("Raw emotionSummary from localStorage:", emotionSummary);

const desiredOrder = ['angry', 'happy', 'disgust', 'sad', 'fear', 'surprise', 'neutral'];

// Create a map from emotion label (lowercase) to percentage (number)
const emotionMap = {};
emotionSummary.forEach(({ label, percentage }) => {
  if(label && percentage !== undefined) {
    emotionMap[label.toLowerCase()] = Number(percentage) || 0;
  } else {
    console.warn("Invalid emotionSummary entry:", { label, percentage });
  }
});

console.log("Emotion Map:", emotionMap);

// Build sorted array by desiredOrder, fallback 0 if missing
const sortedEmotionSummary = desiredOrder.map(label => ({
  label,
  percentage: emotionMap[label] !== undefined ? emotionMap[label] : 0
}));

console.log("Sorted emotionSummary:", sortedEmotionSummary);

if (sortedEmotionSummary.length === 0) {
  emotionBarsContainer.innerHTML = "<p>No emotion data available.</p>";
} else {
  let barsHTML = '';
  sortedEmotionSummary.forEach(({ label, percentage }) => {
    barsHTML += `
      <div class="emotion-bar">
        <label>${label.charAt(0).toUpperCase() + label.slice(1)}</label>
        <div class="bar-bg">
          <div class="bar fill-${label.toLowerCase()}" style="width: ${percentage}%; min-width: 5px;">
            ${percentage}%
          </div>
        </div>
      </div>
    `;
  });
  emotionBarsContainer.innerHTML = barsHTML;
}

// Save button event unchanged below...
document.getElementById("saveResultBtn").addEventListener("click", () => {
  const emotionSummary = JSON.parse(localStorage.getItem("emotionSummary")) || [];

  if (emotionSummary.length === 0) {
    alert("No data to save.");
    return;
  }

  const selectedPatientId = localStorage.getItem("selectedPatientId");
  if (!selectedPatientId) {
    alert("No patient selected. Please go back and choose a patient.");
    return;
  }

  const emotionData = {};
  emotionSummary.forEach(({ label, percentage }) => {
    emotionData[label] = percentage;
  });

  const payload = {
    patient_id: selectedPatientId,
    emotions: emotionData
  };

  console.log("Saving payload:", payload);

  fetch("result.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  })
  .then(res => res.text())
  .then(text => {
    console.log("Raw response text:", text);
    let data = {};
    try {
      data = JSON.parse(text);
    } catch (e) {
      console.error("JSON parse error:", e);
      alert("Server returned invalid response.");
      return;
    }
    if (data.status === "success") {
      alert("Results saved successfully!");
      window.location.href = "Home.php";
    } else {
      alert("Failed to save: " + (data.error || "Unknown error"));
    }
  })
   .catch(err => {
    console.error("Network error or fetch failed:", err);
    alert("Network error while saving.");
  });
});