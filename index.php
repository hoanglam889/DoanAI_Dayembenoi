<?php include('include/config.php'); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dạy Trẻ Biết Nói</title>
<link rel="manifest" href="manifest.json">
<link rel="stylesheet" href="css/style.css">
<meta name="theme-color" content="#4CAF50">
</head>
<body>
<style> /* ===== Slide menu ===== */ #slide-menu { height: 100%; width: 0; position: fixed; top: 0; left: 0; background-color: #fffae3; overflow-x: hidden; transition: 0.3s; padding-top: 60px; z-index: 1000; } #slide-menu a { padding: 10px 20px; text-decoration: none; font-size: 18px; color: #333; display: block; transition: 0.2s; } #slide-menu a:hover { background-color: #ffd966; } #slide-menu .close-btn { position: absolute; top: 10px; right: 20px; font-size: 30px; cursor: pointer; } #menu-btn { position: absolute; left: 10px; top: 10px; font-size: 24px; background: none; border: none; cursor: pointer; } </style>
<header>
  <button id="menu-btn">☰</button>
  Dạy Trẻ Biết Nói 📚
</header>

<!-- Menu -->
<div id="slide-menu">
  <span class="close-btn">&times;</span>
  <a href="#">Trang chủ</a>
  <a href="#">Thêm đồ vật</a>
  <a href="#" id="open-history-menu">Lịch sử</a>
  <a href="#">Cài đặt</a>
</div>

<!-- Danh sách đồ vật -->
<div class="container" id="items-container">
<?php
$stmt = $pdo->query("SELECT * FROM items");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    echo '<div class="card" data-id="'.$row['id'].'">';
    echo '<img src="uploads/'.$row['image_url'].'" alt="">';
    echo '</div>';
}
?>
</div>

<div class="footer">
  <button class="btn-camera">📷</button>
</div>

<!-- Modal Camera -->
<div id="camera-modal">
  <video id="video" autoplay playsinline></video>
  <div>
    <button id="capture-btn">Chụp</button>
    <button id="close-camera-btn">Đóng</button>
  </div>
</div>

<!-- Modal Lịch sử -->
<div id="history-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); justify-content:center; align-items:center; flex-direction:column; z-index:10001;">
  <div style="background:white; border-radius:15px; padding:20px; max-width:90%; max-height:80%; overflow:auto;">
    <h2>Lịch sử chụp</h2>
    <div id="history-list" style="display:grid; grid-template-columns:repeat(auto-fill,minmax(100px,1fr)); gap:10px;"></div>
    <button id="close-history" style="margin-top:10px; padding:8px 15px; border:none; border-radius:10px; background:#ff6f61; color:white; cursor:pointer;">Đóng</button>
  </div>
</div>

<canvas id="canvas" style="display:none;"></canvas>
<audio id="player"></audio>

<script src="script.js"></script>
</body>
</html>
