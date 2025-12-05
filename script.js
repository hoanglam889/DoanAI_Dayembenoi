// ===== Menu slide =====
const menu = document.getElementById('slide-menu');
const btn = document.getElementById('menu-btn');
const closeBtn = document.querySelector('#slide-menu .close-btn');
const historyMenu = document.getElementById('open-history-menu');
const historyModal = document.getElementById('history-modal');
const historyList = document.getElementById('history-list');
const closeHistoryBtn = document.getElementById('close-history');

// Menu
btn.addEventListener('click', e => { menu.style.width='250px'; e.stopPropagation(); });
closeBtn.addEventListener('click', e => { menu.style.width='0'; e.stopPropagation(); });
window.addEventListener('click', e => { if(menu.style.width!=='0' && !menu.contains(e.target) && e.target!==btn) menu.style.width='0'; });
menu.addEventListener('touchstart', e => e.stopPropagation());
btn.addEventListener('touchstart', e => e.stopPropagation());
closeBtn.addEventListener('touchstart', e => e.stopPropagation());
const ip = 'http://192.168.1.150:5000';
// ===== Audio player =====
const player = document.getElementById('player');

// ===== Click vào card để nhận diện =====
document.querySelectorAll('.card').forEach(card=>{
  card.addEventListener('click', async ()=>{
    const img = card.querySelector('img'); 
    if(!img) return;
    try{
      const formData = new FormData();
      formData.append('file', await (await fetch(img.src)).blob(), 'image.jpg');

      const res = await fetch(`${ip}/identify`, {method:'POST', body: formData});
      if(!res.ok) throw new Error('Server lỗi');
      const data = await res.json();
      player.src = `${ip}` + data.url;
      await player.play();
    }catch(err){
      console.error(err);
      const utter = new SpeechSynthesisUtterance("Không nhận diện được, thử nói Xin chào!");
      utter.lang="vi-VN"; utter.rate=1; utter.pitch=1;
      speechSynthesis.speak(utter);
    }
  });
});

// ===== Camera =====
const cameraBtn = document.querySelector('.btn-camera');
const cameraModal = document.getElementById('camera-modal');
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const captureBtn = document.getElementById('capture-btn');
const closeCameraBtn = document.getElementById('close-camera-btn');

cameraBtn.addEventListener('click', async ()=>{
  historyModal.style.display='none';
  cameraModal.style.display='flex';
  try{
    const stream = await navigator.mediaDevices.getUserMedia({video:true});
    video.srcObject = stream;
  }catch(err){ alert('Không mở camera: '+err); }
});

closeCameraBtn.addEventListener('click', ()=>{
  cameraModal.style.display='none';
  if(video.srcObject){ video.srcObject.getTracks().forEach(track=>track.stop()); video.srcObject=null; }
});

captureBtn.addEventListener('click', async ()=>{
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  canvas.getContext('2d').drawImage(video,0,0,canvas.width,canvas.height);

  canvas.toBlob(async blob=>{
    try{
      // Gửi ảnh lên server
      const formData = new FormData();
      formData.append('file', blob, 'photo.jpg');

      const res = await fetch(`${ip}/identify`, {method:'POST', body: formData});
      if(!res.ok) throw new Error('Server AI lỗi');
      const data = await res.json();

      // Lưu lịch sử trên server
      const imgFile = new File([blob],'img_'+Date.now()+'.jpg',{type:'image/jpeg'});
      const audioFile = await (await fetch(`${ip}`+data.url)).blob();
      const audioFileObj = new File([audioFile],'audio_'+Date.now()+'.mp3',{type:'audio/mpeg'});
      const saveForm = new FormData();
      saveForm.append('image', imgFile);
      saveForm.append('audio', audioFileObj);

      const saveRes = await fetch('save_history.php',{method:'POST',body:saveForm});
      if(!saveRes.ok) throw new Error('Lưu lịch sử lỗi');

      // Play audio
      player.src = `${ip}` + data.url;
      await player.play();
    }catch(err){ console.error(err); alert('Lỗi: '+err); }
  },'image/jpeg');

  closeCameraBtn.click();
});

// ===== Lịch sử =====
historyMenu.addEventListener('click', async ()=>{
  cameraModal.style.display='none';
  menu.style.width='0';
  historyModal.style.display='flex';
  historyList.innerHTML='<p>Đang tải...</p>';
  try{
    const res = await fetch('history.php');
    const data = await res.json();
    historyList.innerHTML='';
    data.forEach(item=>{
      const div = document.createElement('div');
      div.style.textAlign='center';
      div.innerHTML=`
        <img src="uploads/${item.image}" style="width:100%; border-radius:8px;">
        <button style="margin-top:5px; padding:5px 10px;" onclick="playHistoryAudio('${item.audio}')">Nghe lại</button>
      `;
      historyList.appendChild(div);
    });
  }catch(err){ console.error(err); historyList.innerHTML='<p>Lỗi tải lịch sử</p>'; }
});

closeHistoryBtn.addEventListener('click', ()=>{ historyModal.style.display='none'; });

function playHistoryAudio(file){
  player.src='uploads/'+file+'?t='+Date.now();
  player.play();
}
