<?php
include('include/config.php');

if(isset($_FILES['image']) && isset($_FILES['audio'])){
    $image = $_FILES['image'];
    $audio = $_FILES['audio'];

    $image_name = time().'_'.$image['name'];
    $audio_name = time().'_'.$audio['name'];

    move_uploaded_file($image['tmp_name'], 'uploads/'.$image_name);
    move_uploaded_file($audio['tmp_name'], 'uploads/'.$audio_name);

    $stmt = $pdo->prepare("INSERT INTO history (image_url, audio_url) VALUES (?, ?)");
    $stmt->execute([$image_name, $audio_name]);

    echo json_encode(['status'=>'ok']);
} else {
    echo json_encode(['status'=>'fail']);
}
