<?php
include 'include/config.php';

header('Content-Type: application/json; charset=utf-8');
// Truy vấn dữ liệu
$sql = "SELECT * FROM items";
$stmt = $pdo->query($sql);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Trả về JSON
header('Content-Type: application/json');
echo json_encode($items);
?>
