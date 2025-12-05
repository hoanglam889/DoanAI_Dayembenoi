<?php
include "db.php";

$name  = $_POST['name'];
$image = $_FILES['image']['name'];
$audio = $_FILES['audio']['name'];

move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$image);
move_uploaded_file($_FILES['audio']['tmp_name'], "uploads/".$audio);

$sql = "INSERT INTO items (name, image, audio) VALUES ('$name', '$image', '$audio')";
$conn->query($sql);

echo "Thêm thành công! <a href='admin.html'>Quay lại</a>";
?>