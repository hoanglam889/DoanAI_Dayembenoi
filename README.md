# 📘 DOAN_AI — Ứng dụng nhận diện hình ảnh + phát âm cho bé (PWA)

Dự án gồm 2 phần:

1. **Server AI (Python + Flask + ResNet50)** → Nhận diện ảnh + trả về tên tiếng Việt.
2. **Website PWA (PHP + MySQL)** →

   * Cho phép bé **click vào hình để nghe âm thanh**
   * Chụp ảnh → gửi server AI → nghe kết quả
   * Xem lại **lịch sử đã nhận diện**
   * Là PWA nên dùng tốt trên **điện thoại & tablet**

---

## 📂 Cấu trúc thư mục

```
DOAN_AI/
│
├── ai_api.py              # Server AI (Python Flask)
├── imagenet_classes.txt   # Nhãn ResNet50
├── labels_vi.json         # Nhãn tiếng Việt
│
├── index.php              # Trang chính PWA
├── admin.html             # Giao diện Admin
├── get_items.php
├── save_history.php
├── history.php
├── upload_items.php
│
├── app/                   # Ảnh cho bé click để phát âm
├── uploads/               # Ảnh chụp từ camera (tự tạo)
├── temp_audio/            # File âm thanh TTS
│
├── css/
├── script.js
├── service-worker.js
├── manifest.json
└── sw.js
```

---

## 🚀 1. Chạy server AI (Python)

### **Yêu cầu**

* Python 3.9+
* pip

### **Cài thư viện**

```bash
pip install flask flask-cors pillow torchvision torch gtts
```

### **Chạy server AI**

```bash
python ai_api.py
```

Sau khi chạy, Flask sẽ chạy ở port **5000**.

---

## 🔧 2. Cấu hình IP server AI

Trong **script.js**:

```javascript
let ip = "http://YOUR_IP:5000";
```

Nếu chạy local XAMPP:

```javascript
let ip = "http://127.0.0.1:5000";
```

Nếu chạy trên LAN để dùng bằng điện thoại:

```javascript
let ip = "http://192.168.x.x:5000";
```

> ⚠️ **Quan trọng:** Server PHP và server Python phải cùng mạng.

---

## 🗂 3. Kết nối database MySQL

Tạo database:

    - Tạo database trên http://localhost/phpmyadmin/
```
    - Tạo db tên tùy chọn và import file: app_baby.sql được đính kèm
    - Vào file config.php sửa đoạn "$db   = "app_baby"" thành tên database vừa tạo;
---

## 📱 4. Tính năng PWA

Dự án hỗ trợ:

* **Offline mode**
* **Add to home screen**
* Chạy như 1 app di động

Cần 3 file:

```
manifest.json
service-worker.js
sw.js
```

Trong `index.php` nhớ include:

```html
<link rel="manifest" href="manifest.json">
<script src="service-worker.js"></script>
```

---

## 🎨 5. Bộ ảnh cho bé click để phát âm

Ảnh nằm trong:

```
app/
```

Ví dụ:

* con mèo → meo.jpg
* quả táo → apple.png

Click ảnh sẽ tự phát âm bằng TTS Việt.

---

## 📸 6. Chức năng camera + nhận diện AI

* Nhấn nút 📷 → mở camera
* Chụp ảnh → gửi đến server AI Python
* Nhận về:

  * `text`: tên tiếng Việt
  * `audio_url`: âm đọc
  * `image_url`: ảnh đã lưu
* Lưu vào lịch sử DB

---

## 📁 7. Thư mục uploads/

Tất cả ảnh chụp từ camera tự động sinh vào:

```
uploads/
```

---

## 🕘 8. Xem lại lịch sử

Trang:

```
history.php
```

Hiển thị:

* Ảnh đã chụp
* Kết quả nhận diện
* Nút nghe lại âm thanh

---

## 🌐 9. Deploy

### 🅿️ PHP hosting (InfinityFree)

* Upload toàn bộ folder **trừ server Python**
* Hoạt động bình thường, nhưng AI Python phải chạy nơi khác

### 🐍 Python AI Server (Render - FREE)

* Tạo web service
* Upload file `ai_api.py`, `labels_vi.json`, `imagenet_classes.txt`
* Lấy URL → dán vào:

```javascript
let ip = "https://your-render-url.onrender.com";
```

---

## 🙌 Tác giả

Phan Huỳnh Hoàng Lâm
Sinh viên thực hiện đồ án AI nhận diện hình ảnh cho trẻ em.
