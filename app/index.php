<?php
// index.php
// Demo API: upload ảnh -> gửi Gemini OCR -> tạo file TTS -> trả về JSON

// ==== CONFIG ====
$UPLOAD_DIR = __DIR__ . "/uploads/";
$AUDIO_DIR  = __DIR__ . "/audio/";
$GEMINI_API_KEY = "YOUR_GEMINI_API_KEY"; // thay bằng API key của bạn
$GOOGLE_TTS_API_KEY = "YOUR_GOOGLE_CLOUD_TTS_API_KEY"; // key TTS

// Tạo thư mục nếu chưa có
if (!is_dir($UPLOAD_DIR)) mkdir($UPLOAD_DIR, 0777, true);
if (!is_dir($AUDIO_DIR)) mkdir($AUDIO_DIR, 0777, true);

header("Content-Type: application/json; charset=utf-8");

// ==== B1: Upload ảnh ====
if (!isset($_FILES['image'])) {
    echo json_encode(["error" => "Chưa có file ảnh"]);
    exit;
}

$imgName = time() . "_" . basename($_FILES["image"]["name"]);
$targetFile = $UPLOAD_DIR . $imgName;
move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);

// ==== B2: Gửi ảnh sang Gemini Vision ====
$base64Image = base64_encode(file_get_contents($targetFile));

$geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=$GEMINI_API_KEY";

$payload = [
  "contents" => [[
    "parts" => [
      ["text" => "Nhận diện chữ trong ảnh này và trả về chính xác, chỉ text thôi."],
      ["inline_data" => [
        "mime_type" => "image/jpeg",
        "data" => $base64Image
      ]]
    ]
  ]]
];

$ch = curl_init($geminiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);

$resData = json_decode($response, true);
if (!isset($resData["candidates"][0]["content"]["parts"][0]["text"])) {
    echo json_encode(["error" => "Gemini không trả về text", "raw" => $resData]);
    exit;
}
$textResult = $resData["candidates"][0]["content"]["parts"][0]["text"];

// ==== B3: Gọi Google TTS ====
$ttsUrl = "https://texttospeech.googleapis.com/v1/text:synthesize?key=$GOOGLE_TTS_API_KEY";
$ttsPayload = [
  "input" => ["text" => $textResult],
  "voice" => ["languageCode" => "vi-VN", "ssmlGender" => "FEMALE"],
  "audioConfig" => ["audioEncoding" => "MP3"]
];

$ch = curl_init($ttsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($ttsPayload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$ttsRes = curl_exec($ch);
curl_close($ch);

$ttsData = json_decode($ttsRes, true);
if (!isset($ttsData["audioContent"])) {
    echo json_encode(["error" => "TTS không trả về audio", "raw" => $ttsData]);
    exit;
}

$audioFile = $AUDIO_DIR . time() . ".mp3";
file_put_contents($audioFile, base64_decode($ttsData["audioContent"]));

// ==== B4: Trả về kết quả ====
echo json_encode([
    "text" => $textResult,
    "image_url" => "uploads/" . $imgName,
    "audio_url" => "audio/" . basename($audioFile)
], JSON_UNESCAPED_UNICODE);
