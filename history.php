    <?php
include('include/config.php');
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT * FROM history ORDER BY id DESC");
$history = [];

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $history[] = [
        "image" => $row['image_url'],   // tên file ảnh
        "audio" => $row['audio_url']    // tên file audio
    ];
}

echo json_encode($history);