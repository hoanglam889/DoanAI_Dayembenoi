async function loadItems() {
  const container = document.querySelector(".container");
  container.innerHTML = "";

  try {
    const res = await fetch("get_items.php");
    const items = await res.json();

    if (!items.length) {
      container.innerHTML = "<p>Chưa có dữ liệu</p>";
      return;
    }

    items.forEach(item => {
      const card = document.createElement("div");
      card.className = "card";
      card.onclick = () => playSound(item.sound_url);

      card.innerHTML = `
        <img src="uploads/${item.image_url}" alt="${item.name}">
        <span>${item.name}</span>
      `;

      container.appendChild(card);
    });
  } catch (e) {
    container.innerHTML = `<p>Lỗi tải dữ liệu: ${e}</p>`;
  }
}

function playSound(file) {
  const audio = document.getElementById("player");
  audio.src = "uploads/" + file;
  audio.play();
}

window.onload = loadItems;
