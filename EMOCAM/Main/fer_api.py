from flask import Flask, render_template, Response, jsonify, request
import cv2
import numpy as np
from tensorflow.keras.models import load_model #type: ignore
from PIL import Image
from io import BytesIO
import base64
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

model = load_model('model/model_final.h5')
face_cascade = cv2.CascadeClassifier('model/haarcascade_frontalface_default.xml')

labels_dict = {0: 'Angry', 1: 'Disgust', 2: 'Fear', 3: 'Happy', 4: 'Neutral', 5: 'Sad', 6: 'Surprise'}

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/process_frame', methods=['POST'])
def process_frame():
    data = request.get_json()
    if 'image' not in data:
        return jsonify({'error': 'No image provided'}), 400

    image_data = data['image'].split(',')[1]  # remove "data:image/jpeg;base64,"
    image = Image.open(BytesIO(base64.b64decode(image_data)))
    image = image.convert('L')  # grayscale
    image_np = np.array(image)

    faces = face_cascade.detectMultiScale(image_np, 1.3, 5)

    response = {}
    for (x, y, w, h) in faces:
        face_img = image_np[y:y+h, x:x+w]
        resized = cv2.resize(face_img, (48, 48))
        normalized = resized / 255.0
        reshaped = np.reshape(normalized, (1, 48, 48, 1))

        result = model.predict(reshaped, verbose=0)
        label = np.argmax(result, axis=1)[0]
        emotion = labels_dict[label]

        response[emotion] = response.get(emotion, 0) + 1

    return jsonify({'emotions': response})

if __name__ == '__main__':
    app.run(debug=True)
