from flask import Flask, request, send_from_directory, jsonify
from flask_cors import CORS
from gtts import gTTS
from PIL import Image
import torch, torchvision.transforms as transforms
from torchvision import models
import json, uuid, os

UPLOAD_FOLDER = "temp_audio"
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

app = Flask(__name__)
CORS(app)

# ===== Model =====
model = models.resnet50(pretrained=True)
model.eval()

with open("imagenet_classes.txt","r") as f:
    labels = [line.strip() for line in f]

with open("labels_vi.json","r",encoding="utf-8") as f:
    label_map = json.load(f)

def predict_image(img_path):
    img = Image.open(img_path).convert("RGB")
    preprocess = transforms.Compose([
        transforms.Resize(256),
        transforms.CenterCrop(224),
        transforms.ToTensor(),
        transforms.Normalize([0.485,0.456,0.406],[0.229,0.224,0.225])
    ])
    img_tensor = preprocess(img).unsqueeze(0)
    with torch.no_grad():
        outputs = model(img_tensor)
    _, idx = outputs.max(1)
    label_en = labels[idx.item()]
    return label_map.get(label_en,"Động vật khác")

# ===== Speak text =====
@app.route("/speak")
def speak():
    text = request.args.get("text","")
    if not text: return "Thiếu text",400
    filename = f"{uuid.uuid4().hex}.mp3"
    filepath = os.path.join(UPLOAD_FOLDER, filename)
    gTTS(text, lang="vi").save(filepath)
    return jsonify({"url": f"/temp_audio/{filename}"})

# ===== Identify image =====
@app.route("/identify",methods=["POST"])
def identify():
    if "file" not in request.files: return "Thiếu file",400
    file = request.files["file"]
    temp_path = f"temp_{uuid.uuid4().hex}.jpg"
    file.save(temp_path)
    try:
        label_vi = predict_image(temp_path)
        tts_filename = f"{uuid.uuid4().hex}.mp3"
        tts_path = os.path.join(UPLOAD_FOLDER, tts_filename)
        gTTS(label_vi, lang="vi").save(tts_path)
    finally:
        if os.path.exists(temp_path): os.remove(temp_path)
    # Trả về link cho iOS/Android dễ fetch
    return jsonify({"url": f"/temp_audio/{tts_filename}", "text": label_vi})

# ===== Serve audio =====
@app.route("/temp_audio/<filename>")
def temp_audio(filename):
    return send_from_directory(UPLOAD_FOLDER, filename)

# ===== Ask AI =====
@app.route("/ask",methods=["POST"])
def ask():
    data = request.get_json()
    question = data.get("question","")
    return jsonify({"answer":f"Bạn vừa hỏi: {question}"})

if __name__=="__main__":
    app.run(host="0.0.0.0", port=5000)
